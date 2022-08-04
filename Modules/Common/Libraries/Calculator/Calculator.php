<?php

namespace Modules\Common\Libraries\Calculator;

use Carbon\Carbon;
use Modules\Common\Entities\Portfolio;

class Calculator
{

    /**
     * bcdiv: 104.7878 -> 104.78
     * round: 104.7878 -> 104.79
     *
     * @param float $number
     *
     * @return float
     */
    public static function round(float $number, int $precision = 2): float
    {
        // round to lower border
        // return bcdiv($number, 1, $precision);

        // round to higher border
        return round($number, $precision);
    }

    /**
     * @param float $afrangaAmount
     * @param float $usedPercent
     *
     * @return float
     */
    public static function getAvailableAmount(
        float $afrangaAmount,
        float $usedPercent
    ): float {
        return self::round($afrangaAmount - ($afrangaAmount * $usedPercent / 100));
    }

    /**
     * [getOverdueDays description]
     *
     * @param Carbon $listingDate
     * @param Carbon $dueDate
     *
     * @return int
     */
    public static function getOverdueDays(Carbon $listingDate, Carbon $dueDate): int
    {
        if ($listingDate->lt($dueDate)) {
            $now = Carbon::now();
            if ($now->gte($dueDate)) {
                return InstallmentCalculator::simpleDateDiff(
                    $now,
                    $dueDate
                );
            }
        }

        return 0;
    }

    /**
     * @param float $loanAmount
     * @param float $originatorPercent
     *
     * @return float
     */
    public static function getOriginatorAmount(
        float $loanAmount,
        float $originatorPercent
    ): float {
        return self::round(
            $loanAmount / 100 * $originatorPercent
        );
    }

    /**
     * @param float $amount
     *
     * @return float
     */
    public static function toEuro(
        float $amount
    ): float {
        return self::round($amount / config('common.currencyRate'));
    }

    /**
     * @param float $amount
     *
     * @return float
     */
    public static function toBgn(
        float $amount
    ): float {
        return self::round($amount * config('common.currencyRate'));
    }

    /**
     * @param $portfolioRanges
     * @param float $range
     *
     * @return float
     */
    public static function getPortfolioRangesPresent(
        $portfolioRanges,
        float $range
    ): float {
        $singePart = 1;

        if (array_sum((array)$portfolioRanges) > 0) {
            $singePart = 100 / array_sum((array)$portfolioRanges);
        }

        return self::round($singePart * $range);
    }

    /**
     * @param float $outstandingInvestmentValue
     * @param float $discountValue
     * @return float
     */
    public static function getMaxSaleAmount(
        float $outstandingInvestmentValue,
        float $discountValue
    ): float {
        $sum = $outstandingInvestmentValue;
        if ($discountValue > 0) {
            $sum += ($outstandingInvestmentValue * $discountValue / 100);
        }

        return self::round(
            $sum
        );
    }
}
