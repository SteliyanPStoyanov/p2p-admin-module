<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart\Entities;

use Modules\Common\Entities\Investor;

interface CartInterface
{
    /**
     * @return int
     */
    public function getCartId(): int;

    /**
     * @return Investor
     */
    public function getInvestor(): Investor;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return CartLoansCollection
     */
    public function getLoans(): CartLoansCollection;
}
