<?php

namespace Modules\Common\Services;

use \Throwable;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\Transaction;
use Modules\Common\Repositories\TransactionRepository;
use Modules\Core\Services\BaseService;
use Modules\Core\Services\StorageService;

class TransactionService extends BaseService
{
    protected TransactionRepository $transactionRepository;
    protected StorageService $storageService;
    protected DocumentParser $documentParser;

    /**
     * TransactionService constructor.
     *
     * @param TransactionRepository $transactionRepository
     * @param StorageService $storageService
     * @param DocumentParser $documentParser
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        StorageService $storageService,
        DocumentParser $documentParser
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->storageService = $storageService;
        $this->documentParser = $documentParser;

        parent::__construct();
    }

    /**
     * Get list of unique investor IDs who made a deposit before x min
     * @param  int    $beforeMinutes
     * @return array
     */
    public function getInvestorsMadeDepositWithAutoInvestStrategies(
        int $beforeMinutes
    ): array
    {
        $dateTime = Carbon::now()->subMinutes($beforeMinutes);

        $results = DB::select(
            DB::raw("
                select distinct is2.investor_id
                from invest_strategy is2
                join transaction t on (
                    t.investor_id = is2.investor_id
                    and t.type = '" . Transaction::TYPE_DEPOSIT . "'
                    and t.created_at >= '" . $dateTime->format('Y-m-d H:i:0') . "'
                    and t.created_at <= '" . $dateTime->format('Y-m-d H:i:59') . "'
                )
                join investor i on (
                    i.investor_id = is2.investor_id
                    and i.active = 1
                    and i.deleted = 0
                )
                where
                    is2.active = 1
                    and is2.agreed = 1
                    and is2.deleted = 0
            ")
        );

        return $results;
    }

    /**
     * @param int $limit
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByWhereConditions(int $limit, array $data)
    {
        $order = $this->getOrderConditions($data);
        unset($data['order']);

        $whereCaseConditions = $this->getWhereCaseConditions($data);
        unset($data['from']);
        unset($data['to']);

        if (!empty($data['limit'])) {
            $limit = $data['limit'];
            unset($data['limit']);
        }

        $whereConditions = $this->getWhereConditions($data, ['name'], 'transaction');

        return $this->transactionRepository->getAll(
            $limit,
            $whereConditions,
            $whereCaseConditions,
            !empty($order) ? $order : ['transaction_id' => 'DESC', 'active' => 'DESC']
        );
    }

    public function deposit(
        float $amount,
        int $investorId,
        int $walletId,
        int $currencyId,
        int $investorBankAccountId,
        ?string $bankTransactionId
    ): Transaction {
        $params = [
            'bank_transaction_id' => $bankTransactionId,
            'investor_id' => $investorId,
            'wallet_id' => $walletId,
            'currency_id' => $currencyId,
            'bank_account_id' => $investorBankAccountId,
            'originator_id' => Originator::ID_ORIG_STIKCREDIT,
            'direction' => Transaction::DIRECTION_IN,
            'type' => Transaction::TYPE_DEPOSIT,
            'amount' => $amount,
            'details' => 'Bank transaction #' . $bankTransactionId,
        ];

        return $this->createTransaction($params);
    }

    public function withdraw(
        float $amount,
        int $taskId,
        int $investorId,
        int $walletId,
        int $currencyId,
        int $investorBankAccountId,
        ?string $bankTransactionId
    ): Transaction {
        $params = [
            'task_id' => $taskId,
            'bank_transaction_id' => $bankTransactionId,
            'investor_id' => $investorId,
            'wallet_id' => $walletId,
            'currency_id' => $currencyId,
            'bank_account_id' => $investorBankAccountId,
            'originator_id' => Originator::ID_ORIG_STIKCREDIT,
            'direction' => Transaction::DIRECTION_OUT,
            'type' => Transaction::TYPE_WITHDRAW,
            'amount' => $amount,
            'details' => 'Bank transaction #' . $bankTransactionId,
        ];

        return $this->createTransaction($params);
    }

    public function bonus(
        float $amount,
        int $taskId,
        int $investorId,
        int $walletId,
        int $currencyId,
        ?int $investorBankAccountId,
        ?string $bankTransactionId
    ): Transaction {
        $params = [
            'task_id' => $taskId,
            'bank_transaction_id' => $bankTransactionId,
            'investor_id' => $investorId,
            'wallet_id' => $walletId,
            'currency_id' => $currencyId,
            'bank_account_id' => $investorBankAccountId,
            'originator_id' => Originator::ID_ORIG_STIKCREDIT,
            'direction' => Transaction::DIRECTION_OUT,
            'type' => Transaction::TYPE_BONUS,
            'amount' => $amount,
            'details' => 'Bank transaction #' . $bankTransactionId,
        ];

        return $this->createTransaction($params);
    }

    public function investment(
        float $amount,
        int $loanId,
        int $investorId,
        int $walletId,
        int $currencyId,
        int $investorBankAccountId,
        int $investmentId
    ): Transaction {
        $params = [
            'loan_id' => $loanId,
            'investor_id' => $investorId,
            'bank_account_id' => $investorBankAccountId,
            'wallet_id' => $walletId,
            'currency_id' => $currencyId,
            'investment_id' => $investmentId,
            'amount' => $amount,
            'originator_id' => Originator::ID_ORIG_STIKCREDIT,
            'direction' => Transaction::DIRECTION_IN,
            'type' => Transaction::TYPE_INVESTMENT,
            'details' => 'Invest in loan #' . $loanId,
        ];

        return $this->createTransaction($params);
    }

    public function repaymentLoan(
        float $principal,
        float $accruedInterest,
        float $interest,
        float $lateInterest,
        int $loanId,
        int $investorId,
        int $walletId,
        int $currencyId,
        int $investorBankAccountId,
        int $investmentId,
        bool $earlyRepayment = false,
        Carbon $repaymentDate = null
    ): Transaction {
        $params = [
            'loan_id' => $loanId,
            'investor_id' => $investorId,
            'bank_account_id' => $investorBankAccountId,
            'investment_id' => $investmentId,
            'wallet_id' => $walletId,
            'currency_id' => $currencyId,
            'originator_id' => Originator::ID_ORIG_STIKCREDIT,
            'direction' => Transaction::DIRECTION_OUT,
            'type' => (
            $earlyRepayment
                ? Transaction::TYPE_EARLY_REPAYMENT
                : Transaction::TYPE_REPAYMENT
            ),

            'principal' => $principal,
            'accrued_interest' => $accruedInterest,
            'interest' => $interest,
            'late_interest' => $lateInterest,
            'amount' => ($principal + $accruedInterest + $interest + $lateInterest),

            'details' => ($earlyRepayment ? 'Early r' : 'R') . 'epayment '
                . 'for loan #' . $loanId . '('
                . 'principal = ' . $principal
                . ', accr_interest = ' . $accruedInterest
                . ', interest = ' . $interest
                . ', late_interest = ' . $lateInterest
                . ')',
        ];

        // used for crons, which runs on night, so we changed today date for yesterday
        if (!empty($repaymentDate)) {
            $params['created_at'] = $repaymentDate->format('Y-m-d H:i:s');
        }

        return $this->createTransaction($params);
    }

    public function repaymentInstallment(
        float $principal,
        float $accruedInterest,
        float $interest,
        float $lateInterest,
        int $loanId,
        int $installmentId,
        int $investorId,
        int $walletId,
        int $currencyId,
        int $investorBankAccountId,
        int $investmentId,
        Carbon $repaymentDate = null
    ): Transaction {
        $params = [
            'loan_id' => $loanId,
            'investor_id' => $investorId,
            'bank_account_id' => $investorBankAccountId,
            'investment_id' => $investmentId,
            'wallet_id' => $walletId,
            'currency_id' => $currencyId,
            'originator_id' => Originator::ID_ORIG_STIKCREDIT,
            'direction' => Transaction::DIRECTION_OUT,
            'type' => Transaction::TYPE_INSTALLMENT_REPAYMENT,
            'details' => 'Repayment for installment #' . $installmentId
                . ', loan #' . $loanId
                . '('
                . ', principal = ' . $principal
                . ', accr_interest = ' . ($accruedInterest == $interest ? 0 : $accruedInterest)
                . ', interest = ' . $interest
                . ', late_interest = ' . $lateInterest
                . ')',
        ];

        $params = $this->addAmounts(
            $params,
            $principal,
            $accruedInterest,
            $interest,
            $lateInterest
        );

        // used for crons, which runs on night, so we changed today date for yesterday
        if (!empty($repaymentDate)) {
            $params['created_at'] = $repaymentDate->format('Y-m-d H:i:s');
        }

        return $this->createTransaction($params);
    }

    public function rebuyLoan(
        float $principal,
        float $accruedInterest,
        float $interest,
        float $lateInterest,
        int $loanId,
        int $investorId,
        int $walletId,
        int $currencyId,
        int $investorBankAccountId,
        int $investmentId,
        bool $manualRebuy = false,
        Carbon $repaymentDate = null
    ): Transaction {
        $params = [
            'loan_id' => $loanId,
            'investor_id' => $investorId,
            'bank_account_id' => $investorBankAccountId,
            'investment_id' => $investmentId,
            'wallet_id' => $walletId,
            'currency_id' => $currencyId,
            'originator_id' => Originator::ID_ORIG_STIKCREDIT,
            'direction' => Transaction::DIRECTION_OUT,
            'type' => (
            $manualRebuy
                ? Transaction::TYPE_BUYBACK_MANUAL
                : Transaction::TYPE_BUYBACK_OVERDUE
            ),

            'principal' => $principal,
            'accrued_interest' => $accruedInterest,
            'interest' => $interest,
            'late_interest' => $lateInterest,
            'amount' => ($principal + $accruedInterest + $interest + $lateInterest),

            'details' => ($manualRebuy ? 'Manual' : 'Overdue') . ' buyback loan #' . $loanId . '('
                . 'principal = ' . $principal
                . ', accr_interest = ' . $accruedInterest
                . ', interest = ' . $interest
                . ', late_interest = ' . $lateInterest
                . ')',
        ];

        // used for crons, which runs on night, so we changed today date for yesterday
        if (!empty($repaymentDate)) {
            $params['created_at'] = $repaymentDate->format('Y-m-d H:i:s');
        }

        return $this->createTransaction($params);
    }

    public function createTransaction(array $params): Transaction
    {
        return $this->transactionRepository->create($params);
    }

    /**
     * @param Investor $investor
     * @param array $filterData
     * @param int|null $limit
     * @param int|null $page
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function transactionList(
        Investor $investor,
        array $filterData,
        int $limit = null,
        int $page = 1
    ) {
        $today = Carbon::now()->format('Y-m-d');
        $dateTo = $today;
        if (isset($filterData['createdAt']['to'])) {
            $dateTo = dbDate($filterData['createdAt']['to']);
        }
        $dateFrom = $today;
        if (isset($filterData['createdAt']['from'])) {
            $dateFrom = dbDate($filterData['createdAt']['from']);
        }


        $filters = !empty($filterData['type']) ? $filterData['type'] : [];
        if (empty($filters)) {
            $filters = array_keys(Transaction::getAccountStatementTypes());
        }

        return $this->transactionRepository->transactionList(
            $investor->investor_id,
            $dateFrom,
            $dateTo,
            $filters,
            $limit,
            $page
        );
    }

    /**
     * @param Investor $investor
     * @param string $dateFrom
     * @param string $dateTo
     * @param array|null $filterData
     *
     * @return mixed
     */
    public function transactionsSum(
        Investor $investor,
        string $dateFrom,
        string $dateTo,
        array $filterData = []
    ) {

        if (empty($filterData)) {
            $filterData = array_keys(Transaction::getAccountStatementTypes());
        }

        return $this->transactionRepository->transactionsSum(
            $investor->investor_id,
            $dateFrom,
            $dateTo,
            $filterData
        );
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ) {
        $where = [];

        $where['transaction.active'] = '1';

        if (!empty($data['investor_id'])) {
            $where[] = [
                'transaction.investor_id',
                '=',
                $data['investor_id']
            ];
            unset($data['investor_id']);
        }
        if (!empty($data['amount'])) {
            if (!empty($data['amount']['from'])) {
                $where[] = [
                    'transaction.amount',
                    '>=',
                    $data['amount']['from'],
                ];
            }

            if (!empty($data['amount']['to'])) {
                $where[] = [
                    'transaction.amount',
                    '<=',
                    $data['amount']['to'],
                ];
            }

            unset($data['amount']);
        }

        if (!empty($data['createdAt']['from'])) {
            $where[] = [
                'transaction.created_at',
                '>=',
                dbDate($data['createdAt']['from'], '00:00:00'),
            ];
        }

        if (!empty($data['createdAt']['to'])) {
            $where[] = [
                'transaction.created_at',
                '<=',
                dbDate($data['createdAt']['to'], '23:59:59'),
            ];
        }

        unset($data['createdAt']);

        if (!empty($data['type'])) {
            $where['transaction_type'] = [$data['type'][0]];
            unset($data['type']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function getWhereCaseConditions(array $data)
    {
        $whereCase = [];
        if (!empty($data['from'])) {
            $whereCase['from'] = $data['from'];
        }
        if (!empty($data['to'])) {
            $whereCase['to'] = $data['to'];
        }

        return $whereCase;
    }

    protected function addAmounts(
        array $params,
        float $principal,
        float $accruedInterest,
        float $interest,
        float $lateInterest
    ): array {
        $params['principal'] = $principal;
        $params['interest'] = $interest;
        $params['accrued_interest'] = 0.0;
        if ($accruedInterest < $interest) {
            $params['accrued_interest'] = $accruedInterest;
            $params['interest'] = 0.0;
        }
        $params['late_interest'] = 0.0;
        if ($lateInterest > 0) {
            $params['late_interest'] = $lateInterest;
        }
        $params['amount'] = ($params['principal'] + $params['accrued_interest'] + $params['interest'] + $params['late_interest']);

        return $params;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function transactionExistByBankTransaction($id): bool
    {
        $transaction = $this->transactionRepository->getByBankTransactionId($id);

        return !empty($transaction);
    }

    /**
     * @param int $investorId
     * @param int $walletId
     *
     * @return array
     */
    public function calculatedWalletByTransactions(int $investorId, int $walletId)
    {
        return $this->transactionRepository->getCalculatedWalletByTransactions($investorId, $walletId);
    }

    /**
     * @param $file
     * @param $fileTypeId
     * @param $filePath
     * @param $newFileName
     *
     * @return bool
     */
    public function importPayments(
        $file,
        $fileTypeId,
        $filePath,
        $newFileName
    ) {
        try {
            $importedFile = $this->storageService->import(
                $file,
                $fileTypeId,
                $filePath,
                $newFileName,
                true
            );

            $this->documentParser->parseImportedPayments($importedFile);
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * @param ImportedPayment $importedPayment
     * @param null|Transaction $transaction
     *
     * @return ImportedPayment
     */
    public function markImportedPaymentHandled(
        ImportedPayment $importedPayment,
        ?Transaction $transaction = null
    ) {
        $importedPayment->status = ImportedPayment::STATUS_HANDLED;
        if (!empty($transaction)) {
            $importedPayment->transaction_id = $transaction->getId();
        }

        $importedPayment->save();

        return $importedPayment;
    }

    /**
     * @param int $investorId
     * @param int $transactionId
     * @return \Illuminate\Database\Eloquent\Model|Transaction|object|null
     */
    public function getByInvestorAndTransactionId(int $investorId, int $transactionId)
    {
        return $this->transactionRepository->byInvestorAndTransactionId($investorId, $transactionId);
    }

    /**
     * @param int $investorId
     * @param string $dateFrom
     * @return bool
     */
    public function investorHasTransactionsAfter(int $investorId, string $dateFrom): bool
    {
        return $this->transactionRepository->investorHasTransactionsAfter(
            $investorId,
            $dateFrom
        );
    }

    /**
     * @param string $startAt
     * @param string $endAt
     * @return float
     */
    public function getRepaymentAmountForPeriod(string $startAt, string $endAt): float
    {
        return $this->transactionRepository->getRepaymentAmountForPeriod($startAt, $endAt);
    }
}
