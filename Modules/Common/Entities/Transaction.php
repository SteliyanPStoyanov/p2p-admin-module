<?php

namespace Modules\Common\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;
use Modules\Common\Observers\TransactionObserver;

class Transaction extends BaseModel implements LoggerInterface
{
    /**
     * TRANSACTION TYPES
     */
    const TYPE_BONUS = 'bonus';
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_INVESTMENT = 'investment';
    const TYPE_EARLY_REPAYMENT = 'early_repayment';
    const TYPE_REPAYMENT = 'repayment';
    const TYPE_INSTALLMENT_REPAYMENT = 'installment_repayment';
    const TYPE_BUYBACK_OVERDUE = 'buyback_overdue';
    const TYPE_BUYBACK_MANUAL = 'buyback_manual';
    const TYPE_SECONDARY_MARKET_SELL = 'sm_sell';
    const TYPE_SECONDARY_MARKET_BUY = 'sm_buy';
    const TYPE_SECONDARY_MARKET_PREMIUM = 'sm_premium';

    const SECONDARY_MARKET_DETAILS_SALE = 'Secondary Market Sale';
    const SECONDARY_MARKET_DETAILS_SALE_PREMIUM = 'Secondary Market Discount/Premium';
    const SECONDARY_MARKET_DETAILS_INVESTMENT = 'Secondary Market Investments';
    const SECONDARY_MARKET_DETAILS_INVESTMENT_PREMIUM = 'Secondary Market Discount/Premium';

    /**
     * ACCOUNT STATEMENT TYPES
     */
    const AC_TYPE_BONUS = 'bonus'; // amount
    const AC_TYPE_DEPOSIT = 'deposit'; // amount
    const AC_TYPE_WITHDRAW = 'withdraw'; // amount
    const AC_TYPE_INVESTMENT = 'investment'; // amount

    // repayment and installment_repayment
    const AC_TYPE_REPAYMENT_PRINCIPAL = 'repayment_principal';
    const AC_TYPE_REPAYMENT_INTEREST = 'repayment_interest';
    const AC_TYPE_REPAYMENT_LATE_INTEREST = 'repayment_late_interest';

    const AC_TYPE_EARLY_REPAYMENT_PRINCIPAL = 'early_repayment_principal';
    const AC_TYPE_EARLY_REPAYMENT_INTEREST = 'early_repayment_interest';
    const AC_TYPE_EARLY_REPAYMENT_LATE_INTEREST = 'early_repayment_late_interest';

    // buyback_manual and buyback_overdue
    const AC_TYPE_REPURCHASED_PRINCIPAL = 'repurchased_principal';
    const AC_TYPE_REPURCHASED_INTEREST = 'repurchased_interest';
    const AC_TYPE_REPURCHASED_LATE_INTEREST = 'repurchased_late_interest';

    const AC_TYPE_SECONDARY_MARKET_BUY = 'sm_buy';
    const AC_TYPE_SECONDARY_MARKET_SELL = 'sm_sell';
    const AC_TYPE_SECONDARY_MARKET_PREMIUM = 'sm_premium';


    /**
     * ACCOUNT STATEMENT LABELS
     */
    const AC_LABEL_BONUS = 'Bonus received';
    const AC_LABEL_DEPOSIT = 'Deposited funds';
    const AC_LABEL_WITHDRAW = 'Withdrawn funds';
    const AC_LABEL_INVESTMENT = 'Investments in loans';

    const AC_LABEL_REPAYMENT_PRINCIPAL = 'Principal received';
    const AC_LABEL_REPAYMENT_INTEREST = 'Interest received';
    const AC_LABEL_REPAYMENT_LATE_INTEREST = 'Late interest received';

    const AC_LABEL_EARLY_REPAYMENT_PRINCIPAL = 'Principal received from early repayment';
    const AC_LABEL_EARLY_REPAYMENT_INTEREST = 'Interest received from early repayment';
    const AC_LABEL_EARLY_REPAYMENT_LATE_INTEREST = 'Late interest from early repayment';

    const AC_LABEL_REPURCHASED_PRINCIPAL = 'Principal received from loan repurchase';
    const AC_LABEL_REPURCHASED_INTEREST = 'Interest received from loan repurchase';
    const AC_LABEL_REPURCHASED_LATE_INTEREST = 'Late interest received from loan repurchase';

