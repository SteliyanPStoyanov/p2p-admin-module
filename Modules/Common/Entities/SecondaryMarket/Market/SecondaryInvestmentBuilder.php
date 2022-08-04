<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Market;

use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestmentInterface;

class SecondaryInvestmentBuilder
{
    /**
     * @param Investor $investor
     * @param Loan $loan
     * @param Investment $investment
     * @param Originator $originator
     * @param float $principal_for_sale
     * @param float $premium
     * @param float $price
     * @param bool $status
     * @param int $secondary_loan_on_sale
     * @return SecondaryInvestmentInterface
     */
    public function buildNew(
        Investor $investor,
        Loan $loan,
        Investment $investment,
        Originator $originator,
        float $principal_for_sale,
        float $premium,
        float $price,
        float $percentSold,
        bool $status,
        int $secondary_loan_on_sale
    ): SecondaryInvestmentInterface
    {
        return SecondaryInvestment::new(
            $investor,
            $loan,
            $investment,
            $originator,
            $principal_for_sale,
            $premium,
            $price,
            $percentSold,
            $status,
            $secondary_loan_on_sale
        );
    }
}
