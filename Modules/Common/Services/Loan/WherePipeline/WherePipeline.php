<?php

declare(strict_types=1);

namespace Modules\Common\Services\Loan\WherePipeline;

use Illuminate\Database\Query\Builder;
use Illuminate\Pipeline\Pipeline;
use Modules\Common\Services\Loan\WherePipeline\Handlers\AmountAvailableMax;
use Modules\Common\Services\Loan\WherePipeline\Handlers\AmountAvailableMin;
use Modules\Common\Services\Loan\WherePipeline\Handlers\Country;
use Modules\Common\Services\Loan\WherePipeline\Handlers\CreatedAtFrom;
use Modules\Common\Services\Loan\WherePipeline\Handlers\CreatedAtTo;
use Modules\Common\Services\Loan\WherePipeline\Handlers\IncludeInvested;
use Modules\Common\Services\Loan\WherePipeline\Handlers\InterestRateMax;
use Modules\Common\Services\Loan\WherePipeline\Handlers\InterestRateMin;
use Modules\Common\Services\Loan\WherePipeline\Handlers\LoanPaymentStatus;
use Modules\Common\Services\Loan\WherePipeline\Handlers\LoanPeriodMax;
use Modules\Common\Services\Loan\WherePipeline\Handlers\LoanPeriodMin;
use Modules\Common\Services\Loan\WherePipeline\Handlers\LoanStatus;
use Modules\Common\Services\Loan\WherePipeline\Handlers\LoanType;
use Modules\Common\Services\Loan\WherePipeline\Handlers\Originator;

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
        Builder $builder,
        array $data,
        int $investorId = 0,
        bool $ignoreFilters = false,
        bool $isAdmin = false
    ): DataWrapper
    {
        $dataWrapper = new DataWrapper($builder, $data, $investorId, $isAdmin);
        $pipeline = app(Pipeline::class);

        $filters = [];
        if (!$ignoreFilters) {
            $filters = [
                new LoanPaymentStatus(),
                new InterestRateMin(),
                new InterestRateMax(),
                new LoanPeriodMin(),
                new LoanPeriodMax(),
                new LoanType(),
                new AmountAvailableMin(),
                new IncludeInvested(),
                new AmountAvailableMax(),
                new CreatedAtFrom(),
                new CreatedAtTo(),
                new Country(),
                new Originator(),
                new LoanStatus(),
            ];
        }

        return $pipeline->send($dataWrapper)->through($filters)->thenReturn();
    }
}
