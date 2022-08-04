<?php

namespace Modules\Common\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\InvestStrategyHistory;
use Modules\Common\Entities\Loan;
use Modules\Core\Repositories\BaseRepository;

class InvestStrategyHistoryRepository extends BaseRepository
{
    protected InvestStrategyHistory $investStrategyHistory;

    public function __construct(InvestStrategyHistory $investStrategyHistory)
    {
        $this->investStrategyHistory = $investStrategyHistory;
    }

    /**
     * @param int|null $limit
     * @param array $where
     * @param array|string[] $order
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getAll(
        ?int $limit,
        array $where = [],
        array $order = []
    ) {
        $builder = DB::table('invest_strategy_history');
        $builder->select(
            DB::raw(
                '
            invest_strategy_history.*
        '
            )
        );

        if (!empty($where['loan_payment_status'])) {
            $builder->whereJsonContains('loan_payment_status->payment_status', $where['loan_payment_status']);
            unset($where['loan_payment_status']);
        }
        if (!empty($where['loan_type'])) {
            $builder->whereJsonContains('loan_type->type', $where['loan_type']);
            unset($where['loan_type']);
        }

        if (!empty($where)) {
            $builder->where($where);
        }

        if (!empty($order['loan_type'])) {
            $builder->orderBy('loan_type->type', $order['loan_type']);
            unset($order['loan_type']);
        }

        if (!empty($order['loan_payment_status'])) {
            $builder->orderBy('loan_payment_status->payment_status', $order['loan_payment_status']);
            unset($order['loan_payment_status']);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $builder->orderBy('invest_strategy_history.archived_at', 'desc');

        if ($limit != null) {
            $result = $builder->paginate($limit);
            $records = $this->investStrategyHistory->hydrate($result->all());
            $result->setCollection($records);
        } else {
            $result = $builder->get();
        }

        return $result;
    }
}

