<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart\Entities;

use Modules\Common\Entities\MarketSecondary;

interface CartLoanBuyerInterface
{
    public function getMarketSecondary(): MarketSecondary;
}
