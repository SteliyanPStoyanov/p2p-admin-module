<?php

declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart;


class CartLoanHelper
{
    public static function calculatePrice(float $premium, float $principalForSale): float
    {
        $percent = 100 + $premium;
        $price = $percent * $principalForSale / 100;

        // to keep consistency with js toFixed(2)
        return (float)sprintf('%.02F', $price);
    }

    public static function comparePrices(float $passedInPrice, float $calculatedPrice): bool
    {
        $passedInPriceStr = (string)$passedInPrice;
        $calculatedPriceStr = (string)$calculatedPrice;

        if (bccomp($passedInPriceStr, $calculatedPriceStr, 2) === 0) {
            return true;
        }

        return false;
    }

    public static function calculatePercentBought(float $principalForSale, float $investmentAmount): float
    {
        $percent = $principalForSale * 100 / $investmentAmount;

        return (float)number_format($percent, 1);
    }

    public static function calculatePercentOnSell(float $principalForSale, float $investmentAmount): float
    {
        $percent = $principalForSale * 100 / $investmentAmount;

        return (float)number_format($percent, 1);
    }

    public static function calculatePercentSold(): float
    {
    }

    /**
     * @param float $premiumPercent
     * @param float $amount
     * @return float
     */
    public static function calculateRealPremium(float $premiumPercent, float $amount): float
    {
        $realPremium = bcdiv(
            bcmul(
                (string)$amount,
                (string)$premiumPercent,
                2
            ),
            '100', 2);

        return (float)sprintf('%.02F', $realPremium);
    }

    public static function formatMoney(float $amount): float
    {
        return (float)sprintf('%.02F', $amount);
    }
}
