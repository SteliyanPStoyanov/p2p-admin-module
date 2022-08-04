<?php

namespace Modules\Common\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Loan;
use Modules\Core\Repositories\BaseRepository;

class InvestStrategyRepository extends BaseRepository
{
    protected InvestStrategy $investStrategy;

    public function __construct(InvestStrategy $investStrategy)
    {
        $this->investStrategy = $investStrategy;
    }

    /**
     * @param int|null $limit
     * @param array $where
     * @param array|string[] $order
     * @param bool $comparePortfolioSize
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getAll(
        ?int $limit,
        array $where = [],
        array $order = ['priority' => 'ASC'],
        bool $comparePortfolioSize = false,
        bool $addAmountFromInvestorPlan = false
    ) {

        $select = 'invest_strategy.*';
        $select .= ', (
            select count(investment.investment_id)
            from investment
            join loan ON (
                loan.loan_id = investment.loan_id
                and loan.unlisted = 0
                and loan.status = \'' . Loan::STATUS_ACTIVE . '\'
            )
            where investment.investment_bunch_id in (
                select investment_bunch.investment_bunch_id
                from investment_bunch
                where investment_bunch.invest_strategy_id = invest_strategy.invest_strategy_id
            )
        ) as count';
        if (!$addAmountFromInvestorPlan) {
            $select .= ', (
                select SUM(ii.principal)
                from investor_installment ii
                join loan l on (
                    ii.loan_id = l.loan_id
                    and l.unlisted = 0
                    and l.status = \'' . Loan::STATUS_ACTIVE . '\'
                )
                join investment i on ii.investment_id = i.investment_id
                join investment_bunch ib on (
                    ib.investment_bunch_id = i.investment_bunch_id
                    and ib.invest_strategy_id = invest_strategy.invest_strategy_id
                )
                where ii.paid = 0
            ) as amount';
        } else {
            $select .= ', (COALESCE(invest_strategy.total_invested, 0) - COALESCE(invest_strategy.total_received, 0)) as amount';
        }


        $builder = DB::table('invest_strategy');
        $builder->select(DB::raw($select));

        if ($comparePortfolioSize) {
            $builder->whereRaw('max_portfolio_size > portfolio_size');
        }

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
        $builder->orderBy('invest_strategy.investor_id', 'desc');

        if ($limit != null) {
            $result = $builder->paginate($limit);
            $records = $this->investStrategy->hydrate($result->all());
            $result->setCollection($records);
        } else {
            $result = $builder->get();
        }


        return $result;
    }

    /**
     * [getById description]
     *
     * @param int $investStrategyId [description]
     *
     * @return InvestStrategy|null
     */
    public function getById(int $investStrategyId, int $investorId = null)
    {
        $where = [];
        $where[] = ['invest_strategy_id', '=', $investStrategyId];
        if (!empty($investorId)) {
            $where[] = ['investor_id', '=', $investorId];
        }

        return InvestStrategy::where($where)->first();
    }

    /**
     * @param array $data
     *
     * @return InvestStrategy
     */
    public function create(array $data)
    {
        $investStrategy = new InvestStrategy();
        $investStrategy->fill($data);
        $investStrategy->save();

        return $investStrategy;
    }

    /**
     * @param InvestStrategy $investStrategy
     * @param array $data
     *
     * @return InvestStrategy
     */
    public function edit(
        InvestStrategy $investStrategy,
        array $data,
        array $customProperties = []
    ) {
        $investStrategy->fill($data);

        if (!empty($customProperties)) {
            foreach ($customProperties as $key => $value) {
                $investStrategy->{$key} = $value;
            }
        }

        $investStrategy->save();

        return $investStrategy;
    }

    /**
     * @param InvestStrategy $investStrategy
     *
     * @throws \Exception
     */
    public function delete(InvestStrategy $investStrategy)
    {
        $investStrategy->delete();
    }

    /**
     * @param InvestStrategy $investStrategy
     */
    public function enable(InvestStrategy $investStrategy)
    {
        $investStrategy->enable();
    }

    /**
     * @param InvestStrategy $investStrategy
     */
    public function disable(InvestStrategy $investStrategy)
    {
        $investStrategy->disable();
    }

    /**
     * @param $investStrategyId
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllLoans($investStrategyId, int $limit)
    {
        $builder = DB::table('invest_strategy');
        $builder->select(
            DB::raw(
                '
            i.loan_id ,
            sum(i.amount) as amount,
            sum(i.percent) as percent
        '
            )
        );
        $builder->join(
            'investment_bunch AS ib',
            'invest_strategy.invest_strategy_id',
            '=',
            'ib.invest_strategy_id'
        );
        $builder->join(
            'investment AS i',
            'ib.investment_bunch_id',
            '=',
            'i.investment_bunch_id'
        );

        $builder->whereRaw('ib.invest_strategy_id = ' . $investStrategyId);
        $builder->groupByRaw(
            'invest_strategy.invest_strategy_id,
            ib.investment_bunch_id,
            i.loan_id'
        );

        $builder->orderBy('i.loan_id', 'desc');

        $result = $builder->paginate($limit);
        $records = InvestStrategy::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }
}

