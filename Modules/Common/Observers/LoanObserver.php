<?php

namespace Modules\Common\Observers;

use Carbon\Carbon;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\AutoRebuyLoanService;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\PortfolioService;

class LoanObserver
{
    public function updating(Loan $loan)
    {
        // if loan become unlisted - goes to any final status
        // means, we not sell it anymore, so we need to remove quality and maturity of it
        if (
            $loan->isDirty('status')
            && in_array($loan->getAttribute('status'), Loan::getFinalStatuses())
        ) {
            // mark installments and investor installments as paid
            $service = \App::make(DistributeService::class);
            $service->closeInstallments($loan, $loan->unlisted_at);


            // logic for auto-rebuy loans - mark them as handled for manual buyback
            // add record for autorebuy for overdue buyback
            if ($loan->status == Loan::STATUS_REBUY) {
                $service = \App::make(AutoRebuyLoanService::class);

                if (!empty($unlistedLoan = $loan->unlistedLoan)) {
                    $service->markUnlistedLoanAsHandled($unlistedLoan);
                } else { // if loan with overdue
                    $service->createAutoRebuyLog($loan);
                }
            }


            // null overdue on closed loan
            $loan->overdue_days = 0;


            if (empty($loan->investments)) {
                return;
            }

            $service = \App::make(PortfolioService::class);
            $service->massReduceQualityRange(
                $loan->loan_id,
                $loan->currency_id, // TODO
                $loan->getOriginal('payment_status')
            );

            $service->massReduceMaturityRange(
                $loan->loan_id,
                Carbon::parse($loan->final_payment_date),
                Carbon::parse($loan->unlisted_at)
            );

            return;
        }

        // if loan has changed its payment status
        // means an installment was paid and next installment become active
        // so the overdue has been changed, so we need to re-calc quality
        if ($loan->isDirty('payment_status')) {
            $service = \App::make(PortfolioService::class);
            $service->massUpdateQualityRange(
                $loan->loan_id,
                $loan->currency_id, // TODO
                $loan->getOriginal('payment_status'),
                $loan->getAttribute('payment_status')
            );
        }
    }
}
