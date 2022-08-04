<?php

namespace Modules\Common\Observers;

use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Services\PortfolioService;

class InvestmentObserver
{
    public function creating(Investment $investment)
    {
        $loan = $investment->loan;
        $wallet = $investment->wallet();
        $service = \App::make(PortfolioService::class);

        $service->updateQualityRange(
            $investment->loan_id,
            $investment->investor_id,
            $investment->loan->payment_status
        );

        $service->updateMaturityRange(
            $investment->investor_id,
            $wallet->currency_id,
            $loan->final_payment_date,
            $investment->created_at
        );

        if ($investment->parent_id) { // Secondary market entry
            CartSecondaryLoans::where('investment_id', $investment->parent_id)
                ->update([
                    'investment_id' => $investment->investment_id
                ]);

            MarketSecondary::where('investment_id', $investment->parent_id)
                ->update([
                    'investment_id' => $investment->investment_id
                ]);
        }
    }
}
