<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart\Entities;

use InvalidArgumentException;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Cart\CartLoanHelper;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestment;

class CartLoanBuyer extends CartLoan implements CartLoanBuyerInterface
{
    private MarketSecondary $marketSecondary;

    /**
     * What part of investment really bought
     * (
     *   Total investment is euro 20 (100%),
     *   investor put on sell 10 euro (50%)
     *   buyer bought 5 euro (25% of initial investment)
     * )
     *
     * @var float
     */
    private float $percentBought;

    public static function buildNew(
        int $cartId,
        Loan $loan,
        Investment $investment,
        Originator $originator,
        MarketSecondary $marketSecondary,
        float $principalForSale,
        float $premium,
        float $percentBought,
        string $filters,
        bool $status,
        string $reason
    ): self
    {
        $price = CartLoanHelper::calculatePrice($premium, $principalForSale);

        return new self(
            0,
            $cartId,
            $loan,
            $investment,
            $originator,
            $marketSecondary,
            $principalForSale,
            $premium,
            $price,
            $percentBought,
            $filters,
            $status,
            $reason
        );
    }

    public static function build(
        int $cartLoanId,
        int $cartId,
        Loan $loan,
        Investment $investment,
        Originator $originator,
        MarketSecondary $marketSecondary,
        float $principalForSale,
        float $premium,
        float $price,
        float $percentBought,
        string $filters,
        bool $status,
        string $reason
    ): self
    {
        return new self(
            $cartLoanId,
            $cartId,
            $loan,
            $investment,
            $originator,
            $marketSecondary,
            $principalForSale,
            $premium,
            $price,
            $percentBought,
            $filters,
            $status,
            $reason
        );
    }

    public function __construct(
        int $cartLoanId,
        int $cartId,
        Loan $loan,
        Investment $investment,
        Originator $originator,
        MarketSecondary $marketSecondary,
        float $principalForSale,
        float $premium, // % - min = 0.1
        float $price,
        float $percentBought,
        string $filters,
        bool $status,
        string $reason
    )
    {
        parent::__construct(
            $cartLoanId,
            $cartId,
            $loan,
            $investment,
            $originator,
            $principalForSale,
            $premium,
            $price,
            0,
            0,
            $filters,
            $status,
            $reason
        );

        $this->marketSecondary = $marketSecondary;
        $this->percentBought = $percentBought;
    }

    /**
     * @return MarketSecondary
     */
    public function getMarketSecondary(): MarketSecondary
    {
        return $this->marketSecondary;
    }

    /**
     * @return float
     */
    public function getPercentBought(): float
    {
        return $this->percentBought;
    }


    public function recalculatePercentBought(): void
    {
        $this->percentBought = CartLoanHelper::calculatePercentBought(
            $this->getPrincipalForSale(),
            (float)$this->getMarketSecondary()->loanOnSale->principal_for_sale
        );
    }

    private function checkSecondaryInvestment(): void
    {
        /*if (! $this->getMarketSecondary()->getMarketSecondaryId())
        {
            //throw new InvalidArgumentException("Secondary investment has no ID. Was it saved into DB?");
        }*/
    }

}
