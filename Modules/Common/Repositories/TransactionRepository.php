<?php

namespace Modules\Common\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Transaction;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Core\Repositories\BaseRepository;

class TransactionRepository extends BaseRepository
{
    /**
     * @param int $limit
     * @param array $where
     * @param array $whereCase
     * @param array|string[] $order
     * @param bool $showDeleted
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $limit,
        array $where = [],
        array $whereCase = [],
        array $order = ['active' => 'DESC', 'transaction_id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $where = $this->checkForDeleted($where, $showDeleted, 'transaction');
        $builder = DB::table('transaction');

        $builder->select(
            DB::raw(
                '
            transaction.*,
            CASE
                WHEN
                    transaction.secondary_market_id IS NULL AND transaction.direction = \'' . Transaction::DIRECTION_OUT . '\'
                THEN
                    originator.name
                WHEN
                    transaction.secondary_market_id IS NULL AND transaction.direction = \'' . Transaction::DIRECTION_IN . '\'
                THEN
                    CONCAT_WS(\' \', investor.first_name, investor.middle_name, investor.last_name)
                WHEN
                    transaction.secondary_market_id IS NOT NULL AND transaction.type = \'' . Transaction::TYPE_SECONDARY_MARKET_BUY . '\'
                THEN
                    CONCAT_WS(\' \', investor.first_name, investor.middle_name, investor.last_name)
                WHEN
                    transaction.secondary_market_id IS NOT NULL AND transaction.type = \'' . Transaction::TYPE_SECONDARY_MARKET_SELL . '\'
                THEN
                    CONCAT_WS(\' \', investor.first_name, investor.middle_name, investor.last_name)
            END AS from,
            CASE
                WHEN
                    transaction.secondary_market_id IS NULL AND transaction.direction = \'' . Transaction::DIRECTION_IN . '\'
                THEN
                    originator.name
                WHEN
                    transaction.secondary_market_id IS NULL AND transaction.direction = \'' . Transaction::DIRECTION_OUT . '\'
                THEN
                    CONCAT_WS(\' \', investor.first_name, investor.middle_name, investor.last_name)
                WHEN
                    transaction.secondary_market_id IS NOT NULL AND transaction.type = \'' . Transaction::TYPE_SECONDARY_MARKET_BUY . '\'
                THEN
                    CONCAT_WS(\' \', ca.first_name, ca.middle_name, ca.last_name)
                WHEN
                    transaction.secondary_market_id IS NOT NULL AND transaction.type = \'' . Transaction::TYPE_SECONDARY_MARKET_SELL . '\'
                THEN
                    CONCAT_WS(\' \', ca.first_name, ca.middle_name, ca.last_name)
            END AS to
            '
            )
        );
        $builder->leftJoin(
            'originator',
            'originator.originator_id',
            '=',
            'transaction.originator_id'
        );

        $builder->leftJoin(
            'investor',
            'investor.investor_id',
            '=',
            'transaction.investor_id'
        );

        $builder->leftJoin(
            'investor as ca',
            'ca.investor_id',
            '=',
            'transaction.counteragent'
        );

        if (!empty($whereCase)) {
            if (!empty($whereCase['from'])) {
                $builder->whereRaw(
                    '
                (CASE
                    WHEN transaction.direction = \'' . Transaction::DIRECTION_OUT . '\'
                        THEN originator.name ILIKE CONCAT(\'%\', ?::text,\'%\')
                    WHEN transaction.direction = \'' . Transaction::DIRECTION_IN . '\'
                        THEN CONCAT_WS(\' \', investor.first_name, investor.middle_name, investor.last_name) ILIKE CONCAT(\'%\', ?::text,\'%\')
                END)
                ',
                    [$whereCase['from'], $whereCase['from']]
                );
            }
            if (!empty($whereCase['to'])) {
                $builder->whereRaw(
                    '
                (CASE
                    WHEN transaction.direction = \'' . Transaction::DIRECTION_IN . '\'
                        THEN originator.name ILIKE CONCAT(\'%\', ?::text,\'%\')
                    WHEN transaction.direction = \'' . Transaction::DIRECTION_OUT . '\'
                        THEN CONCAT_WS(\' \', investor.first_name, investor.middle_name, investor.last_name) ILIKE CONCAT(\'%\', ?::text,\'%\')
                END)
                ',
                    [$whereCase['to'], $whereCase['to']]
                );
            }
        }

        if (empty($where['transaction_type'][0])) {
            unset($where['transaction_type']);
        }

        if (!empty($where['transaction_type'][0])) {
            $builder->whereIn(
                'transaction.type',
                $where['transaction_type']
            );
            unset($where['transaction_type']);
        }

        if (!empty($where)) {
            $builder->where($where);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $builder->groupBy(
            'transaction.transaction_id',
            'originator.name',
            'investor.first_name',
            'investor.middle_name',
            'investor.last_name',
            'ca.first_name',
            'ca.middle_name',
            'ca.last_name'
        );

        $result = $builder->paginate($limit);
        $records = Transaction::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }


    /**
     * @param int $investorId
     * @param string $dateFrom
     * @param string $dateTo
     * @param array $filterData
     * @param int|null $limit
     * @param int|null $page
     * @return array|LengthAwarePaginator
     */
    public function transactionList(
        int $investorId,
        string $dateFrom,
        string $dateTo,
        array $filterData,
        ?int $limit,
        int $page = 1
    ) {
        $dateFrom .= ' 00:00:00';
        $dateTo .= ' 23:59:59';

        $queries = [];
        foreach ($filterData as $key) {
            if (!is_string($key)) {
                continue;
            }
            if (
                in_array($key, Transaction::getAmountTypes()) &&
                $key != Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                $key != Transaction::TYPE_SECONDARY_MARKET_BUY &&
                $key != Transaction::TYPE_SECONDARY_MARKET_SELL
            ) {
                $queries[] = $this->getQueryForTransactionList(
                    $investorId,
                    $key,
                    'amount',
                    [$key],
                    $dateFrom,
                    $dateTo
                );
                continue;
            }

            if (preg_match('/^(repayment)\_(principal|interest|late_interest)/', $key, $m)) {
                $queries[] = $this->getQueryForTransactionList(
                    $investorId,
                    $m[1],
                    $m[2],
                    [
                        Transaction::TYPE_REPAYMENT,
                        Transaction::TYPE_INSTALLMENT_REPAYMENT,
                    ],
                    $dateFrom,
                    $dateTo
                );
                continue;
            }

            if (preg_match('/^(early_repayment)\_(principal|interest|late_interest)/', $key, $m)) {
                $queries[] = $this->getQueryForTransactionList(
                    $investorId,
                    $m[1],
                    $m[2],
                    [
                        Transaction::TYPE_EARLY_REPAYMENT,
                    ],
                    $dateFrom,
                    $dateTo
                );
                continue;
            }

            if (preg_match('/^(repurchased)\_(principal|interest|late_interest)/', $key, $m)) {
                $queries[] = $this->getQueryForTransactionList(
                    $investorId,
                    $m[1],
                    $m[2],
                    [
                        Transaction::TYPE_BUYBACK_MANUAL,
                        Transaction::TYPE_BUYBACK_OVERDUE,
                    ],
                    $dateFrom,
                    $dateTo
                );
                continue;
            }

            if (preg_match('/^(sm_buy)/', $key, $m)) {
                $queries[] = $this->getQueryForTransactionList(
                    $investorId,
                    $m[0],
                    'amount',
                    [
                        Transaction::TYPE_SECONDARY_MARKET_BUY,
                    ],
                    $dateFrom,
                    $dateTo
                );

                continue;
            }

            if (preg_match('/^(sm_sell)/', $key, $m)) {
                $queries[] = $this->getQueryForTransactionList(
                    $investorId,
                    $m[0],
                    'amount',
                    [
                        Transaction::TYPE_SECONDARY_MARKET_SELL,
                    ],
                    $dateFrom,
                    $dateTo
                );

                continue;
            }

            if (preg_match('/^(sm_premium)/', $key, $m)) {
                $queries[] = $this->getQueryForTransactionList(
                    $investorId,
                    $m[0],
                    'amount',
                    [
                        Transaction::TYPE_SECONDARY_MARKET_PREMIUM,
                    ],
                    $dateFrom,
                    $dateTo
                );

                continue;
            }
        }

        $sqlCount = "
            SELECT COUNT(res.transaction_id) FROM (
                (" . implode(") UNION ALL (", $queries) . ")
            ) as res
        ";

        $numrows = (current(DB::select($sqlCount)))->count;

        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT * FROM (
                (" . implode(") UNION ALL (", $queries) . ")
            ) as res
            ORDER BY res.transaction_id DESC
        ";

        if($limit != null){
           $sql .= "OFFSET {$offset} LIMIT {$limit}" ;
        }

        $data = DB::select($sql);

        if($limit == null){
           return $data;
        }
        return new LengthAwarePaginator($data, $numrows, $limit, $page);
    }

