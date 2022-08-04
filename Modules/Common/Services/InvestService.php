<?php

namespace Modules\Common\Services;

use \Exception;
use \Throwable;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Afranga;
use Modules\Common\Entities\ContractTemplate;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\InvestorQualityRange;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\LoanAmountAvailable;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Events\LoanAmountAvailableEvents;
use Modules\Common\Libraries\Calculator\InstallmentCalculator as instCalc;
use Modules\Common\Libraries\Calculator\InvestmentCalculator as invCalc;
use Modules\Core\Services\StorageService;

class InvestService extends CommonService
{
    protected $investorService = null;
    protected $portfolioService = null;
    protected $pdfService = null;
    private $investmentBunchService = null;

    /**
     * Single invest action - buying part of loan priciple
     *
     * Flow:
     * - create investment
     * - create investor installments
     * - update wallet
     * - update portflio
     *
     * @param int $investorId
     * @param int $loanId
     * @param float $amount
     * @param Carbon|null $buyDate
     * @param int|null $investmentBunchId
     *
     * @return bool
     */
    public function invest(
        int $investorId,
        int $loanId,
        float $amount,
        Carbon $buyDate = null,
        int $investmentBunchId = null
    ): bool {
        // define main objects and check that amount is available in wallet and in loan
        try {
            $loan = $this->getLoan($loanId);
            if (!$loan->exists()) {
                throw new Exception("Not existing loan, #" . $loanId);
            }

            // if loan is processing by someonelse, we cannot invest
            if ($loan->isBlocked()) {
                throw new Exception("Loan is already blocked, #" . $loanId);
            }

            // check for available free amount on loan
            if (!$loan->isAvailableAmount($amount)) {
                throw new Exception("Loan has no amount of " . $amount . ", #" . $loanId);
            }

            $investor = $this->getInvestorService()->getById($investorId);
            $wallet = $investor->wallet($loan->currency_id);

            // check if investor has enough money in wallet
            if (!$wallet->hasUninvestedAmount($amount)) {
                throw new Exception(
                    "Investor has no money(" . $amount . "), wallet #" . $wallet->wallet_id . ", #" . $loanId
                );
            }

            $installments = $loan->installments();
        } catch (Throwable $e) {
            Log::channel('invest_service')->error(
                'Failed to define main objects. ' . $e->getMessage()
            );
            return false;
        }

        if (empty($buyDate)) {
            $buyDate = Carbon::now();
        }

        return $this->doInvest(
            $amount,
            $investor,
            $wallet,
            $loan,
            $installments,
            $buyDate,
            $investmentBunchId
        );
    }

