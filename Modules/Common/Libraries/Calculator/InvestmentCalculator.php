<?php

namespace Modules\Common\Libraries\Calculator;

use FontLib\Table\Type\loca;
use Modules\Common\Entities\Loan;

class InvestmentCalculator extends Calculator
{
    /**
     * Calculate the percent of loan bought by investor
     *
     * @param float $amount
     * @param float $totalAmount
     * @param float $sumPercent
     * @param bool $lastInvestment
     * @param int $precision
     * @return array
     */
    public static function getInvestedPercentDetails(
        float $amount,
        float $totalAmount,
        float $sumPercent,
        bool $lastInvestment = false,
        int $precision = 0
    ): array {
        $currentPercent = self::round(($amount / $totalAmount * 100), $precision);

        $details = '';

        if ($lastInvestment === true) {
            $freePercent = bcsub(90, $sumPercent, $precision); // макс който можем да продадем

            if (bccomp($currentPercent, $freePercent, $precision) > 0) { // т.е. иска да купи по-голям %

                $details .= 'Calculated % ' . $currentPercent;
                $currentPercent = self::round($freePercent, $precision);
                $details .= ', Received % ' . $currentPercent;
            }
        }

        return ['percent' => $currentPercent, 'details' => $details];
    }
}