    /**
     * @param int $investorId
     * @param string $type
     * @param string $finType
     * @param array $realTypes
     * @param string $dateFrom
     * @param string $dateTo
     * @return string
     */
    public function getQueryForTransactionList(
        int $investorId,
        string $type,
        string $finType,
        array $realTypes,
        string $dateFrom,
        string $dateTo
    ): string {
        $sql = "
            SELECT
                t.transaction_id,
                t.loan_id,
                t.direction,
                t.created_at,
                t.secondary_market_id,
                t.details,
        ";

        if ('interest' == $finType) {
            $sql .= "(t.accrued_interest + t.interest) as sum,";
            $whereFinType = "(t.accrued_interest > 0 OR t.interest > 0)";
        } else {
            $sql .= "t." . $finType . " as sum,";

            $whereFinType = "t." . $finType . " > 0";

            if ($type == Transaction::TYPE_SECONDARY_MARKET_BUY || $type == Transaction::TYPE_SECONDARY_MARKET_SELL || $type == Transaction::TYPE_SECONDARY_MARKET_PREMIUM) {
                // Select all transactions. No matter if they are > or < than 0
                $whereFinType = "( t." . $finType . " >= 0 or t." . $finType . " <= 0 )";
            }

        }

        $acType = $type;
        if (!in_array($type, Transaction::getAmountTypes()) && !in_array($type, [Transaction::TYPE_SECONDARY_MARKET_SELL, Transaction::TYPE_SECONDARY_MARKET_BUY, Transaction::TYPE_SECONDARY_MARKET_PREMIUM])) {
            $acType = $type . "_" . $finType;
        }
        $sql .= "'" . $acType . "' as type, ";


        $sql .= "'" . $finType . "' as fin_type, ";

        $direction = Transaction::DIRECTION_OUT;
        if (
            Transaction::TYPE_WITHDRAW == $type ||
            Transaction::TYPE_INVESTMENT == $type ||
            Transaction::TYPE_SECONDARY_MARKET_BUY == $type
        ) {
            // For some reason it's upside down. In - is minus, Out is a plus in account stats
            $direction = Transaction::DIRECTION_IN;
        }

        if ($type == Transaction::TYPE_SECONDARY_MARKET_PREMIUM) {
            $sql .= "t.direction, ";
            $sql .= 't2.transaction_id as sm_transaction_id,
                    m.premium as premium ';
        }
        else {
            $sql .= "'" . $direction . "' as direction,
            0 as sm_transaction_id, 0 as premium ";
        }


        $sql .= "FROM transaction t ";

        if ($type == Transaction::TYPE_SECONDARY_MARKET_PREMIUM) {
            $sql .= 'left join
                    "transaction" t2
                on
                    t.key = t2.key and
                    t.transaction_id <> t2.transaction_id
                left join
                    market_secondary as m
                on
                    t.secondary_market_id = m.market_secondary_id ';
        }

        $sql .= "WHERE
                t.investor_id = " . $investorId . "
                AND t.type IN ('" . implode("', '", $realTypes) . "')
                AND (t.created_at BETWEEN '" . $dateFrom . "' AND '" . $dateTo . "')
                AND " . $whereFinType . "
                AND t.active = '1'
                AND t.deleted = '0'
        ";

        return $sql;
    }

