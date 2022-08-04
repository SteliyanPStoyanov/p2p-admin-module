<?php

namespace Modules\Common\Observers;

use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Services\PortfolioService;

class InvestorInstallmentObserver
{
    public function updating(InvestorInstallment $investorInstallment)
    {
        if($investorInstallment->loan()->status == 'active' && $investorInstallment->paid == 1) { // in case when force close loan && if mark paid true

            // Sellers cart
            $cart = CartSecondary::where([
                'investor_id' => $investorInstallment->investor_id,
                'type' => CartSecondary::TYPE_SELLER
            ])->first();

            if (!empty($cart->cart_secondary_id)) {
                foreach ($cart->loansForInvestment as $item) {
                    if($item->percent_on_sell > 0 ) {
                        if ($investorInstallment->investment_id == $item->investment_id) {
                            $newPrincipalToSell = $item->percent_on_sell * $investorInstallment->remaining_principal / 100;
                            $newPrice = $newPrincipalToSell + ($newPrincipalToSell * $item->premium / 100);
                            $cartLoan = CartSecondaryLoans::find($item->cart_loan_id);
                            $cartLoan->update([
                                'price' => $newPrice,
                                'principal_to_sell' => $newPrincipalToSell,
                            ]);


                            // secondary market
                            $secondaryMarket = MarketSecondary::where([
                                'secondary_loan_on_sale' => $item->cart_loan_id
                            ]);

                            if ($secondaryMarket->market_secondary_id) {
                                // Should we have many entries in market_secondary in case if buyer bought only some part of the investment on sell ???? - No we should not. Because we will have buyer cart entries and % sold in market_socondary
                                // lets finish investAllPlansJob and then return to this task

                                // Find real amount sold
                                $realAmountSold = $secondaryMarket->percent_sold * $secondaryMarket->principal_for_sale / 100;
                                $newPercentSold = $realAmountSold * 100 / $newPrincipalToSell;

                                // Can we use newPrice from cart ?
                                // TODO: make sure after click on sell all we update cart entry before put it to market_secondary
                                $secondaryMarket->update([
                                    'percent_sold' => $newPercentSold,
                                    'principal_for_sale' => $newPrincipalToSell,
                                    'price' => $newPrice,
                                ]);
                            }
                        }
                    }
                }
            }

// First on updating looking for paid
// Secondary - have to recalculate loan on sell. Update seller's cart & secondary market. We cannot update buyer's cart. There is their id. And once they click buy it will check if their amount is less or equal to left(origianl) sellers investment

// for example: investment owner have an eu.20 investment and wants to sell - 50% of their investment. So they put eu 10 on sell. And this moment come eu 4 installment payment. So now they own eu 16 investment. And we recalculate what they have on sell. In this case it would be 50% from investment left i.e. eu 16 - 100%, hence 50% - eu 8 - is new amount on sell.

        }
    }
}