    const AC_LABEL_SECONDARY_MARKET_BUY = 'Secondary market investments';
    const AC_LABEL_SECONDARY_MARKET_SELL = 'Secondary market sales';
    const AC_LABEL_SECONDARY_MARKET_PREMIUM = 'Secondary market discount/premium';


    /**
     * ADMIN TRANSACTIONS LABELS
     */
    const LABEL_ADMIN_BONUS = 'Bonus received';
    const LABEL_ADMIN_DEPOSIT = 'Deposited funds';
    const LABEL_ADMIN_WITHDRAW = 'Withdrawn funds';
    const LABEL_ADMIN_INVESTMENTS = 'Investments in loans';
    const LABEL_ADMIN_EARLY_REPAYMENT = 'Early Repayment';
    const LABEL_ADMIN_INSTALLMENT_REPAYMENT = 'Installment Repayment';
    const LABEL_ADMIN_REPAYMENT = 'Repayment';
    const LABEL_ADMIN_BUYBACK_MANUAL = 'BuyBack Manual';
    const LABEL_ADMIN_BUYBACK_OVERDUE = 'BuyBack Overdue';

    const LABEL_ADMIN_SECONDARY_MARKET_BUY = 'Secondary market investments';
    const LABEL_ADMIN_SECONDARY_MARKET_SELL = 'Secondary market sales';
    const LABEL_ADMIN_SECONDARY_MARKET_PREMIUM = 'Secondary market discount/premium';


    /**
     * Transaction payment type
     */
    const PAY_TYPE_PRINCIPAL = 'principal';
    const PAY_TYPE_INTEREST = 'interest';
    const PAY_TYPE_LATE_INTEREST = 'late_interest';
    const PAY_TYPE_ACCR_INTEREST = 'accrued_interest';


    const DIRECTION_IN = 'in';
    const DIRECTION_OUT = 'out';
    const RAW_START_DATE = '2021-02-01';

    /**
     * @var string
     */
    protected $table = 'transaction';

