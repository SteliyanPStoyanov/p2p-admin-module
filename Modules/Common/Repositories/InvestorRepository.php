<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\ChangeLog;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Transaction;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Core\Repositories\BaseRepository;

class InvestorRepository extends BaseRepository
{
    /**
     * [getById description]
     *
     * @param int $investorId [description]
     *
     * @return Investor|null
     */
    public function getById(int $investorId)
    {
        return Investor::where(
            'investor_id',
            '=',
            $investorId
        )->first();
    }

    /**
     * @param string $email
     *
     * @return mixed
     */
    public function getByEmail(string $email)
    {
        return Investor::where(
            'email',
            '=',
            $email
        )->first();
    }

    /**
     * @param array $data
     *
     * @return Investor
     */
    public function create(array $data)
    {
        $investor = new Investor();
        $investor->fill($data);
        $investor->save();

        return $investor;
    }

    /**
     * @param Investor $investor
     * @param array $data
     *
     * @return mixed
     */
    public function update(Investor $investor, array $data)
    {
        $investor->fill($data);
        $investor->save();
        return $investor;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function isExists(string $email)
    {
        return (Investor::where(
                [
                    'email' => $email,
                ]
            )->count()) > 0;
    }

    /**
     * @param int $investorId
     * @param array $data
     *
     * @return mixed
     */
    public function investorUpdate(int $investorId, array $data)
    {
        return Investor::where('investor_id', '=', $investorId)->update($data);
    }

    /**
     * @param int $limit
     * @param array $where
     * @param array|string[] $order
     * @param bool $showDeleted
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['active' => 'DESC', 'investor_id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $where = $this->checkForDeleted($where, $showDeleted, 'investor');

        $builder = DB::table('investor');
        $builder->select(DB::raw('
            investor.*,
            wallet.total_amount,
            wallet.uninvested,
            wallet.currency_id
        '));
        $builder->leftJoin('wallet', 'wallet.investor_id', '=', 'investor.investor_id');

        if (!empty($where)) {
            $builder->where($where);
        }


        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $result = $builder->paginate($limit);
        $records = Investor::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * @return string[]
     */
    public function getStatuses(): array
    {
        return [
            Investor::INVESTOR_STATUS_UNREGISTERED,
            Investor::INVESTOR_STATUS_REGISTERED,
            Investor::INVESTOR_STATUS_AWAITING_DOCUMENTS,
            Investor::INVESTOR_STATUS_VERIFIED,
            Investor::INVESTOR_STATUS_REJECTED_VERIFICATION,
            Investor::INVESTOR_STATUS_AWAITING_VERIFICATION
        ];
    }

    /**
     * @param array $days
     * @param string $status
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllDaysRecall(
        array $days,
        string $status
    ) {
        $builder = DB::table('investor');
        $builder->select(
            DB::raw('investor.*')
        );

        $builder->where('investor.status', '=', $status);

        $startOfDay = Carbon::now()->startOfDay()->toDateTimeString();
        $endOfDay = Carbon::now()->endOfDay()->toDateTimeString();

        $builder->whereRaw(
            "(investor." . $status . "_recall_at IS NULL or
            (investor." . $status . "_recall_at not between '" . $startOfDay . "' and '" . $endOfDay . "'))
            "
        );

        $builder->where(
            function ($query) use ($days) {
                foreach ($days as $day) {
                    $query->orWhereDate('investor.created_at', Carbon::now()->subDays($day)->toDateString());
                }
            }
        );

        return $builder->orderBy('investor.investor_id', 'asc');
    }

    /**
     * @param string $hash
     *
     * @return mixed
     */
    public function getByHash(string $hash)
    {
        return Investor::where(
            'referral_hash',
            '=',
            $hash
        )->first();
    }

    /**
     * @param int $limit
     * @param array $where
     * @param array|string[] $order
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getReferrals(
        int $limit,
        array $where = []
    ) {
        $builder = DB::table('investor', 'a');
        $builder->select(
            DB::raw(
                "concat_ws(' ', a.first_name, a.middle_name, a.last_name) as investor_names,
                       a.email,
                       a.investor_id,
                       count(b.investor_id) as referrals_count,
                       sum(w.deposit) as referrals_deposit,
                       sum(w.invested) as invested_total"
            )
        );

        $builder->join('investor as b', 'b.referral_id', '=', 'a.investor_id');
        $builder->join('wallet as w', 'w.investor_id', '=', 'b.investor_id');

        $builder->groupBy('a.investor_id');

        if (!empty($where['deposit_from'])) {
            $builder->havingRaw('sum(w.deposit) >= ' . $where['deposit_from']);
            unset($where['deposit_from']);
        }
        if (!empty($where['deposit_to'])) {
            $builder->havingRaw('sum(w.deposit) <= ' . $where['deposit_to']);
            unset($where['deposit_to']);
        }
        if (!empty($where['invested_from'])) {
            $builder->havingRaw('sum(w.invested) >= ' . $where['invested_from']);
            unset($where['invested_from']);
        }
        if (!empty($where['invested_to'])) {
            $builder->havingRaw('sum(w.invested) <= ' . $where['invested_to']);
            unset($where['invested_to']);
        }
        if (!empty($where['referrals_count_from'])) {
            $builder->havingRaw('count(b.investor_id) >= ' . $where['referrals_count_from']);
            unset($where['referrals_count_from']);
        }
        if (!empty($where['referrals_count_to'])) {
            $builder->havingRaw('count(b.investor_id) <= ' . $where['referrals_count_to']);
            unset($where['referrals_count_to']);
        }

        if (!empty($where)) {
            $builder->where($where);
        }

        $builder->orderBy('a.investor_id', 'ASC');

        if ($limit != 0) {
            $result = $builder->paginate($limit);
            $records = Investor::hydrate($result->all());
            $result->setCollection($records);
        } else {
            $result = $builder->get();
        }

        return $result;
    }

    /**
     * @param int $limit
     * @param array $where
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function investorTransactions(
        int $limit,
        int $investorId,
        array $where = [],
        bool $addLoanId = true
    ) {
        $builder = DB::table('transaction', 't');

        $builder->select(
            DB::raw(
                '
                   t.transaction_id,
                   t.created_at,
                   t.amount,
                   t.type,
                   t.details,
                   ' . (true === $addLoanId ? 't.loan_id,' : '') . '
                    (CASE
                        WHEN t.direction = \'' . Transaction::DIRECTION_OUT . '\'
                            THEN originator.name
                        WHEN t.direction = \'' . Transaction::DIRECTION_IN . '\'
                            THEN CONCAT_WS(\' \', investor.first_name, investor.middle_name, investor.last_name)
                    END) AS from,
                    (CASE
                        WHEN t.direction = \'' . Transaction::DIRECTION_IN . '\'
                            THEN originator.name
                        WHEN t.direction = \'' . Transaction::DIRECTION_OUT . '\'
                            THEN CONCAT_WS(\' \', investor.first_name, investor.middle_name, investor.last_name)
                    END) AS to
                '
            )
        );

        $builder->leftJoin(
            'originator',
            'originator.originator_id',
            '=',
            't.originator_id'
        );
        $builder->leftJoin(
            'investor',
            'investor.investor_id',
            '=',
            't.investor_id'
        );

        if (!empty($where)) {
            $builder->where($where);
        }

        $builder->whereRaw('t.investor_id = ' . $investorId);

        $builder->orderBy('t.created_at', 'DESC');

        if ($limit != 0) {
            $result = $builder->paginate($limit);

            $records = Transaction::hydrate($result->all());
            $result->setCollection($records);
        } else {
            $result = $builder->get();
        }

        return $result;
    }

    /**
     * @param int $limit
     * @param int $investorId
     * @param array $where
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function investorChangeLogs(
        int $limit,
        int $investorId,
        array $where = []
    ) {
        $builder = DB::table('change_log', 'cl');
        $builder->select(
            DB::raw(
                "
                cl.created_at,
                cl.key,
                cl.old_value,
                cl.new_value,
                cl.created_by,
                cl.created_by_type
                "
            )
        );

        if (!empty($where)) {
            $builder->where($where);
        }

        $builder->whereRaw('cl.investor_id = ' . $investorId);

        $builder->orderBy('cl.created_at', 'DESC');

        if ($limit != 0) {
            $result = $builder->paginate($limit);
            $records = ChangeLog::hydrate($result->all());

            $result->setCollection($records);
        } else {
            $result = $builder->get();
        }

        return $result;
    }

    /**
     * @param int $bonusDaysCount
     * @param float $minAmount
     * @param int $percent
     *
     * @return array
     */

    public function getInvestorWithBonuses(
        int $bonusDaysCount,
        float $minAmount,
        int $percent,
        $date
    ): array {
        $result = DB::select(
            DB::raw(
                "
            select res.*
                from (
                select
                    i.referral_id as parent_id,
                    i.investor_id as child_id,
                    (
                        select sum(t.amount)
                        from transaction as t
                        where t.investor_id = i.investor_id and t.type = '" . Transaction::TYPE_INVESTMENT . "'
                    ) as invested,
                    DATE(i.created_at) as registration_date,
                    (CAST('$date' AS date) - CAST(i.created_at AS date)) as daysPast
                from investor as i
                where i.referral_id is not null
            ) as res
            left join investor_bonus ib on (
                ib.investor_id  = res.parent_id
                and ib.from_investor_id  = res.child_id
            )
            where
                res.invested is not null
                and res.invested >= " . $minAmount . "
                and res.daysPast = " . $bonusDaysCount . "
                and ib.investor_bonus_id is NULL
        "
            )
        );
        if (empty($result)) {
            return [];
        }

        $investorForTasks = [];
        foreach ($result as $row) {
            $bonusAmountForInvestor = $this->calculateBonus($percent, $row->invested);
            if ($bonusAmountForInvestor >= Setting::BONUS_MAX_AMOUNT) {
                $bonusAmountForInvestor = Setting::BONUS_MAX_AMOUNT;
            }

            $investorForTasks[] = (object)[
                'investor_id' => $row->parent_id,
                'amount' => $bonusAmountForInvestor,
                'registration_date' => $row->registration_date,
                'child_id' => $row->child_id,
            ];
        }

        return $investorForTasks;
    }

    public function calculateBonus(int $percent, int $depositAmount)
    {
        return Calculator::round(($percent / 100) * $depositAmount);
    }

    public function getInvestorsBonuses(): array
    {
        $builder = DB::table('investor_bonus', 'ib');
        $builder->select(
            DB::raw(
                '
            ib.investor_id,
            ib.amount,
            ib.investor_bonus_id,
            w.wallet_id,
            w.currency_id,
            ba.bank_account_id
        '
            )
        );
        $builder->join('wallet as w', 'ib.investor_id', '=', 'w.investor_id');
        $builder->leftJoin(
            'bank_account as ba',
            function ($join) {
                $join->on('ib.investor_id', '=', 'ba.investor_id');
                $join->where('ba.default', '=', '1');
                $join->where('ba.active', '=', '1');
                $join->where('ba.deleted', '=', '0');
            }
        );
        $builder->whereRaw('ib.handled = 0');

        $result = $builder->get()->all();
        if (empty($result)) {
            return [];
        }

        $investorForTasks = [];

        foreach ($result as $row) {
            $investorForTasks[] = (object)[
                'investor_id' => $row->investor_id,
                'amount' => $row->amount,
                'investor_bonus_id' => $row->investor_bonus_id,
                'wallet_id' => $row->wallet_id,
                'currency_id' => $row->currency_id,
                'bank_account_id' => $row->bank_account_id,
            ];
        }
        return $investorForTasks;
    }

    public function investorComment(array $data)
    {
        return Investor::where('investor_id', '=', intval($data['investor_id']))->update($data);
    }

    /**
     * @param int $investorId
     * @return mixed
     */
    public function getInvestorReferrals(int $investorId)
    {
        $builder = DB::table('investor', 'i');
        $builder->select(
            DB::raw(
                "
            concat_ws(' ', i.first_name, i.middle_name, i.last_name) as referral_names,
            i.investor_id
        "
            )
        );
        $builder->whereRaw('i.referral_id = ' . $investorId);

        $result = $builder->get();

        return Investor::hydrate($result->all());
    }
}

