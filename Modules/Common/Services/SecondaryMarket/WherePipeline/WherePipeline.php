<?php

declare(strict_types=1);

namespace Modules\Common\Services\SecondaryMarket\WherePipeline;

use Illuminate\Pipeline\Pipeline;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\AmountAvailableMax;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\AmountAvailableMin;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\Country;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\CreatedAtFrom;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\CreatedAtTo;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\DiscountPremiumMax;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\DiscountPremiumMin;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\IncludeInvested;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\InterestRateMax;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\InterestRateMin;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\LoanPaymentStatus;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\LoanPeriodMax;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\LoanPeriodMin;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\LoanStatus;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\LoanType;
use Modules\Common\Services\SecondaryMarket\WherePipeline\Handlers\Originator;
use Illuminate\Database\Eloquent\Builder;

/**
 * Here we are adding the where conditions, where every condition has its own class representation
 */
class WherePipeline
{
    /**
     * @param Builder $builder
     * @param array $data
     * @param int $investorId - used for skipping loans already invested in
     * @param bool $ignoreFilters
     * @param bool $isAdmin
     * @return DataWrapper
     */
    public static function run(
        Builder $marketSecondary,
        array $data,
        int $investorId = 0,
        bool $ignoreFilters = false,
        bool $isAdmin = false
    ): DataWrapper
    {
        $dataWrapper = new DataWrapper($marketSecondary, $data, $investorId, $isAdmin);
        $pipeline = app(Pipeline::class);

        $filters = [];
        if (!$ignoreFilters) {
            $filters = [
                new InterestRateMin(),
                new InterestRateMax(),
                new LoanPeriodMin(),
                new LoanPeriodMax(),
                new LoanType(),
                new AmountAvailableMin(),
                new AmountAvailableMax(),
                new CreatedAtFrom(),
                new CreatedAtTo(),
                new LoanStatus(),
                new DiscountPremiumMin(),
                new DiscountPremiumMax(),
            ];
        }

        return $pipeline->send($dataWrapper)->through($filters)->thenReturn();
    }
}