    /**
     * @param float $amount
     * @param Investor $investor
     * @param Wallet $wallet
     * @param Loan $loan
     * @param array $installments
     * @param Carbon $buyDate
     * @param int|null $investmentBunchId
     * @return bool
     * @throws Throwable
     */
    public function doInvest(
        float $amount,
        Investor $investor,
        Wallet $wallet,
        Loan $loan,
        array $installments,
        Carbon $buyDate,
        int $investmentBunchId = null
    ): bool {
        if (empty($installments)) {
            return false;
        }

        // IMPORTANT!
        // we set blocked flag on loan, so other processes
        // could not proceed any changes on it in the same time
        // $loan->block();


        DB::beginTransaction();

        $result = true;
        try {
            // get important objects, but with blocked status for other updates
            $loan = $this->getLockedLoan($loan->loan_id);
            $wallet = $this->getLockedWallet($wallet->wallet_id);

            // check for available free amount on loan
            if (!$loan->isAvailableAmount($amount)) {
                throw new Exception(
                    "Loan #" . $loan->loan_id . " has no available amount (" . $amount . "/" . $loan->amount_available . ")"
                );
            }

            if (!$wallet->hasUninvestedAmount($amount)) {
                throw new Exception(
                    "Investor has no money(" . $amount . "), wallet #" . $wallet->wallet_id . ", #" . $loan->loan_id
                );
            }

            // create investment
            $investment = $this->createInvestment(
                $investor,
                $loan,
                $amount,
                $buyDate,
                $investmentBunchId
            );
            if (!$investment) {
                throw new Exception("Failed to create investment");
            }

            // update loan
            $soldAmount = $loan->sellAvailableAmount($amount);
            if (!$soldAmount) {
                throw new Exception("Failed to update loan available amount");
            }

            // prepare investor installments
            $import = $this->prepareInvestorInstallments(
                $loan,
                $investment,
                $installments,
                $buyDate
            );
            if (empty($import)) {
                throw new Exception("Failed to prepare investor installments");
            }

            // add investor installments
            $invInsts = $this->createInvestorInstallments($import);
            if (!$invInsts) {
                throw new Exception("Failed to create investor installments");
            }


            // Info: updates on portflio is done in InvestmentObserver

            // upate Investment Bunch count
            if (!empty($investmentBunchId)) {
                $this->getInvestmentBunchService()->investBunchAndStrategyUpdate(
                    $investmentBunchId,
                    $amount
                );
            }

            // create transaction
            $transaction = $this->getTransactionService()->investment(
                $amount,
                $loan->loan_id,
                $investor->investor_id,
                $wallet->wallet_id,
                $wallet->currency_id,
                $investor->getMainBankAccountId(),
                $investment->investment_id

            );
            if (empty($transaction->transaction_id)) {
                throw new Exception("Failed to create transaction");
            }

            // upate wallet
            $invested = $wallet->invest($amount);
            if (!$invested) {
                throw new Exception("Failed to update wallet");
            }

            // Generate assignment agreement
            $this->getPdfService()->generateAssignmentAgreement($investment, $transaction);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();

            // $loan->unblock();

            Log::channel('invest_service')->error(
                'Error! '
                . 'loan #' . $loan->loan_id . ', '
                . 'investor #' . $investor->investor_id . ', '
                . 'amount = ' . $amount . ', '
                . 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );

            $result = false;
        }


        // IMPORTANT!
        // when we are ready with loan selling
        // we need to unblock loan for other operations
        // $loan->unblock();


        return $result;
    }

    /**
     * @param int $walletId
     * @return Wallet
     */
    public function getLockedWallet(int $walletId): Wallet
    {
        return Wallet::where('wallet_id', $walletId)->lockForUpdate()->first();
    }

    /**
     * @param int $loanId
     * @return Loan
     */
    public function getLockedLoan(int $loanId): Loan
    {
        return Loan::where('loan_id', $loanId)->lockForUpdate()->first();
    }

    /**
     * Create investment
     *
     * @param Investor $investor
     * @param Loan $loan
     * @param float $amount
     * @param Carbon|null $buyDate
     * @param int|null $investmentBunchId
     *
     * @return Investment
     */
    public function createInvestment(
        Investor $investor,
        Loan $loan,
        float $amount,
        Carbon $buyDate = null,
        int $investmentBunchId = null
    ): Investment {
        $obj = new Investment();
        if (!empty($investmentBunchId)) {
            $obj->investment_bunch_id = $investmentBunchId;
        }

        $percentDetails = $this->getInvestPercentAndDetails($amount, $loan);

        $obj->investor_id = $investor->investor_id;
        $obj->wallet_id = $investor->wallet()->wallet_id;
        $obj->loan_id = $loan->loan_id;
        $obj->amount = $amount;
        $obj->percent = $percentDetails['percent'];
        $obj->details = $percentDetails['details'];

        if (null !== $buyDate) {
            $obj->created_at = $buyDate->format('Y-m-d H:i:s');
        }
        $obj->save();

        event(new LoanAmountAvailableEvents($loan, $obj));

        return $obj;
    }

    /**
     * @param float $amount
     * @param Loan $loan
     * @return array
     */
    public function getInvestPercentAndDetails(float $amount, Loan $loan): array
    {
        $isLast = (($loan->amount_available - $amount) === 0.00 || $loan->amount_available === 0.00);

        return invCalc::getInvestedPercentDetails(
            $amount,
            $loan->remaining_principal,
            $loan->getInvestmentsPercent(),
            $isLast,
            12
        );
    }

    /**
     * [createInvestorInstallments description]
     *
     * @param Investment $investment
     * @param array $installments
     *
     * @return bool
     */
    public function createInvestorInstallments(array $import): bool
    {
        // multipple insert
        if (!empty($import)) {
            return InvestorInstallment::insert($import);
        }

        return false;
    }

    public function addInvestments(array $data): bool
    {
        // multipple insert
        if (!empty($data)) {
            return Investment::insert($data);
        }

        return false;
    }

