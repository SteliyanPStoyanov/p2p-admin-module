<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Verification;
use Modules\Common\Affiliates\DoAffiliate;
use Modules\Common\Interfaces\HistoryInterface;
use Modules\Common\Observers\InvestorObserver;
use Modules\Common\Services\PortfolioService;
use Modules\Communication\Entities\Email;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseAuthModel;

class Investor extends BaseAuthModel implements HistoryInterface, LoggerInterface
{
    public const INVESTOR_ID = 1;
    public const INVESTOR_STATUS_UNREGISTERED = 'unregistered';
    public const INVESTOR_STATUS_REGISTERED = 'registered';
    public const INVESTOR_STATUS_AWAITING_VERIFICATION = 'awaiting_verification';
    public const INVESTOR_STATUS_AWAITING_DOCUMENTS = 'awaiting_documents';
    public const INVESTOR_STATUS_VERIFIED = 'verified';
    public const INVESTOR_STATUS_REJECTED_VERIFICATION = 'rejected_verification';
    public const TYPE_INDIVIDUAL = 'individual';
    public const TYPE_COMPANY = 'company';
    public const INVESTOR_CONTINUE_VERIFICATION_DAYS = ['1', '7', '21', '51'];
    public const INVESTOR_STATISTIC_DAYS = [14, 28, 90, 180, 365, 0];
    public const INVESTOR_CONTINUE_REGISTRATION_DAYS = ['1', '3', '14', '31'];
    public const SIZE_HASH_LINK = 10;
    public const INVESTOR_CHART_BAR_12 = 12;
    public const INVESTOR_CHART_BAR_24 = 24;
    public const INVESTOR_HASH_LINK_SYMBOLS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    private $wallets = [];
    private $qualities = [];
    private $maturities = [];
    private $portfolios = [];

    protected $guard = 'investor';
    protected $table = 'investor';
    protected $primaryKey = 'investor_id';
    protected $historyClass = ChangeLog::class;

