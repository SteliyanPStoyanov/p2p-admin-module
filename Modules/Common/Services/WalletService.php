<?php

namespace Modules\Common\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Entities\WalletHistory;
use Modules\Common\Repositories\WalletRepository;
use Modules\Core\Services\BaseService;

class WalletService extends BaseService
{
    private WalletRepository $walletRepository;

    /**
     * @param WalletRepository $walletRepository
     */
    public function __construct(
        WalletRepository $walletRepository
    ) {
        $this->walletRepository = $walletRepository;

        parent::__construct();
    }

    /**
     * @param int $investorId
     * @param array $data
     *
     * @return mixed
     */
    public function walletUpdate(int $investorId, array $data)
    {
        return $this->walletRepository->walletUpdate($investorId, $data);
    }

    /**
     * @param int $investorId
     *
     * @return mixed
     */
    public function getByInvestorId(int $investorId)
    {
        return $this->walletRepository->getByInvestorId($investorId);
    }

    /**
     * @param int $length
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getByWhereConditions(int $length, array $data)
    {
        $order = $this->getOrderConditions($data);
        unset($data['order']);

        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }


        $whereConditions = $this->getWhereConditions(
            $data,
            [
                'investor.first_name',
                'investor.middle_name',
                'investor.last_name'
            ],
            'wallet'
        );

        return $this->walletRepository->getAll($length, $whereConditions, $order);
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ) {
        $where = [];

        if (!empty($data['total_amount'])) {
            if (!empty($data['total_amount']['from'])) {
                $where[] = [
                    'wallet.total_amount',
                    '>=',
                    $data['total_amount']['from'],
                ];
            }

            if (!empty($data['total_amount']['to'])) {
                $where[] = [
                    'wallet.total_amount',
                    '<=',
                    $data['total_amount']['to'],
                ];
            }

            unset($data['total_amount']);
        }

        if (!empty($data['invested'])) {
            if (!empty($data['invested']['from'])) {
                $where[] = [
                    'wallet.invested',
                    '>=',
                    $data['invested']['from'],
                ];
            }

            if (!empty($data['invested']['to'])) {
                $where[] = [
                    'wallet.invested',
                    '<=',
                    $data['invested']['to'],
                ];
            }

            unset($data['invested']);
        }

        if (!empty($data['uninvested'])) {
            if (!empty($data['uninvested']['from'])) {
                $where[] = [
                    'wallet.uninvested',
                    '>=',
                    $data['uninvested']['from'],
                ];
            }

            if (!empty($data['uninvested']['to'])) {
                $where[] = [
                    'wallet.uninvested',
                    '<=',
                    $data['uninvested']['to'],
                ];
            }

            unset($data['uninvested']);
        }

        if (!empty($data['createdAt'])) {
            if (!empty($data['createdAt']['from'])) {
                $where[] = [
                    'wallet.created_at',
                    '>=',
                    dbDate($data['createdAt']['from'], '00:00:00'),
                ];
            }

            if (!empty($data['createdAt']['to'])) {
                $where[] = [
                    'wallet.created_at',
                    '<=',
                    dbDate($data['createdAt']['from'], '23:59:59'),
                ];
            }

            unset($data['createdAt']);
        }
        if (!empty($data['type'])) {
            $where[] = [
                'investor.type',
                '=',
                $data['type']
            ];

            unset($data['type']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param Investor $investor
     * @param string $date
     *
     * @return array
     */
    public function walletFromDate(Investor $investor, string $date)
    {
        $today = Carbon::now()->format('Y-m-d');

        $walletBalance = (new WalletHistory)->getWalletForDates(
            Currency::ID_EUR,
            $investor->investor_id,
            Carbon::parse($date)->subDay(1)->format('Y-m-d'),
            null
        );

        if (!empty($walletBalance)) {
            $walletBalance = [
                "date" => $date,
                "investor_id" => $investor->investor_id,
                "uninvested" => $walletBalance['start']['uninvested']
            ];
        } else {
            $walletBalance = [
                "date" => $date,
                "investor_id" => $investor->investor_id,
                "uninvested" => 0
            ];
        }


        return $walletBalance;
    }

    /**
     * @param Investor $investor
     * @param string $date
     *
     * @return array
     */
    public function walletToDate(Investor $investor, string $date)
    {
        $today = Carbon::now()->format('Y-m-d');

        $walletBalance = (new WalletHistory)->getWalletForDates(
            Currency::ID_EUR,
            $investor->investor_id,
            null,
            $date
        );

        if (empty($walletBalance)) {
            $walletBalance = [
                "date" => $date,
                "investor_id" => $investor->investor_id,
                "uninvested" => 0
            ];
        } else {
            $walletBalance = $walletBalance['end'];
        }

        if ($date == $today) {
            if (isset($investor->walletHistoryFirstDate()['date']) && $date < $investor->walletHistoryFirstDate(
                )['date']) {
                $walletBalance = [
                    "date" => $date,
                    "investor_id" => $investor->investor_id,
                    "uninvested" => 0
                ];
            } else {
                $walletBalance =
                    array_merge(
                        [
                            'date' => $today,
                            "investor_id" => $investor->investor_id
                        ],
                        (new Wallet)->getWalletForDates(
                            Currency::ID_EUR,
                            $investor->investor_id,
                        )
                    );
            }
        }

        return $walletBalance;
    }

    /**
     * @param int $investorId
     * @param int $currencyId
     */
    public function addNewInvestorWallet(int $investorId, $currencyId = Currency::ID_EUR)
    {
        $data = [
            'investor_id' => $investorId,
            'currency_id' => $currencyId,
            'date' => Carbon::now()
        ];

        $this->walletRepository->createWallet($data);
    }

