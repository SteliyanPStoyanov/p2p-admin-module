<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Services\InvestmentService;

class AutoInvestService extends CommonService
{
    /**
     * Get array with investors and their highest strategies
     * Format: [investor_id] => [strategy1]
     *
     * @param  int|null $investorId
     * @param  int|null $investStrategyId
     * @return array
     */
    public function getInvestorsWithHighestStrategy(
        int $investorId = null,
        int $investStrategyId = null
    ): array
    {
        $minAmount = \SettingFacade::getMinAmountForInvest();

        $where = [];
        $where[] = 'is2.active = 1';
        $where[] = 'is2.agreed = 1';
        $where[] = 'is2.deleted = 0';
        $where[] = '(is2.max_portfolio_size - is2.portfolio_size) > ' . $minAmount;
        if (!empty($investorId) && $investorId > 0) {
            $where[] = "is2.investor_id = " . (int) $investorId;
        }
        if (!empty($investStrategyId) && $investStrategyId > 0) {
            $where[] = "is2.invest_strategy_id = " . (int) $investStrategyId;
        }

        $results = DB::select(
            DB::raw("
                select is3.*
                from invest_strategy is3
                join (
                    select
                        is2.investor_id as unique_investor_id,
                        MIN(is2.priority) as min_priority
                    from invest_strategy is2
                    join investor i on (i.investor_id = is2.investor_id and i.active = 1 and i.deleted = 0)
                    join wallet w on w.investor_id = i.investor_id  and w.uninvested > " . $minAmount . "
                    where " . implode(" AND ", $where) . "
                    group by is2.investor_id
                ) as investorIdAndPriority on (
                    investorIdAndPriority.unique_investor_id = is3.investor_id
                    and investorIdAndPriority.min_priority = is3.priority
                    and is3.active = 1
                    and is3.agreed = 1
                    and is3.deleted = 0
                );
            ")
        );

        if (empty($results)) {
            return [];
        }

        $sortedData = [];
        array_walk($results, function ($value, $key) use (&$sortedData) {
            $sortedData[$value->investor_id] = (InvestStrategy::hydrate([(array) $value]))->first();
        });

        return $sortedData;
    }

    /**
     * Get array with investors and their strategies
     * Format: [investor_id] => [strategy1, strategy2]
     * @param  int|null $investorId
     * @param  int|null $investStrategyId
     * @return array
     */
    public function getInvestorsStrategies(
        int $investorId = null,
        int $investStrategyId = null
    ): array
    {
        $where = [];
        $where[] = 'ist.active = 1';
        $where[] = 'ist.agreed = 1';
        $where[] = 'ist.deleted = 0';
        $where[] = 'ist.portfolio_size < ist.max_portfolio_size';
        $where[] = 'w.uninvested > 0';
        if (!empty($investorId) && $investorId > 0) {
            $where[] = "ist.investor_id = " . (int) $investorId;
        }
        if (!empty($investStrategyId) && $investStrategyId > 0) {
            $where[] = "ist.invest_strategy_id = " . (int) $investStrategyId;
        }

        $results = DB::select(
            DB::raw("
                SELECT ist.*
                FROM invest_strategy ist
                JOIN investor i ON (
                    i.investor_id = ist.investor_id
                    AND i.active = 1
                    AND i.deleted = 0
                )
                JOIN wallet w ON w.wallet_id = ist.wallet_id
                WHERE " . implode(" AND ", $where) . "
                ORDER BY
                    ist.investor_id ASC,
                    ist.priority ASC
            ")
        );

        if (empty($results)) {
            return [];
        }

        $sortedData = [];
        array_walk($results, function ($value, $key) use (&$sortedData) {
            $sortedData[$value->investor_id] = (InvestStrategy::hydrate([(array) $value]))->get();
        });

        return $sortedData;
    }

    /**
     * Get highest count of strategies for 1 investor
     * @param  int|null $investorId
     * @return int
     */
    public function getMaxInvestStrategiesCount(
        int $investorId = null,
        bool $skipActiveDeleteCheck = false
    ): int
    {
        $where = [];
        $where[] = 'ist.agreed = 1';
        if (!empty($investorId)) {
            $where[] = "ist.investor_id = " . $investorId;
        }
        if (!$skipActiveDeleteCheck) {
            $where[] = 'ist.active = 1';
            $where[] = 'ist.deleted = 0';
        }

        $result = DB::select(
            DB::raw("
                SELECT MAX(res.count) as count
                FROM (
                    SELECT ist.investor_id, COUNT(ist.*) as count
                    FROM invest_strategy ist
                    JOIN investor i ON (
                        i.investor_id = ist.investor_id
                        AND i.active = 1
                        AND i.deleted = 0
                    )
                    WHERE " . implode(" AND ", $where) . "
                    GROUP BY ist.investor_id
                ) as res
            ")
        );

        return ($result[0]->count ?? 0);
    }

    /**
     * Get count of investors which have invest strategies
     * @param  int|null $investorId
     * @return int
     */
    public function getDistinctInvestorsCountWithStrategies(
        int $priority = null
    ): int
    {
        $where = [];
        $where[] = 'ist.agreed = 1';
        $where[] = 'ist.active = 1';
        $where[] = 'ist.deleted = 0';
        if (!empty($priority)) {
            $where[] = "ist.priority = " . $priority;
        }

        $result = DB::select(
            DB::raw("
                SELECT COUNT(res.investor_id) as count
                FROM (
                    SELECT DISTINCT ist.investor_id
                    FROM invest_strategy ist
                    WHERE " . implode(" AND ", $where) . "
                ) as res
            ")
        );

        return ($result[0]->count ?? 0);
    }
}