    public function addInvestmentsSecondaryMarket(array $data): int
    {
        // single insert
        if (!empty($data)) {
            return Investment::insertGetId($data, 'investment_id');
        }

        return 0;
    }

    public function addTransactions(array $data): bool
    {
        // multipple insert
        if (!empty($data)) {
            return Transaction::insert($data);
        }

        return false;
    }

    public function addInvestorQualityRanges(array $data): bool
    {
        // multipple insert
        if (!empty($data)) {
            return InvestorQualityRange::insert($data);
        }

        return false;
    }

    public function addLoanAmountAvailableStats(array $data): bool
    {
        // multipple insert
        if (!empty($data)) {
            return LoanAmountAvailable::insert($data);
        }

        return false;
    }

    //////////////////////////////////////////////////////////////

    public function prepareInvestorInstallments(
        Loan $loan,
        Investment $investment,
        array $installments,
        Carbon $buyDate
    ): array {
        $import = [];
        $prevDueDate = null; // used for cacl difference between installments
        $prevInstallmentPaid = false; // by default not paid
        $remainingPricipal = $investment->amount;

        foreach ($installments as $key => $installment) {
            $dueDate = Carbon::parse($installment->due_date);

            // if installment is paid, we skip it
            if (1 == $installment->paid) {
                $prevInstallmentPaid = true;
                $prevDueDate = $dueDate;
                continue;
            }

            // get installment
            $instRow = $this->prepareInvestorInstallment(
                $loan,
                $investment,
                $installment,
                $remainingPricipal,
                $buyDate,
                $prevDueDate,
                $prevInstallmentPaid
            );
            $import[] = $instRow;

            // update counters
            $prevDueDate = $dueDate;
            $prevInstallmentPaid = false;
            $remainingPricipal = ($remainingPricipal - $instRow['principal']);
        }

        // check for lost cents in principal
        if (!empty($import)) {
            $key = array_key_last($import);
            if ($import[$key]['remaining_principal'] != $import[$key]['principal']) {
                $import[$key]['principal'] = $import[$key]['remaining_principal'];
                $import[$key]['total'] = $import[$key]['principal'] + $import[$key]['interest'];
            }
        }

        return $import;
    }

    private function prepareInvestorInstallment(
        Loan $loan,
        Investment $investment,
        Installment $installment,
        float $remainingPricipal,
        Carbon $buyDate,
        Carbon $prevDueDate = null,
        bool $previousInstallmentPaid = false
    ): array {
        try {
            $inst = [];
            $inst['loan_id'] = $investment->loan_id;
            $inst['investor_id'] = $investment->investor_id;
            $inst['investment_id'] = $investment->investment_id;
            $inst['interest_percent'] = $investment->percent;
            $inst['installment_id'] = $installment->installment_id;
            $inst['currency_id'] = $installment->currency_id;
            $inst['created_at'] = $investment->created_at;
            // $inst['installment_remaining_principal'] = $installment->remaining_principal;

            /*
            'remaining_principal',
            'principal',
            'accrued_interest',
            'interest',
            'late_interest',
            'total',
            'days'
            */
            $instSums = instCalc::calcInvestorInstallmentAmounts(
                $remainingPricipal,
                $installment->principal,
                $loan->interest_rate_percent,
                $investment->percent,
                $buyDate,
                Carbon::parse($installment->due_date),
                $prevDueDate,
                $previousInstallmentPaid
            );

            return ($inst + $instSums);
        } catch (Throwable $e) {
            Log::channel('invest_service')->error(
                'Amounts calculations failed. Msg: ' . $e->getMessage() . ', ' . "\n" . 'params: ' . "\n"
                . 'remaining_principal = ' . $installment->remaining_principal . "\n"
                . ', principal = ' . $installment->principal . "\n"
                . ', interest = ' . $installment->interest . "\n"
                . ', investor % = ' . $investorPercent . "\n"
                . ', listing date = ' . $loanListingDate->format('Y-m-d') . "\n"
                . ', buy date = ' . $buyDate->format('Y-m-d') . "\n"
                . ', i.due date = ' . $installment->due_date . "\n"
                . ', prev date = ' . (null !== $prevDueDate ? $prevDueDate->format('Y-m-d') : 'null') . "\n"
            );

            throw new Exception("Installment calculations failed");
        }

        return [];
    }