    /**
     * @param int $investorId
     * @param string $dateFrom
     * @param string $dateTo
     * @param array $filters
     * @param bool $mapKeys
     * @return array
     */
    public function transactionsSum(
        int $investorId,
        string $dateFrom,
        string $dateTo,
        array $filters = [],
        bool $mapKeys = true
    ): array {
        $dateFrom .= ' 00:00:00';
        $dateTo .= ' 23:59:59';

        $query = $this->getQueryForTransactionsSum(
            $investorId,
            $dateFrom,
            $dateTo,
            $filters
        );

        $rows = DB::select(DB::raw($query));
        if (empty($rows)) {
            return [];
        }

        $result = [];
        foreach ($rows as $row) {
            if (in_array($row->type, [Transaction::TYPE_WITHDRAW, Transaction::TYPE_INVESTMENT, Transaction::TYPE_SECONDARY_MARKET_BUY])) {
                $row->direction = Transaction::DIRECTION_IN;
            } else {
                if (
                    $row->type == Transaction::TYPE_DEPOSIT ||
                    $row->type == Transaction::TYPE_SECONDARY_MARKET_SELL
                ) {
                    $row->direction = Transaction::DIRECTION_OUT;
                }
            }

            $key = $row->type;
            if (!in_array($key, Transaction::getAmountTypes())
                && !in_array($key, [Transaction::TYPE_SECONDARY_MARKET_SELL, Transaction::TYPE_SECONDARY_MARKET_BUY, Transaction::TYPE_SECONDARY_MARKET_PREMIUM])) {
                $key = $row->type . '_' . $row->fin_type;
            }

            if ($mapKeys) {
                $key = Transaction::getLabelForKey($key);
            }

            $result[$key] = $row;
        }
        ksort($result);

        return $result;
    }

