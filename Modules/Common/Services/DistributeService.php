<?php

namespace Modules\Common\Services;

use \Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\RepaidInstallment;
use Modules\Common\Entities\RepaidLoan;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Events\LoanAmountAvailableEvents;
use Modules\Common\Services\InvestStrategyService;
use Throwable;

class DistributeService extends CommonService
{
    private $strategyService = null;

    /////////////////////////////// LOANS  //////////////////////////////

    public function distributeLoans(
        array $newRepaidLoans,
        Carbon $repaymentDate = null
    ): int
    {
        $distributed = 0;

        foreach ($newRepaidLoans as $newRepaidLoan) {
            if (true === $this->distributeLoan($newRepaidLoan, $repaymentDate)) {
                $distributed++;
            }
        }

        return $distributed;
    }

    public function distributeLoan(
        RepaidLoan $repaidLoan,
        Carbon $repaymentDate = null
    ): bool
    {
        try {
            $loan = $repaidLoan->loan();
            if (!$loan) {
                throw new Exception("Failed to find loan for repaid_installment, repaidLoan->lender_id =" . $repaidLoan->lender_id);
            }

            // skip if already paid
            if (Loan::STATUS_ACTIVE != $loan->status) {
                throw new Exception(
                    "Already paid loan #" . $loan->loan_id
                );
            }

            $investments = $loan->distinctInvestments();
        } catch (Throwable $e) {
            Log::channel('distr_service')->error(
                'Error! Failed to repaid loan, ' . 'msg: ' . $e->getMessage()
                . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
            return false;
        }

        return $this->doDistributeLoan(
            $repaidLoan,
            $loan,
            $investments,
            $repaymentDate
        );
    }

    public function doDistributeLoan(
        RepaidLoan $repaidLoan,
        Loan $loan,
        array $investments = [],
        Carbon $repaymentDate = null
    ): bool
    {
        DB::beginTransaction();

        try {
            $earlyRepayment = (RepaidLoan::TYPE_EARLY == $repaidLoan->repayment_type);

            // repay investments
            $this->distributeInvestments(
                $loan,
                $investments,
                $earlyRepayment,
                false, // buyback
                $repaymentDate
            );


            // mark repaid_loan as handled
            $repaidLoan->handle();


            // mark loan as repaid & unlisted
            $loan->repaid($earlyRepayment, $repaymentDate);


            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();

            Log::channel('distr_service')->error(
                'Error! Failed to distribute loan #' . $repaidLoan->lender_id . ', '
                . 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );

            return false;
        }

        return true;
    }

    public function distributeInvestments(
        Loan $loan,
        array $investments,
        bool $earlyRepayment = false,
        bool $buybackOverdue = false,
        Carbon $repaymentDate = null
    ): void
    {
        if (empty($investments)) {
            return;
        }

        $now = !empty($repaymentDate) ? $repaymentDate : Carbon::now();
        foreach ($investments as $investment) {
            // define main objects
            $investor = $investment->investor();
            $wallet = $investor->wallet();

            $unpaidInstallments = $investor->getUnpaidInstallments(
                $loan->loan_id,
                $investment->getId()
            );
            $strategiesRepayments = $this->getStrategyService()
                ->getStrategiesPrincipalsForLoan(
                    $investor->investor_id,
                    $loan->loan_id,
                    $investment->getId()
                );

            // add money to wallet
            $sums = $this->getAmountsForRepayment($unpaidInstallments, $now);
            $principal = $sums['principal'];
            $interest = $sums['interest'];
            $lateInterest = $sums['late_interest'];
            $accruedInterest = $sums['accrued_interest'];

            $wallet->addIncomeAmounts(
                $principal,
                ($interest + $accruedInterest),
                $lateInterest
            );


            // IMPORTANT: maturity & quality ranges update in loan observer


            // create transaction
            if ($buybackOverdue) {
                $this->getTransactionService()->rebuyLoan(
                    $principal,
                    $accruedInterest,
                    $interest,
                    $lateInterest,
                    $loan->loan_id,
                    $investor->investor_id,
                    $wallet->wallet_id,
                    $wallet->currency_id,
                    $investor->getMainBankAccountId(),
                    $investment->getId(),
                    !empty($loan->unlistedLoan->unlisted_loan_id),
                    $repaymentDate
                );
            } else {
                $this->getTransactionService()->repaymentLoan(
                    $principal,
                    $accruedInterest,
                    $interest,
                    $lateInterest,
                    $loan->loan_id,
                    $investor->investor_id,
                    $wallet->wallet_id,
                    $wallet->currency_id,
                    $investor->getMainBankAccountId(),
                    $investment->getId(),
                    $earlyRepayment,
                    $repaymentDate
                );
            }


            // updates strategies
            if (!empty($strategiesRepayments)) {
                $this->getStrategyService()->updateStrategiesAmounts($strategiesRepayments);
            }
        }
    }

    /////////////////////////// INSTALLMENTS ////////////////////////////

    public function distributeInstallments(
        array $newRepaidInstallments,
        Carbon $repaymentDate = null
    ): int
    {
        $distributed = 0;

        foreach ($newRepaidInstallments as $repaidInstallment) {
            if (true === $this->distributeInstallment($repaidInstallment, $repaymentDate)) {
                $distributed++;
            }
        }

        return $distributed;
    }

    public function distributeInstallment(
        RepaidInstallment $repaidInstallment,
        Carbon $repaymentDate = null
    ): bool
    {
        try {

            $loan = $repaidInstallment->loan();
            if (!$loan) {
                throw new Exception(
                    "Failed to find loan for repaid_installment, repaidInstallment->repaid_installment_id = " . $repaidInstallment->repaid_installment_id
                );
            }

            $installment = $repaidInstallment->installment();
            if (!$installment) {
                throw new Exception(
                    "Failed to find installment for repaid_installment #" . $repaidInstallment->repaid_installment_id
                );
            }

            // skip if already paid
            if (1 == $installment->paid) {
                throw new Exception(
                    "ALready paid installment #" . $installment->installment_id
                );
            }

            // there are loans with no investments, so this array could be null
            $investorInstallments = $repaidInstallment->investorInstallments();

        } catch (Throwable $e) {
            Log::channel('distr_service')->error(
                'Error! Failed to get installments. '
                . 'repaid_installment = ' . $repaidInstallment->repaid_installment_id . ', '
                . 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
            return false;
        }

        return $this->doDistributeInstallment(
            $repaidInstallment,
            $loan,
            $installment,
            $investorInstallments,
            $repaymentDate
        );
    }

    public function doDistributeInstallment(
        RepaidInstallment $repaidInstallment,
        Loan $loan,
        Installment $installment,
        array $investorInstallments = null,
        Carbon $repaymentDate = null
    ): bool
    {
        DB::beginTransaction();

        try {

            // mark installment as paid
            $installment->pay($repaymentDate);

            // update loan
            $nextInstallment = $loan->getFirstUnpaidInstallment();
            $loan->addPayment($nextInstallment, $repaymentDate);
            event(new LoanAmountAvailableEvents($loan, null, $installment));
            if (!empty($investorInstallments)) {
                // loop investor installments (parts of current installment)
                foreach ($investorInstallments as $investorInstallment) {

                    if ($investorInstallment->paid == 1) {
                        continue;
                    }

                    // get investor
                    $investor = $investorInstallment->investor();

                    // get strategies repayments
                    $strategiesRepayments = $this->getStrategyService()
                        ->getStrategyPrincipalForInstallment(
                            $investorInstallment
                        );

                    // mark investor installments as paid
                    $investorInstallment->pay($repaymentDate);

                    // update wallet amounts
                    $wallet = $investor->wallet();
                    $wallet->addIncomeForInstallment($investorInstallment);

                    // Info: update portfolio quality is done in LoanObserver

                    // add transaction
                    $this->getTransactionService()->repaymentInstallment(
                        $investorInstallment->principal,
                        $investorInstallment->accrued_interest,
                        $investorInstallment->interest,
                        $investorInstallment->late_interest,
                        $loan->loan_id,
                        $investorInstallment->installment_id,
                        $investor->investor_id,
                        $wallet->wallet_id,
                        $wallet->currency_id,
                        $investor->getMainBankAccountId(),
                        $investorInstallment->investment_id,
                        $repaymentDate
                    );

                    // update strategy amounts
                    if (!empty($strategiesRepayments)) {
                        $this->getStrategyService()
                            ->updateStrategiesAmounts($strategiesRepayments);
                    }
                }
            }

            // mark new payment as handled
            $repaidInstallment->handle();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();

            Log::channel('distr_service')->error(
                'Error! Failed to update installments. '
                . 'repaid_installment = ' . $repaidInstallment->repaid_installment_id . ', '
                . 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
            return false;
        }


        // we do it out of previous operation, because insallment should be saved as paid
        // then we can close a loan
        try {
            if ($installment->isLast()) {

                // if last installment has been paid -> close loan
                if (empty($repaymentDate)) {
                    $repaymentDate = Carbon::now();
                }

                $earlyRepayment = $repaymentDate->lt(Carbon::parse($loan->final_payment_date));
                $loan->repaid($earlyRepayment, $repaymentDate);
            }
        } catch (Throwable $e) {
            Log::channel('distr_service')->error(
                'Error! Failed to close loan. '
                . 'repaid_installment = ' . $repaidInstallment->repaid_installment_id . ', '
                . 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
        }

        return true;
    }

    /**
     * Set paid, paid_at and payment_status for installments
     * And paid, paid_at for investor installment
     * We use current method when loan is repaying
     * Invokes from LoanObserver
     *
     * @param Loan $loan
     * @param Carbon $paidAt
     * @return bool
     */
    public function closeInstallments(Loan $loan, Carbon $paidAt): bool
    {
        $installments = $loan->getUnpaidInstallments();
        if (empty($installments)) {
            return true;
        }

        // close global/common installments
        foreach ($installments as $installment) {
            $installment->pay($paidAt);
        }


        $invInstallments = $loan->investorInstallments(false);
        if ($invInstallments->count() < 1) {
            return true;
        }

        // close investor installments
        foreach ($invInstallments->get() as $invIstallment) {
            $invIstallment->pay($paidAt);
        }

        return true;
    }

    private function getAmountsForRepayment(
        array $unpaidInstallments = [],
        Carbon $now
    ): array
    {
        $nowDate = $now->format('Y-m-d');
        $summs = [
            'principal' => 0,
            'accrued_interest' => 0,
            'interest' => 0,
            'late_interest' => 0,
        ];


        foreach ($unpaidInstallments as $key => $installment) {
            $dueDate = Carbon::parse($installment->due_date);

            // we should return all pricipal
            $summs['principal'] += $installment->principal;

            // if today is a due_date we take only interest
            if ($nowDate == $installment->due_date) {
                $summs['interest'] += $installment->interest;
                continue;
            }

            // if today is after due_date we take: interest & late_interest
            if ($now->gt($dueDate)) {
                $summs['interest'] += $installment->interest;
                $summs['late_interest'] += $installment->late_interest;
                continue;
            }

            // if today is before due_date we take: only accrued_interest
            $summs['accrued_interest'] += $installment->accrued_interest;
        }

        return $summs;
    }

    private function getStrategyService(): InvestStrategyService
    {
        if (null === $this->strategyService) {
            $this->strategyService = \App::make(InvestStrategyService::class);
        }

        return $this->strategyService;
    }
}
