<?php

declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart\Entities;

use InvalidArgumentException;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Cart\CartLoanHelper;

class CartLoan implements CartLoanInterface
{
    private int $cartLoanId;

    private int $cartId;

    private Loan $loan;

    private Investment $investment;

    private Originator $originator;

    private float $principalForSale;

    private float $premium;

    /**
     * principal + premium = selling price
     *
     * @var float
     */
    private float $price;

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
    private float $percentOnSell;

    /**
     * @var float
     */
    private float $percentBought;

    /**
     * @var string
     */
    private string $filters;

    private bool $status;

    private string $reason;

    public static function new(
        int $cartId,
        Loan $loan,
        Investment $investment,
        Originator $originator,
        float $principalForSale,
        float $premium,
        float $percentOnSell,
        float $percentBought,
        string $filters,
        bool $status,
        string $reason
    ): self {
        return new self(
            0,
            $cartId,
            $loan,
            $investment,
            $originator,
            $principalForSale,
            $premium,
            CartLoanHelper::calculatePrice($premium, $principalForSale),
            $percentOnSell,
            $percentBought,
            $filters,
            $status,
            $reason
        );
    }

    public static function create(
        int $cartLoanId,
        int $cartId,
        Loan $loan,
        Investment $investment,
        Originator $originator,
        float $principalForSale,
        float $premium,
        float $price,
        float $percentOnSell,
        float $percentBought,
        string $filters,
        bool $status,
        string $reason
    ): self {
        return new self(
            $cartLoanId,
            $cartId,
            $loan,
            $investment,
            $originator,
            $principalForSale,
            $premium,
            $price,
            $percentOnSell,
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
        float $principalForSale,
        float $premium, // % - min = 0.1
        float $price,
        float $percentOnSell,
        float $percentBought,
        string $filters,
        bool $status,
        string $reason
    ) {
        $this->cartLoanId = $cartLoanId;
        $this->cartId = $cartId;
        $this->loan = $loan;
        $this->investment = $investment;
        $this->originator = $originator;
        $this->principalForSale = $principalForSale;
        $this->premium = $premium;
        $this->price = $price;
        $this->percentOnSell = $percentOnSell;
        $this->percentBought = $percentBought;
        $this->filters = $filters;
        $this->status = $status;
        $this->reason = $reason;

        $this->checkStatus();

        $this->checkPremium();

        $this->checkPrice();

        $this->checkPrincipalForSale();
    }


    /**
     * @param int $cartLoanId
     */
    public function setCartLoanId(int $cartLoanId): void
    {
        $this->cartLoanId = $cartLoanId;
    }

    /**
     * @inheritdoc
     */
    public function getInvestment(): Investment
    {
        return $this->investment;
    }

    /**
     * @inheritdoc
     */
    public function getOriginator(): Originator
    {
        return $this->originator;
    }

    /**
     * @inheritdoc
     */
    public function getCartLoanId(): int
    {
        return $this->cartLoanId;
    }

    /**
     * @inheritdoc
     */
    public function getCartId(): int
    {
        return $this->cartId;
    }

    /**
     * @inheritdoc
     */
    public function getLoan(): Loan
    {
        return $this->loan;
    }

    /**
     * @inheritdoc
     */
    public function getPrincipalForSale(): float
    {
        return $this->principalForSale;
    }

    /**
     * @inheritdoc
     */
    public function getPremium(): float
    {
        return $this->premium;
    }

    /**
     * @inheritdoc
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getFilters(): string
    {
        return $this->filters;
    }

    /**
     * @return float
     */
    public function getPercentOnSell(): float
    {
        return $this->percentOnSell;
    }

    public function setPercentOnSell(float $percentOnSell): void
    {
        $this->percentOnSell = $percentOnSell;
    }

    /**
     * @return float
     */
    public function getPercentBought(): float
    {
        return $this->percentBought;
    }

    /**
     * @inheritdoc
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    private function checkStatus(): void
    {
        if ($this->isStatus() == false && empty($this->getReason())) {
            throw new InvalidArgumentException("Reason is required when status is not true");
        }
    }

    //TODO: Refactor this because we calculate price in many places - for example check out checkPrice method
    private function calculatePrice(): void
    {
        $this->price = CartLoanHelper::calculatePrice($this->getPremium(), $this->getPrincipalForSale());
    }

    /**
     * This method check if passed in price is equal to price calculated
     * based on passed in principal_for_sale and premium
     *
     * @throws InvalidArgumentException
     */
    private function checkPrice(): void
    {
        $price = CartLoanHelper::calculatePrice($this->getPremium(), $this->getPrincipalForSale());

        if (false == CartLoanHelper::comparePrices($this->getPrice(), $price)) {
            throw new InvalidArgumentException("Indicated price is not equal to calculated");
        }
    }

    /**
     * @param float $principalForSale
     */
    public function setPrincipalForSale(float $principalForSale): void
    {
        $this->principalForSale = $principalForSale;

        $this->checkPrincipalForSale();

        $this->calculatePrice();
    }

    /**
     * @param float $premium
     */
    public function setPremium(float $premium): void
    {
        $this->premium = $premium;

        $this->calculatePrice();
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    private function checkPremium(): void
    {
         $premiumLimit =(int)\SettingFacade::getSettingValue(
            Setting::PREMIUM_LIMIT_VALUE_KEY
        );

        if (abs($this->getPremium()) > $premiumLimit) {
            throw new InvalidArgumentException("Premium should be between -15% - +15%");
        }
    }

    private function checkPrincipalForSale(): void
    {
        if ($this->getPrincipalForSale() > $this->getInvestment()->amount) {
            $this->setPrincipalForSale((float)$this->getInvestment()->amount);

            // recalculate price according to new principal
            $this->calculatePrice();
        }
    }
}