    //////////////////////////////////////////////////////////////

    private function getLoan(int $loanId)
    {
        return Loan::where('loan_id', '=', $loanId)->first();
    }

    private function getInvestmentBunchService()
    {
        if (null === $this->investmentBunchService) {
            $this->investmentBunchService = \App::make(InvestmentBunchService::class);
        }

        return $this->investmentBunchService;
    }

    /**
     * @param Loan $loan
     * @param Investor $investor
     * @param $assignmentAgreement
     *
     * @return mixed
     */
    public function generateAgreementNoInvestment(Loan $loan, Investor $investor, $assignmentAgreement)
    {
        $vars = [
            'ContractTemplate' => $assignmentAgreement->toArray(),
            'Loan' => $loan->toArray(),
            'Investor' => $investor->toArray(),
            'Transaction' => [
                'transaction_id' => ''
            ],
            'Investment' => [
                'amount' => ''
            ],
            'Originator' => $loan->originator->toArray(),
            'Buyback' => $loan->buyback ? 'Yes' : 'No',
            'Afranga' => [
                'pin' => Afranga::PIN,
            ],
            'LoanContract' => [
                'created_at' => Carbon::now()->toDateString()
            ],
            'Agreement' => [
                'language' => ContractTemplate::AGREEMENT_LANGUAGE,
            ],
        ];
        $vars['Transaction']['created_at'] = Carbon::now()->format('dmY');
        $vars['Originator']['name'] = Str::upper($vars['Originator']['name']);

        return App::make(PDFCreatorService::class)->generateAgreement(
            $assignmentAgreement->text,
            $vars,
            sprintf(
                StorageService::ASSIGNMENT_AGREEMENT_DIR,
                $loan->loan_id,
                Carbon::now()->format('Y-m-d_H:i:s')
            )
        );
    }

    public function getInvestmentsNotLinkedToTransactions(int $investorId)
    {
        $result = DB::select(
            DB::raw(
                "
            SELECT i.*
            FROM investment i
            JOIN transaction t ON (
                t.key = i.key
                and t.investment_id IS NULL
                and t.type IN (
                    '" . Transaction::TYPE_INVESTMENT . "',
                    '" . Transaction::TYPE_SECONDARY_MARKET_SELL . "',
                    '" . Transaction::TYPE_SECONDARY_MARKET_BUY . "'
                )
                and t.investor_id = " . $investorId . "
            )
            WHERE i.investor_id = " . $investorId . "
        "
            )
        );

        if (empty($result)) {
            return [];
        }

        return Investment::hydrate($result);
    }

    public function getLostInvestmentsVsTransactions(
        int $investorId = null,
        int $intervalMin = null,
        int $limit = 0
    ): array {
        $where = ['1 = 1'];
        if ($investorId) {
            $where[] = 'i.investor_id = ' . $investorId;
        }
        if ($intervalMin) {
            $where[] = 'i.created_at::DATE = NOW()::TIMESTAMP::DATE'
                . " AND NOW() > i.created_at + (" . $intervalMin . " ||' minutes')::interval";
        }

        $result = DB::select(
            DB::raw(
                "
            SELECT i.*
            FROM investment i
            JOIN loan l on i.loan_id = l.loan_id AND l.unlisted = 0
            JOIN transaction t ON (
                t.key = i.key
                and t.investment_id IS NULL
                and t.type = '" . Transaction::TYPE_INVESTMENT . "'
            )
            WHERE " . implode(' AND ', $where) . "
            " . ($limit > 0 ? "LIMIT " . $limit : "")
            )
        );

        if (empty($result)) {
            return [];
        }

        return Investment::hydrate($result)->all();
    }

    public function getLostInvestmentsVsLoanAmountStats(
        int $investorId = null,
        int $intervalMin = null,
        int $limit = 0
    ): array {
        $where = ['1 = 1'];
        if ($investorId) {
            $where[] = 'i.investor_id = ' . $investorId;
        }
        if ($intervalMin) {
            $where[] = 'i.created_at::DATE = NOW()::TIMESTAMP::DATE'
                . " AND NOW() > i.created_at + (" . $intervalMin . " ||' minutes')::interval";
        }

        $result = DB::select(
            DB::raw(
                "
            SELECT i.*
            FROM investment i
            JOIN loan l on i.loan_id = l.loan_id AND l.unlisted = 0
            JOIN loan_amount_available t ON (
                t.key = i.key
                and t.investment_id IS NULL
                and t.type = '" . Transaction::TYPE_INVESTMENT . "'
            )
            WHERE " . implode(' AND ', $where) . "
            " . ($limit > 0 ? "LIMIT " . $limit : "")
            )
        );

        if (empty($result)) {
            return [];
        }

        return Investment::hydrate($result)->all();
    }

