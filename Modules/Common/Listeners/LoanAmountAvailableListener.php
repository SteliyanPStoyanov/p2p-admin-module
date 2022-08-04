<?php

namespace Modules\Common\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\LoanAmountAvailable;
use Modules\Common\Events\LoanAmountAvailableEvents;

class LoanAmountAvailableListener
{
    public function __construct()
    {
    }

    /**
     * @param LoanAmountAvailableEvents $loanAmountAvailableEvents
     * @return false
     */
    public function handle(LoanAmountAvailableEvents $loanAmountAvailableEvents)
    {
        try {
            $loanAmountAvailable = new LoanAmountAvailable();

            if (!empty($loanAmountAvailableEvents->investment)) {
                $amount_before = $loanAmountAvailableEvents->loan->amount_available;
                $amount_after = $loanAmountAvailableEvents->loan->amount_available - $loanAmountAvailableEvents->investment->amount;
                $type = LoanAmountAvailable::TYPE_INVESTMENT;
                $loanAmountAvailable->investment_id = $loanAmountAvailableEvents->investment->investment_id;
            }

            if (!empty($loanAmountAvailableEvents->installment)) {
                $amount_before = $loanAmountAvailableEvents->loan->amount_available + $loanAmountAvailableEvents->installment->total;
                $amount_after = $loanAmountAvailableEvents->loan->amount_available;
                $type = LoanAmountAvailable::TYPE_REPAYMENT;
                $loanAmountAvailable->installment_id = $loanAmountAvailableEvents->installment->installment_id;
            }

            $loanAmountAvailable->loan_id = $loanAmountAvailableEvents->loan->loan_id;
            $loanAmountAvailable->amount_before = $amount_before;
            $loanAmountAvailable->amount_after = $amount_after;
            $loanAmountAvailable->type = $type;

            $loanAmountAvailable->save();
        } catch (Exception $e) {
            Log::channel('loan_amount_available')->error(
                'Failed to save change log. ' . $e->getMessage()
            );
            return false;
        }
    }
}

