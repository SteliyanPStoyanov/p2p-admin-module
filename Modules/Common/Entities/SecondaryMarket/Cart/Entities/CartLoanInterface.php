<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart\Entities;


use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;

interface CartLoanInterface
{
    /**
     * @return int
     */
    public function getCartLoanId(): int;

    /**
     * @return int
     */
    public function getCartId(): int;

    /**
     * @return Loan
     */
    public function getLoan(): Loan;

    /**
     * @return Investment
     */
    public function getInvestment(): Investment;

    /**
     * @return Originator
     */
    public function getOriginator(): Originator;

    /**
     * @return float
     */
    public function getPrincipalForSale(): float;

    /**
     * @return float
     */
    public function getPremium(): float;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @return string
     */
    public function getFilters(): string;

    /**
     * @return bool
     */
    public function isStatus(): bool;

    /**
     * @return string
     */
    public function getReason(): string;

    public function setCartLoanId(int $cartLoanId): void;
}