    public function getLostInvestmentsVsLoanContracts(
        int $investorId = null,
        int $intervalMin = null,
        int $limit = 0
    ): array {
        $where = ['1 = 1'];
        if ($investorId) {
            $where[] = 'i.investor_id = ' . $investorId;
        }
        if ($intervalMin) {
            $where[] = 'i.created_at::DATE = NOW()::TIMESTAMP::DATE'
                . " AND NOW() > i.created_at + (" . $intervalMin . " ||' minutes')::interval";
        }
        $where[] = 't.loan_contract_id IS NULL';

        $result = DB::select(
            DB::raw(
                "
            SELECT i.*
            FROM investment i
            JOIN loan l on i.loan_id = l.loan_id AND l.unlisted = 0
            LEFT JOIN loan_contract t ON t.investment_id = i.investment_id
            WHERE " . implode(' AND ', $where) . "
            " . ($limit > 0 ? "LIMIT " . $limit : "")
            )
        );

        if (empty($result)) {
            return [];
        }

        return Investment::hydrate($result)->all();
    }

    public function getLostInvestmentsVsInvestorPlans(
        int $investorId = null,
        int $intervalMin = null,
        int $limit = 0
    ): array {
        $where = ['1 = 1'];
        if ($investorId) {
            $where[] = 'i.investor_id = ' . $investorId;
        }
        if ($intervalMin) {
            $where[] = 'i.created_at::DATE = NOW()::TIMESTAMP::DATE'
                . " AND NOW() > i.created_at + (" . $intervalMin . " ||' minutes')::interval";
        } else {
            $where[] = "i.created_at > (NOW() - INTERVAL '36 hours' )";
        }

        $where[] = 'ii.investor_installment_id IS NULL';

        $result = DB::select(
            DB::raw(
                "
            SELECT i.*
            FROM investment i
            JOIN loan l on i.loan_id = l.loan_id AND l.unlisted = 0
            LEFT JOIN investor_installment ii ON ii.investment_id = i.investment_id
            WHERE " . implode(' AND ', $where) . "
            " . ($limit > 0 ? "LIMIT " . $limit : "")
            )
        );

        if (empty($result)) {
            return [];
        }

        return Investment::hydrate($result)->all();
    }

    public function updateLoansAmountWithoutRelations(int $investorId): int
    {
        return DB::affectingStatement(
            "
            UPDATE loan_amount_available
            SET investment_id = i.investment_id
            FROM investment i
            JOIN loan_amount_available t ON (
                t.key = i.key
                AND t.investment_id is null
                AND t.type = :type
            )
            WHERE
                loan_amount_available.investment_id is null
                AND loan_amount_available.key = i.key
                AND i.investor_id = :investor_id;
            ",
            [
                'investor_id' => $investorId,
                'type' => Transaction::TYPE_INVESTMENT,
            ]
        );
    }

    public function updateTransactionsWithoutRelations(int $investorId): int
    {
        return DB::affectingStatement(
            "
            UPDATE transaction
            SET investment_id = i.investment_id
            FROM investment i
            WHERE
                transaction.key = i.key
                AND transaction.type = :type
                AND transaction.investment_id is null
                AND transaction.investor_id = :investor_id;
            ",
            [
                'type' => Transaction::TYPE_INVESTMENT,
                'investor_id' => $investorId,
            ]
        );
    }

    public function hasPlanForInvestment(int $investmentId): bool
    {
        $installmentsCount = InvestorInstallment::where(
            [
                'investment_id' => $investmentId,
            ]
        )->count();

        return ($installmentsCount > 0);
    }

    protected function getPdfService(): PDFCreatorService
    {
        if (null === $this->pdfService) {
            $this->pdfService = \App::make(PDFCreatorService::class);
        }

        return $this->pdfService;
    }
}