    /**
     * @var string
     */
    protected $primaryKey = 'transaction_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'transaction_id',
        'active',
        'deleted',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'enabled_at',
        'enabled_by',
        'disabled_at',
        'disabled_by',
        // 'created_at', - we overwrite it
    ];

    private $current;

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::observe(TransactionObserver::class);
    }

    /**
     * @return string[]
     */
    public static function getTypes()
    {
        return [
            self::TYPE_BONUS,
            self::TYPE_DEPOSIT,
            self::TYPE_EARLY_REPAYMENT,
            self::TYPE_INSTALLMENT_REPAYMENT,
            self::TYPE_INVESTMENT,
            self::TYPE_REPAYMENT,
            self::TYPE_WITHDRAW,
            self::TYPE_BUYBACK_MANUAL,
            self::TYPE_BUYBACK_OVERDUE,
            self::TYPE_SECONDARY_MARKET_BUY,
            self::TYPE_SECONDARY_MARKET_SELL,
            self::TYPE_SECONDARY_MARKET_PREMIUM,
        ];
    }

    public static function getRepaymentStatuses(): array
    {
        return [
            self::TYPE_BUYBACK_OVERDUE,
            self::TYPE_BUYBACK_MANUAL,
            self::TYPE_REPAYMENT,
            self::TYPE_EARLY_REPAYMENT,
            self::TYPE_INSTALLMENT_REPAYMENT,
        ];
    }

    /**
     * @return string[]
     */
    public static function getAccountStatementTypes(
        bool $orderByLabel = true
    ): array {
        $arr = [
            self::AC_TYPE_BONUS => self::AC_LABEL_BONUS,
            self::AC_TYPE_DEPOSIT => self::AC_LABEL_DEPOSIT,
            self::AC_TYPE_WITHDRAW => self::AC_LABEL_WITHDRAW,
            self::AC_TYPE_INVESTMENT => self::AC_LABEL_INVESTMENT,
            self::AC_TYPE_REPAYMENT_PRINCIPAL => self::AC_LABEL_REPAYMENT_PRINCIPAL,
            self::AC_TYPE_REPAYMENT_INTEREST => self::AC_LABEL_REPAYMENT_INTEREST,
            self::AC_TYPE_REPAYMENT_LATE_INTEREST => self::AC_LABEL_REPAYMENT_LATE_INTEREST,
            self::AC_TYPE_EARLY_REPAYMENT_PRINCIPAL => self::AC_LABEL_EARLY_REPAYMENT_PRINCIPAL,
            self::AC_TYPE_EARLY_REPAYMENT_INTEREST => self::AC_LABEL_EARLY_REPAYMENT_INTEREST,
            self::AC_TYPE_EARLY_REPAYMENT_LATE_INTEREST => self::AC_LABEL_EARLY_REPAYMENT_LATE_INTEREST,
            self::AC_TYPE_REPURCHASED_PRINCIPAL => self::AC_LABEL_REPURCHASED_PRINCIPAL,
            self::AC_TYPE_REPURCHASED_INTEREST => self::AC_LABEL_REPURCHASED_INTEREST,
            self::AC_TYPE_REPURCHASED_LATE_INTEREST => self::AC_LABEL_REPURCHASED_LATE_INTEREST,

            self::AC_TYPE_SECONDARY_MARKET_SELL => self::AC_LABEL_SECONDARY_MARKET_SELL,
            self::AC_TYPE_SECONDARY_MARKET_BUY => self::AC_LABEL_SECONDARY_MARKET_BUY,
            self::AC_TYPE_SECONDARY_MARKET_PREMIUM => self::AC_LABEL_SECONDARY_MARKET_PREMIUM,
        ];

        if (!$orderByLabel) {
            return $arr;
        }

        // we doing this shit, to order the list of types by label
        $arrayFlip = array_flip($arr);
        ksort($arrayFlip);
        $arr = array_flip($arrayFlip);

        return $arr;
    }

    public static function getLabelForKey(string $key): string
    {
        $map = self::getAccountStatementTypes(false);
        if (array_key_exists($key, $map)) {
            return $map[$key];
        }

        return $key;
    }

    /**
     * Get array with transaction types, where we use only field - amount
     * @return array
     */
    public static function getAmountTypes(): array
    {
        return [
            self::TYPE_BONUS,
            self::TYPE_DEPOSIT,
            self::TYPE_WITHDRAW,
            self::TYPE_INVESTMENT,
            self::TYPE_SECONDARY_MARKET_BUY,
            self::TYPE_SECONDARY_MARKET_SELL,
            self::TYPE_SECONDARY_MARKET_PREMIUM,
        ];
    }

    /**
     * @param string $type
     * @return string
     */
    public static function getAdminLabel(string $type): string
    {
        $mapping = [
            self::TYPE_BONUS => self::LABEL_ADMIN_BONUS,
            self::TYPE_DEPOSIT => self::LABEL_ADMIN_DEPOSIT,
            self::TYPE_WITHDRAW => self::LABEL_ADMIN_WITHDRAW,
            self::TYPE_INVESTMENT => self::LABEL_ADMIN_INVESTMENTS,
            self::TYPE_EARLY_REPAYMENT => self::LABEL_ADMIN_EARLY_REPAYMENT,
            self::TYPE_REPAYMENT => self::LABEL_ADMIN_REPAYMENT,
            self::TYPE_INSTALLMENT_REPAYMENT => self::LABEL_ADMIN_INSTALLMENT_REPAYMENT,
            self::TYPE_BUYBACK_MANUAL => self::LABEL_ADMIN_BUYBACK_MANUAL,
            self::TYPE_BUYBACK_OVERDUE => self::LABEL_ADMIN_BUYBACK_OVERDUE,

            self::AC_TYPE_SECONDARY_MARKET_SELL => self::LABEL_ADMIN_SECONDARY_MARKET_SELL,
            self::AC_TYPE_SECONDARY_MARKET_BUY => self::LABEL_ADMIN_SECONDARY_MARKET_BUY,
            self::AC_TYPE_SECONDARY_MARKET_PREMIUM => self::LABEL_ADMIN_SECONDARY_MARKET_PREMIUM,
        ];

        if (empty($mapping[$type])) {
            return $type;
        }

        return $mapping[$type];
    }

    /**
     * @return string[]
     */
    public static function getDirections()
    {
        return [
            self::DIRECTION_IN,
            self::DIRECTION_OUT,
        ];
    }

    /**
     * @param $bankTransactionId
     * @return bool
     */
    public static function existsById($bankTransactionId)
    {
        return (self::where('bank_transaction_id', $bankTransactionId)->count() > 0);
    }

    /**
     * @return BelongsTo
     */
    public function investor(): BelongsTo
    {
        return $this->belongsTo(
            Investor::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function wallet()
    {
        return $this->belongsTo(
            Wallet::class,
            'wallet_id',
            'wallet_id'
        );
    }

    ///////////////////////////// STATS ////////////////////////////

    public function getDepositForDate(string $date): float
    {
        return (double)$this::where(
            [
                'direction' => self::DIRECTION_IN,
                'type' => self::TYPE_DEPOSIT,
                'active' => 1,
                'deleted' => 0
            ]
        )->whereBetween(
            'created_at',
            [
                $date . " 00:00:00",
                $date . " 23:59:59"
            ]
        )->sum('amount');
    }

    /**
     * @param string $date
     * @param Originator $originator
     *
     * @return array
     */
    public function getRebuyAmountsForDate(string $date, Originator $originator): array
    {
        return current(
            $this::selectRaw(
                'sum(principal) as principal, sum(accrued_interest) as accrued_interest, sum(interest) as interest, sum(late_interest) as late_interest'
            )
                ->where('originator_id', $originator->originator_id)
                ->where(
                    [
                        'direction' => self::DIRECTION_OUT,
                        'active' => 1,
                        'deleted' => 0
                    ]
                )->whereIn(
                    'type',
                    [
                        self::TYPE_BUYBACK_OVERDUE,
                        self::TYPE_BUYBACK_MANUAL,
                    ]
                )->whereBetween(
                    'created_at',
                    [
                        $date . " 00:00:00",
                        $date . " 23:59:59"
                    ]
                )->get()->toArray()
        );
    }

    public function getRebuyAmountsForPeriod(string $from, string $to, Originator $originator): array
    {
        return current(
            $this::selectRaw(
                'sum(principal) as principal, sum(accrued_interest) as accrued_interest, sum(interest) as interest, sum(late_interest) as late_interest'
            )
                ->where('originator_id', $originator->originator_id)
                ->where(
                    [
                        'direction' => self::DIRECTION_OUT,
                        'active' => 1,
                        'deleted' => 0
                    ]
                )->whereIn(
                    'type',
                    [
                        self::TYPE_BUYBACK_OVERDUE,
                        self::TYPE_BUYBACK_MANUAL,
                    ]
                )->whereBetween(
                    'created_at',
                    [
                        $from . " 00:00:00",
                        $to . " 23:59:59"
                    ]
                )->get()->toArray()
        );
    }

    /**
     * @param string $date
     * @param Originator $originator
     *
     * @return array
     */
    public function getRepaidAmountsForDate(string $date, Originator $originator): array
    {
        $this->current = current(
            $this::selectRaw(
                'sum(principal) as principal, sum(accrued_interest) as accrued_interest, sum(interest) as interest, sum(late_interest) as late_interest'
            )
                ->where('originator_id', $originator->originator_id)
                ->where(
                    [
                        'direction' => self::DIRECTION_OUT,
                        'active' => 1,
                        'deleted' => 0
                    ]
                )->whereIn(
                    'type',
                    [
                        self::TYPE_INSTALLMENT_REPAYMENT,
                        self::TYPE_EARLY_REPAYMENT,
                        self::TYPE_REPAYMENT,
                    ]
                )->whereBetween(
                    'created_at',
                    [
                        $date . " 00:00:00",
                        $date . " 23:59:59"
                    ]
                )->get()->toArray()
        );
        return $this->current;
    }

    public function getRepaidAmountsForPeriod(string $from, string $to, Originator $originator): array
    {
        return current(
            $this::selectRaw(
                'sum(principal) as principal, sum(accrued_interest) as accrued_interest, sum(interest) as interest, sum(late_interest) as late_interest'
            )
                ->where('originator_id', $originator->originator_id)
                ->where(
                    [
                        'direction' => self::DIRECTION_OUT,
                        'active' => 1,
                        'deleted' => 0
                    ]
                )->whereIn(
                    'type',
                    [
                        self::TYPE_INSTALLMENT_REPAYMENT,
                        self::TYPE_EARLY_REPAYMENT,
                        self::TYPE_REPAYMENT,
                    ]
                )->whereBetween(
                    'created_at',
                    [
                        $from . " 00:00:00",
                        $to . " 23:59:59"
                    ]
                )->get()->toArray()
        );
    }

    public function getOutstandingBalance(Originator $originator, string $to, string $from = null): float
    {
        if (empty($from)) {
            $from = self::RAW_START_DATE; // Just begin of begins :D
        }

        $invested = (float)(current(
            $this::selectRaw(
                'sum(amount) as amount'
            )
                ->where('originator_id', $originator->originator_id)
                ->where(
                    [
                        'direction' => self::DIRECTION_IN,
                        'active' => 1,
                        'deleted' => 0
                    ]
                )->whereIn(
                    'type',
                    [
                        self::TYPE_INVESTMENT,
                    ]
                )
                ->where('created_at', '>=', $from . " 00:00:00")
                ->where('created_at', '<=', $to)
                ->get()
                ->toArray()
        ))['amount'];

        // TMP dirty fix on february data
        // since we were creating repayments on 00:00:00
        // then we change it to 23:59:59
        // so part of repayments goes wrongly on previous date
        // because of this, below IF has been added
        // will bre removed next month
        $compareSign = '<=';
        if ($to == '2021-03-01 00:00:00') {
            $compareSign = '<';
        }

        $repaidPrincipal = (float)(current(
            $this::selectRaw(
                'sum(principal) as amount'
            )
                ->where('originator_id', $originator->originator_id)
                ->where(
                    [
                        'direction' => self::DIRECTION_OUT,
                        'active' => 1,
                        'deleted' => 0
                    ]
                )->whereIn(
                    'type',
                    [
                        self::TYPE_BUYBACK_OVERDUE,
                        self::TYPE_BUYBACK_MANUAL,
                        self::TYPE_INSTALLMENT_REPAYMENT,
                        self::TYPE_EARLY_REPAYMENT,
                        self::TYPE_REPAYMENT,
                    ]
                )
                ->where('created_at', '>=', $from . " 00:00:00")
                ->where('created_at', $compareSign, $to)
                ->get()
                ->toArray()
        ))['amount'];

        return ($invested - $repaidPrincipal);
    }

    /**
     * @param string $date
     * @param Originator $originator
     *
     * @return float
     */
    public function getInvestedAmountForDate(string $date, Originator $originator): float
    {
        $result = $this::selectRaw('sum(amount) as amount')
            ->where('originator_id', $originator->originator_id)
            ->where(
                [
                    'direction' => self::DIRECTION_IN,
                    'active' => 1,
                    'deleted' => 0
                ]
            )->whereIn(
                'type',
                [
                    self::TYPE_INVESTMENT,
                ]
            )->whereBetween(
                'created_at',
                [
                    $date . " 00:00:00",
                    $date . " 23:59:59"
                ]
            )->first();

        return $result->amount ?? 0.00;
    }

    public function getInvestedAmountForPeriod(string $from, string $to, Originator $originator): float
    {
        $result = $this::selectRaw('sum(amount) as amount')
            ->where('originator_id', $originator->originator_id)
            ->where(
                [
                    'direction' => self::DIRECTION_IN,
                    'active' => 1,
                    'deleted' => 0
                ]
            )->whereIn(
                'type',
                [
                    self::TYPE_INVESTMENT,
                ]
            )->whereBetween(
                'created_at',
                [
                    $from . " 00:00:00",
                    $to . " 23:59:59"
                ]
            )->first();

        return $result->amount ?? 0.00;
    }

    /**
     * @param int|null $days
     * @return array
     */
    public static function getTransactionsPerDay(int $days = null): array
    {
        $dayTo = Carbon::today()->subDays($days)->format('Y-m-d') . ' 00:00:01';
        $daysStats = "TO_CHAR(t.created_at , 'YYYY-mm-dd') as created";

        $results = DB::select(
            DB::raw(
                "select
                    :investment as type,
                   " . $daysStats . ",
                    sum(amount) as number
                from transaction as t
                where t.type = :investment
                and t.created_at >= '" . $dayTo . "'
                and t.active = 1 and deleted = 0
                group by created
                UNION
                select
                    :deposit as type,
                    " . $daysStats . ",
                    sum(amount) as number
                from transaction as t
                where t.type = :deposit
                and t.created_at >= '" . $dayTo . "'
                and t.active = 1 and deleted = 0
                group by created
                UNION
                select
                    :withdraw as type,
                    " . $daysStats . ",
                    sum(amount) as number
                from transaction as t
                where t.type = :withdraw
                and t.created_at >= '" . $dayTo . "'
                and t.active = 1 and deleted = 0
                group by created
                ORDER BY created;
                "
            ),
            [
                'deposit' => Transaction::TYPE_DEPOSIT,
                'withdraw' => Transaction::TYPE_WITHDRAW,
                'investment' => Transaction::TYPE_INVESTMENT,
            ]
        );

        return array_map(
            function ($value) {
                return (array)$value;
            },
            $results
        );
    }
}
