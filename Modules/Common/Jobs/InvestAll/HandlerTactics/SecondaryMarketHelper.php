<?php
declare(strict_types=1);

namespace Modules\Common\Jobs\InvestAll\HandlerTactics;

use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Portfolio;

trait SecondaryMarketHelper
{
    public function getInvestorBlockedQuality(Investor $investor): Portfolio
    {
        return $investor->getQualityPortfolioBlockedForUpdate();
    }

    public function getInvestorBlockedMaturity(Investor $investor): Portfolio
    {
        return $investor->getMaturityPortfolioBlockedForUpdate();
    }
}