    /**
     * @param int $investorId
     * @param string $dateFrom
     * @param string $dateTo
     * @param array $filters
     * @return string
     */
    private function getQueryForTransactionsSum(
        int $investorId,
        string $dateFrom,
        string $dateTo,
        array $filters = []
    ): string {

        $sql = [];
        $otherTypes = array_intersect($filters, Transaction::getAmountTypes());
        if (count($otherTypes) > 0) {
            $sql[] = "
            select sum(t.amount) as sum,t.type, 'amount' as fin_type, STRING_AGG (distinct t.direction, ';') direction
            from transaction t
            where
                t.investor_id = " . $investorId . "
                and t.type in ('" . implode("','", $otherTypes) . "')
                and (t.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
            group by t.type
            ";
        }
        if (in_array(Transaction::AC_TYPE_REPAYMENT_PRINCIPAL, $filters)) {
            $sql[] = "
            select SUM(resp.sum) as sum, resp.type, resp.fin_type, resp.direction
            from (
                select
                    sum(t1.principal) as sum, 'principal' as fin_type,
                    CASE WHEN t1.type = '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "' THEN '" . Transaction::TYPE_REPAYMENT . "' ELSE t1.type END AS type,
                    STRING_AGG (distinct t1.direction, ';') direction
                from transaction t1
                where
                    t1.investor_id = " . $investorId . "
                    and t1.type in ('" . Transaction::TYPE_REPAYMENT . "', '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "')
                    and (t1.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                    and t1.principal > 0
                group by t1.type
            ) as resp
            group by
                resp.type,
                resp.fin_type,
                resp.direction
            ";
        }
        if (in_array(Transaction::AC_TYPE_REPAYMENT_INTEREST, $filters)) {
            $sql[] = "
            select SUM(res.sum) as sum, res.type, res.fin_type, res.direction
            from (
                select
                    sum(t2.accrued_interest + t2.interest) as sum,
                    CASE WHEN t2.type = '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "' THEN '" . Transaction::TYPE_REPAYMENT . "' ELSE t2.type END AS type,
                    'interest' as fin_type , STRING_AGG (distinct t2.direction, ';') direction
                from transaction t2
                where
                    t2.investor_id = " . $investorId . "
                    and t2.type in ('" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "', '" . Transaction::TYPE_REPAYMENT . "')
                    and (t2.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                    and (t2.accrued_interest > 0 or t2.interest > 0)
                group by t2.type
            ) as res
            group by
                res.type,
                res.fin_type,
                res.direction
            ";
        }
        if (in_array(Transaction::AC_TYPE_REPAYMENT_LATE_INTEREST, $filters)) {
            $sql[] = "
            select SUM(resl.sum) as sum, resl.type, resl.fin_type, resl.direction
            from (
                select
                    sum(t3.late_interest) as sum, 'late_interest' as fin_type ,
                    CASE WHEN t3.type = '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "' THEN '" . Transaction::TYPE_REPAYMENT . "' ELSE t3.type END AS type,
                    STRING_AGG (distinct t3.direction, ';') direction
                from transaction t3
                where
                    t3.investor_id = " . $investorId . "
                    and t3.type in ('" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "', '" . Transaction::TYPE_REPAYMENT . "')
                    and (t3.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                    and t3.late_interest > 0
                group by t3.type
            ) as resl
            group by
                resl.type,
                resl.fin_type,
                resl.direction
            ";
        }
        if (in_array(Transaction::AC_TYPE_REPURCHASED_PRINCIPAL, $filters)) {
            $sql[] = "
            select SUM(resp.sum) as sum, 'repurchased' as type, resp.fin_type, resp.direction
            from (
                select sum(t1.principal) as sum, 'principal' as fin_type,
                    CASE WHEN t1.type = '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "' THEN '" . Transaction::TYPE_REPAYMENT . "' ELSE t1.type END AS type,
                    STRING_AGG (distinct t1.direction, ';') direction
                from transaction t1
                where
                    t1.investor_id = " . $investorId . "
                    and t1.type in ('" . Transaction::TYPE_BUYBACK_OVERDUE . "', '" . Transaction::TYPE_BUYBACK_MANUAL . "')
                    and (t1.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                    and t1.principal > 0
                group by t1.type
            ) as resp
            group by
                resp.fin_type,
                resp.direction
            ";
        }
        if (in_array(Transaction::AC_TYPE_REPURCHASED_INTEREST, $filters)) {
            $sql[] = "
            select SUM(res.sum) as sum, 'repurchased' as type, res.fin_type, res.direction
            from (
                select
                    sum(t2.accrued_interest + t2.interest) as sum,
                    CASE WHEN t2.type = '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "' THEN '" . Transaction::TYPE_REPAYMENT . "' ELSE t2.type END AS type,
                    'interest' as fin_type , STRING_AGG (distinct t2.direction, ';') direction
                from transaction t2
                where
                    t2.investor_id = " . $investorId . "
                    and t2.type in ('" . Transaction::TYPE_BUYBACK_OVERDUE . "', '" . Transaction::TYPE_BUYBACK_MANUAL . "')
                    and (t2.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                    and (t2.accrued_interest > 0 or t2.interest > 0)
                    group by t2.type
            ) as res
            group by
                res.fin_type,
                res.direction
            ";
        }
        if (in_array(Transaction::AC_TYPE_REPURCHASED_LATE_INTEREST, $filters)) {
            $sql[] = "
            select SUM(resl.sum) as sum, 'repurchased' as type, resl.fin_type, resl.direction
            from (
                select
                    sum(t3.late_interest) as sum, 'late_interest' as fin_type ,
                    CASE WHEN t3.type = '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "' THEN '" . Transaction::TYPE_REPAYMENT . "' ELSE t3.type END AS type,
                    STRING_AGG (distinct t3.direction, ';') direction
                from transaction t3
                where
                    t3.investor_id = " . $investorId . "
                    and t3.type in ('" . Transaction::TYPE_BUYBACK_OVERDUE . "', '" . Transaction::TYPE_BUYBACK_MANUAL . "')
                    and (t3.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                    and t3.late_interest > 0
                group by t3.type
            ) as resl
            group by
                resl.fin_type,
                resl.direction
            ";
        }
        if (in_array(Transaction::AC_TYPE_EARLY_REPAYMENT_PRINCIPAL, $filters)) {
            $sql[] = "
            select SUM(resp.sum) as sum, resp.type, resp.fin_type, resp.direction
            from (
                select
                    sum(t1.principal) as sum, 'principal' as fin_type,
                    CASE WHEN t1.type = '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "' THEN '" . Transaction::TYPE_REPAYMENT . "' ELSE t1.type END AS type,
                    STRING_AGG (distinct t1.direction, ';') direction
                from transaction t1
                where
                    t1.investor_id = " . $investorId . "
                    and t1.type in ('" . Transaction::TYPE_EARLY_REPAYMENT . "')
                    and (t1.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                    and t1.principal > 0
                group by t1.type
            ) as resp
            group by
                resp.type,
                resp.fin_type,
                resp.direction
            ";
        }
        if (in_array(Transaction::AC_TYPE_EARLY_REPAYMENT_INTEREST, $filters)) {
            $sql[] = "
            select SUM(res.sum) as sum, res.type, res.fin_type, res.direction
            from (
                select
                    sum(t2.accrued_interest + t2.interest) as sum,
                    CASE WHEN t2.type = '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "' THEN '" . Transaction::TYPE_REPAYMENT . "' ELSE t2.type END AS type,
                    'interest' as fin_type , STRING_AGG (distinct t2.direction, ';') direction
                from transaction t2
                where
                    t2.investor_id = " . $investorId . "
                    and t2.type in ('" . Transaction::TYPE_EARLY_REPAYMENT . "')
                    and (t2.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                    and (t2.accrued_interest > 0 or t2.interest > 0)
               group by t2.type
            ) as res
            group by
                res.type,
                res.fin_type,
                res.direction
            ";
        }
        if (in_array(Transaction::AC_TYPE_EARLY_REPAYMENT_LATE_INTEREST, $filters)) {
            $sql[] = "
            select SUM(resl.sum) as sum, resl.type, resl.fin_type, resl.direction
            from (
                select
                    sum(t3.late_interest) as sum, 'late_interest' as fin_type ,
                    CASE WHEN t3.type = '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "' THEN '" . Transaction::TYPE_REPAYMENT . "' ELSE t3.type END AS type,
                    STRING_AGG (distinct t3.direction, ';') direction
                from transaction t3
                where
                    t3.investor_id = " . $investorId . "
                    and t3.type in ('" . Transaction::TYPE_EARLY_REPAYMENT . "')
                    and (t3.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                    and t3.late_interest > 0
               group by t3.type
            ) as resl
            group by
                resl.type,
                resl.fin_type,
                resl.direction
            ";
        }

        if (in_array(Transaction::AC_TYPE_SECONDARY_MARKET_BUY, $filters)) {
            $sql[] = "
                select
                       SUM(resmb.sum) as sum,
                       resmb.type,
                       'sm_buy' as fin_type,
                       resmb.direction
                from (
                    select
                        SUM(\"amount\"),
                        type,
                        direction
                    from
                        \"transaction\" t
                    where
                        t.type = '".Transaction::TYPE_SECONDARY_MARKET_BUY."' and
                        t.investor_id = ".$investorId." and
                        (t.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                        -- and
                        --t.investment_id notnull
                    group by
                        secondary_market_id, investment_id, t.type, t.direction
                ) as resmb
                group by resmb.type, resmb.direction
            ";
        }

        if (in_array(Transaction::AC_TYPE_SECONDARY_MARKET_SELL, $filters)) {
            $sql[] = "
                select
                       SUM(resm.sum) as sum,
                       resm.type,
                       'sm_sell' as fin_type,
                       resm.direction
                from (
                    select
                        SUM(\"amount\"),
                        type,
                        direction
                    from
                        \"transaction\" t
                    where
                        t.type = '".Transaction::TYPE_SECONDARY_MARKET_SELL."' and
                        t.investor_id = ".$investorId." and
                        (t.created_at between '" . $dateFrom . "' and '" . $dateTo . "')
                        --and
                        --t.investment_id notnull
                    group by
                        secondary_market_id, investment_id, t.type, t.direction
                ) as resm
                group by resm.type, resm.direction
            ";
        }

        if (in_array(Transaction::AC_TYPE_SECONDARY_MARKET_PREMIUM, $filters)) {
            $sql[] = "
                select
                       case
                            when
                                a.sum is not null and b.sum is not null
                            then
                                SUM(a.sum + b.sum)
                            when
                                a.sum is null and b.sum is not null
                            then
                                SUM(b.sum)
                            when
                                a.sum is not null and b.sum is null
                            then
                               SUM(a.sum)
                            end as sum,
                       'sm_premium' as type,
                       'sm_premium' as fin_type,
                       'n/a' as direction
                from (
                    select
                        SUM(0 + amount),
                        investor_id
                    from
                        \"transaction\" t
                    where
                        t.type = '".Transaction::TYPE_SECONDARY_MARKET_PREMIUM."' and
                        t.investor_id = ".$investorId." and
                        (t.created_at between '" . $dateFrom . "' and '" . $dateTo . "') and
                        t.direction = '".Transaction::DIRECTION_IN."'
                    group by
                        t.investor_id
                ) as a
                FULL JOIN
                (
                    select
                        SUM(0 - t.amount),
                        investor_id
                    from
                        \"transaction\" t
                    where
                        t.type = '".Transaction::TYPE_SECONDARY_MARKET_PREMIUM."' and
                        t.investor_id = ".$investorId." and
                        (t.created_at between '" . $dateFrom . "' and '" . $dateTo . "') and
                        t.direction = '".Transaction::DIRECTION_OUT."'
                    group by
                        t.investor_id
                ) as b
                ON
                    a.investor_id = b.investor_id
                GROUP BY a.sum, b.sum
            ";
        }

        $query = $sql[0];
        if (count($sql) > 0) {
            $query = '(' . implode(') union all (', $sql) . ')';
        }

        return $query;
    }

    /**
     * @param array $data
     *
     * @return Transaction
     */
    public function create(array $data): Transaction
    {
        $transaction = new Transaction();
        $transaction->fill($data);
        $transaction->save();

        return $transaction;
    }

    /**
     * @param $id
     *
     * @return null|\Illuminate\Database\Eloquent\Model|Transaction|object
     */
    public function getByBankTransactionId($id)
    {
        return Transaction::where('bank_transaction_id', $id)->first();
    }

    /**
     * @param int $investorId
     * @param int $walletId
     *
     * @return array
     */
    public function getCalculatedWalletByTransactions(int $investorId, int $walletId)
    {
        $rows = $results = DB::select(
            DB::raw(
                "
        select
            coalesce((
                select sum(t.amount)
                from transaction as t
                join investor i on t.investor_id = i.investor_id
                where
                    i.investor_id = '$investorId'
                    and wallet_id = '$walletId'
                    and t.type = '" . Transaction::TYPE_DEPOSIT . "'
               group by t.investor_id
            ), 0) as deposit_amount,
            coalesce((
                select sum(t.amount)
                from transaction as t
                join investor i on t.investor_id = i.investor_id
                where
                    i.investor_id = '$investorId'
                    and wallet_id = '$walletId'
                    and t.type = '" . Transaction::TYPE_INVESTMENT . "'
                group by t.investor_id
            ), 0) as investment_amount,
            coalesce((
                select sum(t.amount)
                from transaction as t
                join investor i on t.investor_id = i.investor_id
                where
                    i.investor_id = '$investorId'
                    and wallet_id = '$walletId'
                    and t.type = '" . Transaction::TYPE_WITHDRAW . "'
                group by t.investor_id
            ), 0) as withdraw_amount,
            coalesce((
                select sum(t.principal)
                from transaction as t
                join investor i on t.investor_id = i.investor_id
                where
                    i.investor_id = '$investorId'
                    and wallet_id = '$walletId'
                    and t.type IN ('"
                . Transaction::TYPE_REPAYMENT . "','"
                . Transaction::TYPE_EARLY_REPAYMENT . "','"
                . Transaction::TYPE_INSTALLMENT_REPAYMENT . "'
                    )
                group by t.investor_id
            ), 0) as repayment_principal,
            coalesce((
                select (sum(t.late_interest) + sum(t.accrued_interest) + sum(t.interest))
                from transaction as t
                join investor i on t.investor_id = i.investor_id
                where
                    i.investor_id = '$investorId'
                    and wallet_id = '$walletId'
                    and t.type IN ('"
                . Transaction::TYPE_REPAYMENT . "','"
                . Transaction::TYPE_EARLY_REPAYMENT . "','"
                . Transaction::TYPE_INSTALLMENT_REPAYMENT . "'
                    )
                group by t.investor_id
            ), 0) as income,
            coalesce((
                select (sum(t.accrued_interest) + sum(t.interest))
                from transaction as t
                join investor i on t.investor_id = i.investor_id
                where
                    i.investor_id = '$investorId'
                    and wallet_id = '$walletId'
                    and t.type IN ('"
                . Transaction::TYPE_REPAYMENT . "','"
                . Transaction::TYPE_EARLY_REPAYMENT . "','"
                . Transaction::TYPE_INSTALLMENT_REPAYMENT . "'
                    )
                group by t.investor_id
            ), 0) as interest,
            coalesce((
                select sum(t.late_interest)
                from transaction as t
                join investor i on t.investor_id = i.investor_id
                where
                    i.investor_id = '$investorId'
                    and wallet_id = '$walletId'
                    and t.type IN ('"
                . Transaction::TYPE_REPAYMENT . "','"
                . Transaction::TYPE_EARLY_REPAYMENT . "','"
                . Transaction::TYPE_INSTALLMENT_REPAYMENT . "'
                    )
                group by t.investor_id
            ), 0) as late_interest,
            coalesce((
                select sum(t.amount)
                from transaction as t
                join investor i on t.investor_id = i.investor_id
                where
                    i.investor_id = '$investorId'
                    and wallet_id = '$walletId'
                    and t.type IN ('" . Transaction::TYPE_BONUS . "')
                group by t.investor_id
            ), 0) as bonus;
        "
            )
        );

        if (isset($rows[0])) {
            return $rows[0];
        }

        return null;
    }

    public function getRepaymentAmountForPeriod(string $startAt, string $endAt): float
    {
        $result = DB::select(
            DB::raw(
                "
                select sum(t.principal) as principal_sum
                from transaction as t
                where t.created_at >= timestamp '$startAt'
                and t.created_at <= timestamp '$endAt'
                and t.type IN ('" . Transaction::TYPE_REPAYMENT . "',
               '" . Transaction::TYPE_EARLY_REPAYMENT . "','" .
                Transaction::TYPE_INSTALLMENT_REPAYMENT . "')
                "
            )
        );

        return current($result)->principal_sum;
    }

    /**
     * @param int $investorId
     * @param int $transactionId
     * @return \Illuminate\Database\Eloquent\Model|Transaction|object|null
     */
    public function byInvestorAndTransactionId(int $investorId, int $transactionId)
    {
        return Transaction::where(
            [
                'investor_id' => $investorId,
                'transaction_id' => $transactionId,
                'active' => 1,
                'deleted' => 0
            ]
        )->first();
    }

    /**
     * @param int $investorId
     * @param string $dateFrom
     * @return bool
     */
    public function investorHasTransactionsAfter(
        int $investorId,
        string $dateFrom
    ): bool {
        return (
            Transaction::where(
                [
                    ['investor_id', '=', $investorId],
                    ['created_at', '>', $dateFrom],
                    ['active', '=', 1],
                    ['deleted', '=', 0]
                ]
            )->whereIn(
                'type',
                [
                    Transaction::TYPE_INVESTMENT,
                    Transaction::TYPE_WITHDRAW,
                ]
            )->count()
            ) > 0;
    }
}
