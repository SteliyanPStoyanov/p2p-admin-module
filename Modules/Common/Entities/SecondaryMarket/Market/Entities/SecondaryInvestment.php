<?php

declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Market\Entities;


use InvalidArgumentException;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\SecondaryMarket\Cart\CartLoanHelper;

class SecondaryInvestment implements SecondaryInvestmentInterface
{
    private int $market_secondary_id;

    private int $secondary_loan_on_sale;

    private Investor $investor;

    private Loan $loan;

    private Investment $investment;

    private Originator $originator;

    private float $principal_for_sale;

    private float $premium;

    private float $price;

    private float $percentSold;

    private bool $active;

    public static function new(
        Investor $investor,
        Loan $loan,
        Investment $investment,
        Originator $originator,
        float $principal_for_sale,
        float $premium,
        float $price,
        float $percentSold,
        bool $active = true,
        int $secondary_loan_on_sale = 0
    ): self {
        return new self(
            0,
            $investor,
            $loan,
            $investment,
            $originator,
            $principal_for_sale,
            $premium,
            $price,
            $percentSold,
            $active,
            $secondary_loan_on_sale
        );
    }

    public static function create(
        int $market_secondary_id,
        Investor $investor,
        Loan $loan,
        Investment $investment,
        Originator $originator,
        float $principal_for_sale,
        float $premium,
        float $price,
        float $percentSold,
        bool $active = true,
        int $secondary_loan_on_sale = 0
    ): self {
        return new self(
            $market_secondary_id,
            $investor,
            $loan,
            $investment,
            $originator,
            $principal_for_sale,
            $premium,
            $price,
            $percentSold,
            $active,
            $secondary_loan_on_sale
        );
    }

    public function __construct(
        int $market_secondary_id,
        Investor $investor,
        Loan $loan,
        Investment $investment,
        Originator $originator,
        float $principal_for_sale,
        float $premium,
        float $price,
        float $percentSold,
        bool $active,
        int $secondary_loan_on_sale = 0
    ) {
        $this->market_secondary_id = $market_secondary_id;

        $this->investor = $investor;

        $this->loan = $loan;

        $this->investment = $investment;

        $this->originator = $originator;

        $this->principal_for_sale = $principal_for_sale;

        $this->premium = $premium;

        $this->price = $price;

        $this->percentSold = $percentSold;

        $this->active = $active;

        $this->secondary_loan_on_sale = $secondary_loan_on_sale;

        $this->checkPremium();

        $this->checkPrice();
    }

    /**
     * @return int
     */
    public function getMarketSecondaryId(): int
    {
        return $this->market_secondary_id;
    }

    /**
     * @return int
     */
    public function getSecondaryLoanOnSale(): int
    {
        return $this->secondary_loan_on_sale;
    }

    /**
     * @param int $secondary_loan_on_sale
     */
    public function setSecondaryLoanOnSale(int $secondary_loan_on_sale): void
    {
        $this->secondary_loan_on_sale = $secondary_loan_on_sale;
    }

    /**
     * @return Investor
     */
    public function getInvestor(): Investor
    {
        return $this->investor;
    }

    /**
     * @return Loan
     */
    public function getLoan(): Loan
    {
        return $this->loan;
    }

    /**
     * @return Investment
     */
    public function getInvestment(): Investment
    {
        return $this->investment;
    }

    /**
     * @return Originator
     */
    public function getOriginator(): Originator
    {
        return $this->originator;
    }

    /**
     * @return float
     */
    public function getPrincipalForSale(): float
    {
        return $this->principal_for_sale;
    }

    /**
     * @return float
     */
    public function getPremium(): float
    {
        return $this->premium;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getPercentSold(): float
    {
        return $this->percentSold;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    private function checkPrice(): void
    {
        $price = CartLoanHelper::calculatePrice($this->getPremium(), $this->getPrincipalForSale());

        if ($this->getPrice() !== $price) {
            throw new InvalidArgumentException("Indicated price is not equal to calculated");
        }
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

    public function asArray(): array
    {
        $marketSecondaryId = [];
        if ($this->getMarketSecondaryId()) {
            $marketSecondaryId = ['market_secondary_id' => $this->getMarketSecondaryId()];
        }

        return array_merge(
            $marketSecondaryId,
            [
                'investor_id' => $this->getInvestor()->investor_id,
                'loan_id' => $this->getLoan()->loan_id,
                'investment_id' => $this->getInvestment()->investment_id,
                'originator_id' => $this->getOriginator()->originator_id,
                'principal_for_sale' => $this->getPrincipalForSale(),
                'premium' => $this->getPremium(),
                'price' => $this->getPrice(),
                'secondary_loan_on_sale' => $this->getSecondaryLoanOnSale(),
            ]
        );
    }
}