    /**
     * @var string[]
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'birth_date',
        'citizenship',
        'residence',
        'city',
        'postcode',
        'address',
        'comment',
        'type',
        'political',
        'locale_id',
        'status',
        'verification_data',
        'referral_hash',
        'referral_id',
        'unregistered_recall_at',
        'registered_recall_at',
        'email_notification',
        'running_bunch_id',
        'buy_now',
    ];

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::observe(InvestorObserver::class);
    }


    public function fullName()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    /**
     * @return HasMany
     */
    public function email(): HasMany
    {
        return $this->hasMany(
            Email::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(
            Document::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(
            Country::class,
            'residence',
            'country_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function verification(): BelongsTo
    {
        return $this->belongsTo(
            Verification::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function investorCitizenship(): BelongsTo
    {
        return $this->belongsTo(
            Country::class,
            'citizenship',
            'country_id'
        );
    }

    /**
     * @return HasMany
     */
    public function portfolios(): HasMany
    {
        return $this->hasMany(
            Portfolio::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return HasMany
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(
            BankAccount::class,
            'investor_id',
            'investor_id'
        )
            ->distinct('iban')
            ->orderBy('iban')
            ->orderBy('default', 'DESC');
    }

    /**
     * @return ?BankAccount
     */
    public function mainBankAccount()
    {
        return $this->bankAccounts->where('default', 1)->first();
    }

    /**
     * @return int|null
     */
    public function getMainBankAccountId(): ?int
    {
        $account = $this->mainBankAccount();
        if (empty($account->bank_account_id)) {
            return null;
        }

        return $account->bank_account_id;
    }

    /**
     * @return int
     */
    public function earnedIncome()
    {
        $wallet = $this->wallet();

        if (empty($wallet)) {
            return 0;
        }

        return ($wallet->interest + $wallet->late_interest + $wallet->bonus);
    }

    /**
     * @return int
     */
    public function totalBalance()
    {
        $wallet = $this->wallet();

        if (empty($wallet)) {
            return 0;
        }

        return ($this->earnedIncome() + $wallet->deposit - $wallet->withdraw);
    }

    /**
     * @param int|string $currencyId
     *
     * @return mixed
     */
    public function wallet(int $currencyId = Currency::ID_EUR): ?Wallet
    {
        if (!isset($this->wallets[$currencyId])) {
            $this->wallets[$currencyId] = Wallet::where(
                [
                    'investor_id' => $this->investor_id,
                    'currency_id' => $currencyId,
                    'active' => 1,
                    'deleted' => 0,
                ]
            )->first();
        }
        return $this->wallets[$currencyId];
    }

    public function getWalletBlockedForUpdate(int $currencyId = Currency::ID_EUR): ?Wallet
    {
        return Wallet::where(
            [
                'investor_id' => $this->investor_id,
                'currency_id' => $currencyId,
                'active' => 1,
                'deleted' => 0,
            ]
        )->lockForUpdate()->first();
    }

    public function getQualityPortfolioBlockedForUpdate(
        int $currencyId = Currency::ID_EUR
    ): Portfolio {
        return Portfolio::where(
            [
                'investor_id' => $this->investor_id,
                'currency_id' => $currencyId,
                'type' => Portfolio::PORTFOLIO_TYPE_QUALITY,
                'active' => 1,
                'deleted' => 0,
            ]
        )->lockForUpdate()->first();
    }

    public function getMaturityPortfolioBlockedForUpdate(
        int $currencyId = Currency::ID_EUR
    ): Portfolio {
        return Portfolio::where(
            [
                'investor_id' => $this->investor_id,
                'currency_id' => $currencyId,
                'type' => Portfolio::PORTFOLIO_TYPE_MATURITY,
                'active' => 1,
                'deleted' => 0,
            ]
        )->lockForUpdate()->first();
    }

    /**
     * @return mixed
     */
    public function verificationTask()
    {
        return $this->tasks->where('task_type', Task::TASK_TYPE_VERIFICATION)->first();
    }

    /**
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(
            Task::class,
            'investor_id',
            'investor_id',
        );
    }

    /**
     * @return HasMany
     */
    public function investorBonuses(): HasMany
    {
        return $this->hasMany(
            InvestorBonus::class,
            'investor_bonus_id',
            'investor_bonus_id',
        );
    }

    /**
     *
     * @param int|string $currencyId
     *
     * @return mixed
     */
    public function portfolio(int $currencyId = Currency::ID_EUR)
    {
        if (!isset($this->portfolios[$currencyId])) {
            $this->portfolios[$currencyId] = \App::make(PortfolioService::class)->getPortfolio(
                $this->investor_id,
                $currencyId
            );
        }
        return $this->portfolios[$currencyId];
    }

    /**
     * @param int $currencyId
     * @return mixed
     */
    public function quality(int $currencyId = Currency::ID_EUR)
    {
        if (!isset($this->qualities[$currencyId])) {
            $this->qualities[$currencyId] = \App::make(PortfolioService::class)->getQuality(
                $this->investor_id,
                $currencyId
            );
        }
        return $this->qualities[$currencyId];
    }

    /**
     * @param int $currencyId
     * @return mixed
     */
    public function maturity(int $currencyId = Currency::ID_EUR)
    {
        if (!isset($this->maturities[$currencyId])) {
            $this->maturities[$currencyId] = \App::make(PortfolioService::class)->getMaturity(
                $this->investor_id,
                $currencyId
            );
        }
        return $this->maturities[$currencyId];
    }

    /**
     * Could be more then one investment for 1 loan from 1 investor
     *
     * @param int $loanId
     * @param float|null $amount
     * @param int|null $investmentId
     * @return array
     */
    public function investments(
        int $loanId,
        float $amount = null,
        int $investmentId = null
    ): array {
        $where = [
            'investor_id' => $this->investor_id,
            'loan_id' => $loanId,
        ];
        if (!empty($amount)) {
            $where['amount'] = $amount;
        }
        if (!empty($investmentId)) {
            $where['investment_id'] = $investmentId;
        }

        return Investment::where($where)->get()->all();
    }

    /**
     * [installments description]
     *
     * @param int $loanId
     *
     * @return array
     */
    public function installments(int $loanId): array
    {
        return InvestorInstallment::where(
            [
                'investor_id' => $this->investor_id,
                'loan_id' => $loanId,
            ]
        )->get()->all();
    }

    /**
     * @return Builder|\Illuminate\Database\Query\Builder|InvestmentBunch
     */
    public function getInvestmentBunch()
    {
        $investmentBunch = InvestmentBunch::where(
            [
                ['investor_id', '=', $this->investor_id],
                ['finished', '=', 0],
            ]
        )
            ->orderBy('investment_bunch_id', 'DESC');

        if (!empty($investmentBunch)) {
           return $investmentBunch->first();
        }
        return false;
    }

    /**
     * [getUnpaidInstallments description]
     *
     * @param int $loanId
     * @param int $investmentId
     *
     * @return array
     */
    public function getUnpaidInstallments(int $loanId, int $investmentId): array
    {
        $builder = DB::table('investor_installment');
        $builder->select(
            DB::raw(
                '
            investor_installment.*,
            installment.due_date
        '
            )
        );
        $builder->join(
            'installment',
            'investor_installment.installment_id',
            '=',
            'installment.installment_id'
        );
        $builder->where(
            [
                'investor_installment.investor_id' => $this->investor_id,
                'investor_installment.investment_id' => $investmentId,
                'investor_installment.loan_id' => $loanId,
                'investor_installment.paid' => 0,
            ]
        );
        $builder->orderBy('installment_id', 'ASC');

        if (!$builder->count()) {
            return [];
        }

        return InvestorInstallment::hydrate($builder->get()->all())->all();
    }

    /**
     * @return string[]
     */
    public static function getTypes()
    {
        return [
            self::TYPE_INDIVIDUAL,
            self::TYPE_COMPANY,
        ];
    }

    /**
     * @return array
     */
    public static function getRegisteredInvestors(): array
    {
        $results = DB::select(
            DB::raw(
                "
            SELECT
                 COUNT(investor.status)
            FROM investor
            WHERE investor.status != :unregistered
        "
            ),
            [
                'unregistered' => Investor::INVESTOR_STATUS_UNREGISTERED
            ]
        );

        $results = array_map(
            function ($value) {
                return (array)$value;
            },
            $results
        );

        return current($results);
    }

    /**
     * @param array $statuses
     *
     * @return array
     */
    public static function getInvestorsByStatus(array $statuses): array
    {
        $implode = implode("', '", $statuses);
        $results = DB::select(
            DB::raw(
                "
            SELECT
                 investor.status,
                 COUNT(investor.status)
            FROM investor
            WHERE investor.status IN ('" . $implode . "')
            GROUP BY investor.status
        "
            )
        );
        $investor = [];

        foreach ($results as $result) {
            $investor[$result->status] = $result->count;
        }

        return $investor;
    }

    /**
     * @return mixed
     */
    public static function getInvestorsWithDeposit()
    {
        $results = DB::select(
            DB::raw(
                "select count(*) from (
                         select :deposit as type
                         FROM transaction as t
                         WHERE t.type = :deposit
                           and t.active = '1'
                           and t.deleted = '0'
                         group by investor_id
                     ) as tr
        "
            ),
            [
                'deposit' => Transaction::TYPE_DEPOSIT
            ]
        );

        $results = array_map(
            function ($value) {
                return (array)$value;
            },
            $results
        );

        return current($results);
    }

    /**
     * @param int|null $days
     * @return array
     */
    public static function getInvestorsActivityPerDay(int $days = null): array
    {
        $dayTo = Carbon::today()->subDays($days)->format('Y-m-d') . ' 00:00:01';

        $daysStats = "TO_CHAR(i.created_at , 'YYYY-mm-dd') as created";

        $results = DB::select(
            DB::raw(
                "select
                    :registered as type,
                    " . $daysStats . ",
                    count(*) as number
                from investor as i
                where i.created_at >= '" . $dayTo . "'
                and i.status != :unregistered
                group by created
                UNION
                select
                    :verified as type,
                     " . $daysStats . ",
                    count(*) as number
                from investor as i
                where
                    i.created_at >= '" . $dayTo . "'
                    and i.status = :verified
                group by created
                UNION
                select
                    :deposit as type,
                     " . $daysStats . ",
                    count(*) as number
                from transaction as i
                where type = :deposit
                 and i.created_at >= '" . $dayTo . "'
                group by created
                ORDER BY created;
        "
            ),
            [
                'deposit' => Transaction::TYPE_DEPOSIT,
                'verified' => Investor::INVESTOR_STATUS_VERIFIED,
                'unregistered' => Investor::INVESTOR_STATUS_UNREGISTERED,
                'registered' => Investor::INVESTOR_STATUS_REGISTERED,
            ]
        );

        return array_map(
            function ($value) {
                return (array)$value;
            },
            $results
        );
    }

    /**
     * @return HasMany
     */
    public function agreements(): HasMany
    {
        return $this->hasMany(
            InvestorAgreement::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return bool
     */
    public function addFundNotificationChecked(): bool
    {
        return (bool)$this
            ->agreements
            ->where('agreement_id', Agreement::RECEIVE_FUNDS_NOTIFICATION_ID)
            ->where('value', 1)
            ->count();
    }

    /**
     * @return bool
     */
    public function withdrawNotificationChecked(): bool
    {
        return (bool)$this
            ->agreements
            ->where('agreement_id', Agreement::WITHDRAW_REQUEST_NOTIFICATION_ID)
            ->where('value', 1)
            ->count();
    }

    /**
     * @return bool
     */
    public function newDeviceNotificationChecked(): bool
    {
        return (bool)$this
            ->agreements
            ->where('agreement_id', Agreement::NEW_DEVICE_NOTIFICATION)
            ->where('value', 1)
            ->count();
    }

    /**
     * @param float $amount
     *
     * @return bool
     */
    public function canWithdraw(float $amount): bool
    {
        $wallet = $this->wallet();

        return $wallet->hasUninvestedAmount($amount);
    }

    /**
     * @return mixed
     */
    public function getActiveWithdrawTasksSum()
    {
        return DB::selectOne(
            DB::raw(
                '
                SELECT
                       COALESCE(SUM(t.amount), 0) AS sum
                FROM
                    task AS t
                WHERE
                      t.investor_id = :investor_id
                AND
                      t.task_type = :tasktype
                AND
                      t.active = :active
                AND
                      t.deleted = :deleted
                AND
                      (t.status != :status_done
                AND
                      t.status != :status_cancel)
            '
            ),
            [
                'investor_id' => $this->investor_id,
                'status_done' => Task::TASK_STATUS_DONE,
                'status_cancel' => Task::TASK_STATUS_CANCEL,
                'active' => 1,
                'deleted' => 0,
                'tasktype' => Task::TASK_TYPE_WITHDRAW
            ]
        )->sum;
    }

    /**
     * @return HasMany
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(
            InvestorContract::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @param int $periodDays
     *
     * @return array
     */
    public function earnedIncomeStatisticsDays(int $periodDays): array
    {
        $dayTo = Carbon::now()->endOfDay()->subDays($periodDays);
        $where = [];
        if ($periodDays > 0) {
            $where[] = "t.created_at > '" . $dayTo . "'";
        }
        $where[] = "t.investor_id = '" . $this->investor_id . "'";
        $where[] = "t.direction = '" . Transaction::DIRECTION_OUT . "'";
        $where[] = "t.type in  (
                '" . Transaction::TYPE_BUYBACK_MANUAL . "',
                '" . Transaction::TYPE_BUYBACK_OVERDUE . "',
                '" . Transaction::TYPE_EARLY_REPAYMENT . "',
                '" . Transaction::TYPE_REPAYMENT . "',
                '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "'
                )";
        $results = DB::select(
            DB::raw(
                "select
                    TO_CHAR(t.created_at , 'YYYY-mm-dd') as created,
                    (sum(t.accrued_interest) + sum(t.interest) + sum(t.late_interest)) as earned
                from transaction as t
               where " . implode(' AND ', $where) . "
                group by TO_CHAR(t.created_at , 'YYYY-mm-dd')
                ORDER BY created;
        "
            )
        );

        return $results;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function earnedIncomeStatisticsWeek(int $limit): array
    {
        $where = [];
        $where[] = "t.investor_id = '" . $this->investor_id . "'";
        $where[] = "t.direction = '" . Transaction::DIRECTION_OUT . "'";
        $where[] = "t.type in  (
                '" . Transaction::TYPE_BUYBACK_MANUAL . "',
                '" . Transaction::TYPE_BUYBACK_OVERDUE . "',
                '" . Transaction::TYPE_EARLY_REPAYMENT . "',
                '" . Transaction::TYPE_REPAYMENT . "',
                '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "'
                )";
        $results = DB::select(
            DB::raw(
                "select
                    TO_CHAR(date_trunc('week', t.created_at::date), 'YYYY-mm-dd') AS created,
                    (sum(t.accrued_interest) + sum(t.interest) + sum(t.late_interest)) as earned
                from transaction as t
               where " . implode(' AND ', $where) . "
                group by created
                ORDER BY created desc
                LIMIT " . $limit . ";
        "
            )
        );

        return $results;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function earnedIncomeStatisticsMonth(int $limit): array
    {
        $where = [];
        $where[] = "t.investor_id = '" . $this->investor_id . "'";
        $where[] = "t.direction = '" . Transaction::DIRECTION_OUT . "'";
        $where[] = "t.type in  (
                '" . Transaction::TYPE_BUYBACK_MANUAL . "',
                '" . Transaction::TYPE_BUYBACK_OVERDUE . "',
                '" . Transaction::TYPE_EARLY_REPAYMENT . "',
                '" . Transaction::TYPE_REPAYMENT . "',
                '" . Transaction::TYPE_INSTALLMENT_REPAYMENT . "'
                )";
        $results = DB::select(
            DB::raw(
                "select
                    TO_CHAR(date_trunc('month', t.created_at::date), 'YYYY-mm-dd') AS created,
                    (sum(t.accrued_interest) + sum(t.interest) + sum(t.late_interest)) as earned
                from transaction as t
               where " . implode(' AND ', $where) . "
                group by created
                 ORDER BY created desc
                 LIMIT " . $limit . ";
        "
            )
        );

        return $results;
    }

    /**
     * @param int $periodDays
     *
     * @return array
     */
    public function investedStatisticsDays(int $periodDays): array
    {
        $dayTo = Carbon::today()->subDays($periodDays);

        $where = [];
        if ($periodDays > 0) {
            $where[] = "t.date >= '" . $dayTo->format('Y-m-d') . "'";
        }
        $where[] = "t.investor_id = '" . $this->investor_id . "'";

        $walletHistoryInvested = $this->walletHistoryInvestedDate();
        if (!isset($walletHistoryInvested[0])) {
            return [];
        }

        $results = DB::select(
            DB::raw(
                "
                SELECT
                    t.date as created,
                    t.invested
                FROM wallet_history as t
                WHERE " . implode(' AND ', $where) . "
                ORDER BY t.date desc;
            "
            )
        );

        return $this->investedStatisticsToday($this->investor_id, $results);
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function investedStatisticsWeek(int $limit): array
    {
        $whereOr = [];

        $walletHistoryInvested = $this->walletHistoryInvestedDate();
        if (!isset($walletHistoryInvested[0])) {
            return [];
        }

        for ($i = 0; $i <= $limit; $i++) {
            $whereOr[] = "t.date = '" . Carbon::now()->subWeek($i)->endOfWeek()->format('Y-m-d') . "'";
        }

        $results = DB::select(
            DB::raw(
                "
                SELECT
                       t.date as created,
                    t.invested as invested
                FROM wallet_history as t
                WHERE t.investor_id = '" . $this->investor_id . "'
                AND (" . implode(' OR ', $whereOr) . ")
                group by invested,t.date
                 ORDER BY t.date desc
                 LIMIT " . $limit . ";
            "
            )
        );

        return $this->investedStatisticsToday($this->investor_id, $results);
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function investedStatisticsMonth(int $limit): array
    {
        $whereOr = [];

        $where[] = "t.investor_id = '" . $this->investor_id . "'";

        $walletHistoryInvested = $this->walletHistoryInvestedDate();
        if (!isset($walletHistoryInvested[0])) {
            return [];
        }

        for ($i = 0; $i <= $limit; $i++) {
            $whereOr[] = "t.date = '" . Carbon::now()->subMonth($i)->endOfMonth()->format('Y-m-d') . "'";
        }

        $results = DB::select(
            DB::raw(
                "
                SELECT
                       t.date as created,
                    t.invested as invested
                FROM wallet_history as t
                WHERE t.investor_id = '" . $this->investor_id . "'
                AND (" . implode(' OR ', $whereOr) . ")
                group by invested,t.date
                 ORDER BY t.date desc
                 LIMIT " . $limit . ";
            "
            )
        );

        return $this->investedStatisticsToday($this->investor_id, $results);
    }

    /**
     * @param int $investorId
     * @param array|null $results
     * @return array|null
     */
    public function investedStatisticsToday(
        int $investorId,
        array $results = null
    ): ?array {
        $resultsToday = DB::select(
            DB::raw(
                "
                SELECT
                t2.date as created,
                t2.invested
            FROM wallet t2
            WHERE t2.investor_id = '" . $investorId . "';
            "
            )
        );

        if (empty($results)) {
            $results = $resultsToday;
        } else {
            if (!empty($resultsToday[0]) && ($resultsToday[0]->created != $results[0]->created)) {
                $results = array_merge($resultsToday, $results);
            }
        }

        return $results;
    }

    /**
     * @return array
     */
    public function investorRemainingInvestment(): array
    {
        $results = DB::select(
            DB::raw(
                "
                select
                    l.payment_status,
                    sum(ii.principal) as amount
                from investor_installment ii
                join loan l on l.loan_id = ii.loan_id
                where
                    ii.investor_id = '" . $this->investor_id . "'
                    and ii.paid = 0
                group by l.payment_status
                order by l.payment_status;
            "
            )
        );

        $data = [];
        foreach ($results as $result) {
            $data[$result->payment_status] = $result->amount;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function investorRemainingInvestmentLoans(): array
    {
        $results = DB::select(
            DB::raw(
                "select mm.payment_status ,count(*) as number
                    from (
                        select l.payment_status , count(DISTINCT l.loan_id)
                        from investor_installment as ii
                        join loan l on ii.loan_id = l.loan_id
                        where ii.investor_id = '" . $this->investor_id . "'
                        and ii.paid = 0
                        group by l.loan_id
                        ) as mm
                        group by mm.payment_status
                        "
            )
        );

        $data = [];
        foreach ($results as $result) {
            $data[$result->payment_status] = $result->number;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function investorRemainingInvestmentTerm(): array
    {
        $results = DB::select(
            DB::raw(
                "
                select
                    l.final_payment_date,
                    sum(ii.principal) as amount
                from investor_installment ii
                join loan l on l.loan_id = ii.loan_id
                where
                    ii.investor_id = '" . $this->investor_id . "'
                    and ii.paid = 0
                group by l.final_payment_date
                order by l.final_payment_date;
            "
            )
        );

        $data = [];
        foreach ($results as $result) {
            $data[Portfolio::getMaturityRangeColumnByDate(
                Carbon::parse($result->final_payment_date)
            )][] = $result->amount;
        }

        return $data;
    }

    public function investorRemainingInvestmentTermNumber(): array
    {
        $results =
            DB::select(
                DB::raw(
                    "select l.final_payment_date , count(l.loan_id) as amount
                        from investor_installment as ii
                        join loan l on ii.loan_id = l.loan_id
                        where ii.investor_id = '" . $this->investor_id . "'
                        and ii.paid = 0
                        group by l.loan_id"
                )
            );

        $data = [];
        foreach ($results as $result) {
            $data[Portfolio::getMaturityRangeColumnByDate(
                Carbon::parse($result->final_payment_date)
            )][] = $result->amount;
        }

        return $data;
    }

    /**
     * @param int $currencyId
     *
     * @return array
     */
    public function walletHistoryFirstDate(int $currencyId = Currency::ID_EUR): array
    {
        $builder = DB::table('wallet_history');
        $builder->selectRaw(DB::raw('wh.date'));
        $builder->fromRaw(DB::raw("wallet_history wh"));
        $builder->whereRaw(
            "wh.investor_id = '" . $this->investor_id . "'
                 and wh.currency_id = '" . $currencyId . "'"
        );
        $builder->orderByRaw("date asc");
        $builder->limit(1);
        return (array)$builder->first();
    }

    /**
     * @return array
     */
    public function firstTransactionDate(): array
    {
        $builder = DB::table('transaction');
        $builder->selectRaw(DB::raw('tr.created_at'));
        $builder->fromRaw(DB::raw("transaction tr"));
        $builder->whereRaw(
            "tr.investor_id = '" . $this->investor_id . "'
                 and tr.direction = '" . Transaction::DIRECTION_OUT . "'"
        );
        $builder->orderByRaw("created_at asc");
        $builder->limit(1);
        return (array)$builder->first();
    }

    /**
     * @return array
     */
    public function walletHistoryInvestedDate(): array
    {
        return DB::select(
            DB::raw(
                "
                SELECT
                    t.date as created,
                    t.invested
                FROM wallet_history as t
                WHERE  t.investor_id = '" . $this->investor_id . "'
                AND t.invested > 0
                ORDER BY t.date
                LIMIT 1;
            "
            )
        );
    }

    /**
     * @return bool
     */
    public function hasActiveVerificationTask(): bool
    {
        $count = DB::selectOne(
            DB::raw(
                '
                SELECT COUNT(*) as count
                FROM task AS t
                WHERE
                    t.investor_id = :investor_id
                    AND t.status != :status_done
                    AND t.task_type = :tasktype
            '
            ),
            [
                'investor_id' => $this->investor_id,
                'status_done' => Task::TASK_STATUS_DONE,
                'tasktype' => Task::TASK_TYPE_VERIFICATION
            ]
        )->count;

        return ($count > 0);
    }

    /**
     * @return bool
     */
    public function isVerified(): bool
    {
        return in_array(
            $this->status,
            [
                self::INVESTOR_STATUS_VERIFIED,
                self::INVESTOR_STATUS_REJECTED_VERIFICATION,
            ]
        );
    }

    public function setActiveInvestmentBunch(int $investmentBunchId)
    {
        if (empty($this->running_bunch_id)) {
            $this->setRunningBunchId($investmentBunchId);
            return true;
        }

        if ($this->running_bunch_id == $investmentBunchId) {
            return true;
        }

        return false;
    }

    public function setRunningBunchId(int $investmentBunchId)
    {
        $this->running_bunch_id = $investmentBunchId;
        $this->save();
    }

    public function removeRunningBunchId()
    {
        $this->running_bunch_id = null;
        $this->save();
    }

    /**
     * @return array
     */
    public function investorBonus(): array
    {
        $results = DB::select(
            DB::raw(
                "
                SELECT
                    sum(ib.amount) as sum,
                    'receivedBonus' as type
                FROM investor_bonus AS ib
                WHERE
                    ib.investor_id = :investor_id
                    AND ib.handled = :receivedHandle
                UNION ALL
                SELECT
                    sum(ib.amount) as sum,
                    'accruedBonus' as type
                FROM investor_bonus AS ib
                WHERE
                    ib.investor_id = :investor_id
                    AND ib.handled = :accruedHandle
            "
            ),
            [
                'investor_id' => $this->investor_id,
                'receivedHandle' => 1,
                'accruedHandle' => 0,
            ]
        );

        $data = [];
        foreach ($results as $result) {
            $data[$result->type] = $result->sum;
        }

        return $data;
    }


    public function cartSecondarySeller()
    {
        return $this->hasOne(
            CartSecondary::class,
            'investor_id',
            'investor_id'
        )->where('type', CartSecondary::TYPE_SELLER);
    }

    /**
     * @return HasMany
     */
    public function company(): HasMany
    {
        return $this->hasMany(
            InvestorCompany::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return HasMany
     */
    public function affiliateInvestors(): HasMany
    {
        return $this->hasMany(
            AffiliateInvestor::class,
            'investor_id',
            'investor_id'
        )->orderBy('affiliate_investor_id', 'DESC');
    }

    /**
     * @return bool
     */
    public function isAffiliate(): bool
    {
        $affiliates = $this->affiliateInvestors()->first();
        if (is_null($affiliates)) {
            return false;
        }

        return $affiliates->count() > 0;
    }

    /**
     * @return bool
     */
    public function isActiveAffiliate(): bool
    {
        $investorRegisteredDate = Carbon::parse($this->created_at);

        if ($this->isAffiliate() && ($investorRegisteredDate->diffInDays() < DoAffiliate::AFFILIATE_DAYS)) {
            return true;
        }
        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    public function getAffiliateDescription()
    {
        $affiliateDescription = $this->affiliateInvestors()->first();

        if (is_null($affiliateDescription)) {
            return false;
        }
        return $affiliateDescription->affiliate->affiliate_description;
    }

    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    public function getAffiliateClientId()
    {
        $clientId = $this->affiliateInvestors()->first();

        if (is_null($clientId)) {
            return false;
        }
        return $clientId->client_id;
    }

    /**
     * @return HasMany
     */
    public function affiliateStats(): HasMany
    {
        return $this->hasMany(
            AffiliateStats::class,
            'investor_id',
            'investor_id'
        );
    }
}
