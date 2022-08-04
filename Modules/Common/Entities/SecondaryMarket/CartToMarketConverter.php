<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket;

use Modules\Common\Entities\SecondaryMarket\Cart\Entities\Cart;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\NewSecondaryInvestmentCollection;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestmentCollectionInterface;
use Modules\Common\Entities\SecondaryMarket\Market\SecondaryInvestmentBuilder;

class CartToMarketConverter
{
    /**
     * @param Cart $cart
     * @param array $loanData
     * @return SecondaryInvestmentCollectionInterface
     */
    public static function run(Cart $cart, array $loanData): SecondaryInvestmentCollectionInterface
    {
        $secondaryInvestmentCollection = new NewSecondaryInvestmentCollection();
        $secondaryInvestmentBuilder = new SecondaryInvestmentBuilder();

        foreach ($cart->getLoans()->get() as $loan) {
            $secondaryInvestment = $secondaryInvestmentBuilder->buildNew(
                $cart->getInvestor(),
                $loan->getLoan(),
                $loan->getInvestment(),
                $loan->getOriginator(),
                $loan->getPrincipalForSale(),
                $loan->getPremium(),
                $loan->getPrice(),
                0,
                true,
                $loan->getCartLoanId()
            );

            $secondaryInvestmentCollection->add($secondaryInvestment);
        }

        return $secondaryInvestmentCollection;
    }
}
