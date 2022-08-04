<?php

namespace Modules\Common\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Loan;
use Modules\Core\Repositories\BaseRepository;

class InvestmentRepository extends BaseRepository
{

    /**
     * @param int|null $limit
     * @param array $where
     * @param array|string[] $order
     * @param bool $showDeleted
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        ?int $limit,
        array $where = [],
        array $order = ['loan.status' => 'ASC'],
        bool $showDeleted = false
    ) {
        $builder = DB::table('investment');
        $builder->select(
            DB::raw('
            investment.investment_id,
            investment.loan_id,
            investment.active,
            investment.amount,
            investment.created_at as investment_created_at,
            loan.loan_id as loanId,
            loan.lender_issue_date,
            loan.created_at as loan_created_at,
            loan.originator_id,
            loan.country_id,
            loan.interest_rate_percent,
            loan.remaining_principal as loan_remaining_principal,
            loan.period,
            loan.amount_available,
            loan.payment_status,
            loan.type,
            loan.final_payment_date,
            loan.status,
            loan.unlisted,
            country.name as country_name,
            originator.name as originator_name
        '));

        $builder->addSelect(
            DB::raw(
                self::getSqlInvestorInstalment(($where['loan_unlisted'] ?? 0))
            )
        );

        $builder->join('loan', 'loan.loan_id', '=', 'investment.loan_id');
        $builder->join('country', 'country.country_id', '=', 'loan.country_id');
        $builder->join('originator', 'originator.originator_id', '=', 'loan.originator_id');

        $builder = $this->cardWhereBuilder($builder, $where);
        if (!empty($where['card'])) {
            unset($where['card']);
        }

        if (!empty($where['loan.payment_status'])) {
            $builder->whereIn('loan.payment_status', $where['loan.payment_status']);
            unset($where['loan.payment_status']);
        }

        if (!empty($where['invested_amount'])) {
            $builder = self::getInvestedAmountSql($builder, $where['invested_amount'] , ($where['loan_unlisted'] ?? 0));
            unset($where['invested_amount']);
        }

        if (!empty($where['loan_unlisted'])) {
            unset($where['loan_unlisted']);
        }
        if (!empty($where['loan.final_payment_status'])) {
            $builder->whereIn('loan.final_payment_status', $where['loan.final_payment_status']);
            unset($where['loan.final_payment_status']);
        }

        if (!empty($where['loan.final_payment_status'])) {
            $builder->whereIn('loan.final_payment_status', $where['loan.final_payment_status']);
            unset($where['loan.final_payment_status']);
        }

        if (isset($where['investment.parent_id']) && $where['investment.parent_id'] == 'IS NOT NULL' ) {
            $builder->whereNotNull('investment.parent_id');
            unset($where['investment.parent_id']);
        }

        if (isset($where['investment.parent_id']) && $where['investment.parent_id'] == 'IS NULL' ) {
            $builder->whereNull('investment.parent_id');
            unset($where['investment.parent_id']);
        }

        if (!empty($where)) {
            if (
                array_search('loan.unlisted', array_column($where, '0'))
                && array_key_exists('loan.payment_status', $order)
            ) {
                $direction = $order['loan.payment_status'];
                unset($order['loan.payment_status']);
                $order['loan.final_payment_status'] = $direction;
            }

            $builder->where($where);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $builder->groupBy(
            'investment.investment_id',
            'loan.loan_id',
            'country.name',
            'originator.name'
        );

        if ($limit != null) {
            $result = $builder->paginate($limit);
            $records = Investment::hydrate($result->all());
            $result->setCollection($records);
        } else {
            $result = Investment::hydrate($builder->get()->all());
        }

        return $result;
    }

    /**
     * @param array $data
     *
     * @return Investment
     */
    public function create(array $data): Investment
    {
        $investment = new Investment();
        $investment->fill($data);
        $investment->save();

        return $investment;
    }

    /**
     * @param int $investorId
     * @param string|null $status
     * @return int
     */
    public function getInvestmentsCount(int $investorId, string $status = null)
    {
        if (empty($status)) {
            return Investment::where('investor_id', '=', $investorId)->count();
        }

        $unlisted = ($status === Loan::STATUS_ACTIVE ? 0 : 1);

        $builder = DB::table('investment');
        $builder->select(DB::raw('investment.*'));
        $builder->join(
            'loan',
            'loan.loan_id',
            '=',
            'investment.loan_id',
        );
        $builder->where('investment.investor_id', '=', $investorId);
        $builder->where('loan.unlisted', '=', $unlisted);
        $builder->where('investment.active', '=', Investment::STATUS_ACTIVE);

        return $builder->count();
    }

    /**
     * @param $builder
     * @param $where
     * @return mixed
     */
    public function cardWhereBuilder($builder, $where)
    {
        if (!empty($where['card'])) {
            if ($where['card'] == 'listed') {
                $builder->join(
                    'market_secondary',
                    'market_secondary.investment_id',
                    '=',
                    'investment.investment_id'
                );

                $builder->whereRaw('market_secondary.active = 1');
            }

            if ($where['card'] == 'exclude') {
                $builder->leftJoin(
                    'market_secondary',
                    'market_secondary.investment_id',
                    '=',
                    'investment.investment_id'
                );
                $builder->whereRaw('market_secondary.investment_id IS NULL');
            }
        }

        return $builder;
    }

     /**
     * @param int $loanStatus
     * @return string
     */
    public function getSqlInvestorInstalment(int $loanStatus): string
    {
        $table = 'investor_installment';
        if ($loanStatus == 1) {
            $table = 'investor_installment_history';
        }

        return '(SELECT COALESCE(SUM(ii.principal), 0) FROM ' . $table . ' as ii
        WHERE ii.investment_id = investment.investment_id AND ii.paid = 0) AS invested_sum,
        (SELECT COALESCE(SUM(ii.principal + ii.accrued_interest + ii.interest + ii.late_interest), 0)
        FROM ' . $table . ' as ii WHERE ii.investment_id = investment.investment_id
        AND ii.paid = 1) AS received_amount';
    }

    /**
     * @param $builder
     * @param array $amounts
     * @param int $loanStatus
     * @return mixed
     */
    public function getInvestedAmountSql($builder, array $amounts ,int $loanStatus)
    {

        $table = 'investor_installment';
        if ($loanStatus == 1) {
            $table = 'investor_installment_history';
        }

        foreach ($amounts as $key => $amount) {
            $builder->whereRaw(
                '(SELECT COALESCE(SUM(ii.principal), 0)
        FROM '.$table.' as ii
        WHERE ii.investment_id = investment.investment_id
        AND ii.paid = '.$loanStatus.') ' . (($key == 'to') ? '<= ' : '>= ') . $amount . ' '
            );
        }

        return $builder;
    }
}