    /**
     * @return array
     */
    public function getWalletsBalance(): array
    {
        $query = <<<EOD
select
    i.investor_id,
    i.first_name,
    i.last_name,
    coalesce((
        select SUM(t1.amount)
        from "transaction" t1
        where t1.investor_id = i.investor_id  and t1."type" = :deposit
        and t1.active = 1 and t1.deleted = 0
    ), 0) as deposited,
    coalesce((
        select SUM(t2.amount)
        from "transaction" t2
        where t2.investor_id = i.investor_id  and t2."type" = :investment
        and t2.active = 1 and t2.deleted = 0
    ), 0) as invested,
    coalesce((
        select SUM(t3.amount)
        from "transaction" t3
        where t3.investor_id = i.investor_id
        and t3."type" IN (:buyback_overdue, :buyback_manual, :repayment, :early_repayment, :installment_repayment)
        and t3.active = 1 and t3.deleted = 0
    ), 0) as repayments,
    coalesce((
        select SUM(t3.principal)
        from "transaction" t3
        where t3.investor_id = i.investor_id
        and t3."type" IN (:buyback_overdue, :buyback_manual, :repayment, :early_repayment, :installment_repayment)
    ), 0) as repaid_principal,
    coalesce((
        select SUM(t2.amount)
        from "transaction" t2
        where t2.investor_id = i.investor_id  and t2."type" = :withdraw
        and t2.active = 1 and t2.deleted = 0
    ), 0) as withdrawed,
    coalesce((
        select SUM(t4.amount)
        from "transaction" t4
        where t4.investor_id = i.investor_id  and t4."type" = :bonus
    ), 0) as bonus,
    (
        coalesce((
            select SUM(t1.amount)
            from "transaction" t1
            where t1.investor_id = i.investor_id  and t1."type" = :deposit
            and t1.active = 1 and t1.deleted = 0
        ), 0) - coalesce((
            select SUM(t2.amount)
            from "transaction" t2
            where t2.investor_id = i.investor_id  and t2."type" = :investment
            and t2.active = 1 and t2.deleted = 0
        ), 0) + coalesce((
            select SUM(t3.amount)
            from "transaction" t3
            where t3.investor_id = i.investor_id
            and t3."type" IN (:buyback_overdue, :buyback_manual,:repayment, :early_repayment, :installment_repayment)
            and t3.active = 1 and t3.deleted = 0
        ), 0) - coalesce((
            select SUM(t2.amount)
            from "transaction" t2
            where t2.investor_id = i.investor_id  and t2."type" = :withdraw
            and t2.active = 1 and t2.deleted = 0
        ), 0) + coalesce((
            select SUM(t4.amount)
            from "transaction" t4
            where t4.investor_id = i.investor_id  and t4."type" = :bonus
            and t4.active = 1 and t4.deleted = 0
        ), 0)
    ) as balance,
    coalesce((
        select SUM(t5.amount)
        from "transaction" t5
        where t5.investor_id = i.investor_id  and t5."type" = :sm_buy
    ), 0) as sm_buy,
    coalesce((
        select
               SUM(t7.amount)
        from
            "transaction" t6
        join
            transaction t7
        on
            t6.key = t7.key
        where
              t6.investor_id = i.investor_id and
              t6."type" = :sm_buy and
              t7."type" = :sm_premium
    ), 0) as sm_buy_premium,
    coalesce((
        select
               SUM(t10.amount)
        from
            "transaction" t9
        join
            transaction t10
        on
            t9.key = t10.key
        where
              t9.investor_id = i.investor_id and
              t9."type" = :sm_sell and
              t10."type" = :sm_premium
    ), 0) as sm_sell_premium,
    coalesce((
        select SUM(t8.amount)
        from "transaction" t8
        where t8.investor_id = i.investor_id  and t8."type" = :sm_sell
    ), 0) as sm_sell,
    w2.bonus as wallet_bonus,
    w2.deposit as wallet_deposited,
    w2.invested as wallet_invested,
    w2.income as wallet_income,
    (w2.uninvested + w2.blocked_amount) as wallet_uninvested,
    w2.withdraw as wallet_withdraw,
    w2.total_amount as wallet_total_amount,
    coalesce((
        select sum(ii.principal)
        from investor_installment ii
        where ii.investor_id = i.investor_id and ii.paid = 0 and deleted = 0
    ),0) as outstanding_principal
    from investor i
    left join wallet w2 on i.investor_id = w2.investor_id
    where i.status = :verified
    and i.deleted = 0
    and i.active = 1
EOD;

        return DB::select(
            DB::raw($query),
            [
                'verified' => Investor::INVESTOR_STATUS_VERIFIED,
                'bonus' => Transaction::TYPE_BONUS,
                'withdraw' => Transaction::TYPE_WITHDRAW,
                'deposit' => Transaction::TYPE_DEPOSIT,
                'investment' => Transaction::TYPE_INVESTMENT,
                'buyback_overdue' => Transaction::TYPE_BUYBACK_OVERDUE,
                'buyback_manual' => Transaction::TYPE_BUYBACK_MANUAL,
                'repayment' => Transaction::TYPE_REPAYMENT,
                'early_repayment' => Transaction::TYPE_EARLY_REPAYMENT,
                'installment_repayment' => Transaction::TYPE_INSTALLMENT_REPAYMENT,
                'sm_buy' => Transaction::TYPE_SECONDARY_MARKET_BUY,
                'sm_sell' => Transaction::TYPE_SECONDARY_MARKET_SELL,
                'sm_premium' => Transaction::TYPE_SECONDARY_MARKET_PREMIUM
            ]
        );
    }
}
