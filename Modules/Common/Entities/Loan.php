<?php

namespace Modules\Common\Entities;

use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Setting;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Modules\Common\Observers\LoanObserver;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Loan extends BaseModel implements LoggerInterface
{
    public const DB_SITE = 'sqlsrv_site';
    public const DB_OFFICE = 'sqlsrv_office';

    public const TYPE_PAYDAY = 'payday';
    public const TYPE_INSTALLMENTS = 'installments';
    public const LABEL_TYPE_INSTALLMENT = 'Instalment loan';
    public const LABEL_TYPE_PAYDAY = 'Short-term loan';
    public const STATUS_NEW = 'new';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_REBUY = 'rebuy';
    public const STATUS_REPAID = 'repaid';
    public const STATUS_REPAID_EARLY = 'repaid_early';
    public const STATUS_REPAID_EARLY_LABEL = 'Repaid early';
    public const STATUS_REPAID_LATE_LABEL = 'Repaid late';

    // We dont have status 'finish', we using this only for filter in loan list,
    // when we search by `finish` actually we search by STATUS_REPAID_EARLY or STATUS_REPAID or STATUS_REBUY
    public const STATUS_FINISH = 'finish';

    public const LABEL_NOT_READY = 'Not Ready';
    public const LABEL_ACTIVE = 'Active';
    public const LABEL_FINISHED = 'Finished';
    public const LABEL_REPURCHASED = 'Repurchased';

    public const PAY_STATUS_NONE = 'none';
    public const PAY_STATUS_CURRENT = 'current';
    public const PAY_STATUS_1_15 = '1-15 days';
    public const PAY_STATUS_16_30 = '16–30 days';
    public const PAY_STATUS_31_60 = '31–60 days';
    public const PAY_STATUS_LATE = 'late';
    public const PAY_STATUS_60_PLUS_DAYS = '60+ Days';
    public const PAY_STATUS_60_PLUS_DAYS_LATE = '60+ days late';

    protected $table = 'loan';

    protected $primaryKey = 'loan_id';

    protected $fillable = [
        'originator_id',
        'lender_id',
        'type',
        'from_office',
        'country_id',
        'currency_id',
        'lender_issue_date',
        'final_payment_date',
        'period',
        'prepaid_schedule_payments',
        'amount',
        'amount_afranga',
        'amount_available',
        'remaining_principal',
        'interest_rate_percent',
        'buyback',
        'contract_tempate_id',
        'assigned_origination_fee_share',
        'borrower_age',
        'borrower_gender',
        'status',
        'payment_status',
        'unlisted',
        'blocked',
        'overdue_days',
    ];

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        self::observe(LoanObserver::class);
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_PAYDAY,
            self::TYPE_INSTALLMENTS,
        ];
    }

    public static function getTypesWithLabels(string $key = null)
    {
        $map = [
            self::TYPE_INSTALLMENTS => self::LABEL_TYPE_INSTALLMENT,
            self::TYPE_PAYDAY => self::LABEL_TYPE_PAYDAY
        ];

        if (!empty($key)) {
            return $map[$key];
        }

        return $map;
    }

    /**
     * @return array
     */
    public static function getGenders(): array
    {
        return [
            'male',
            'female',
        ];
    }

    /**
     * @return array
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW,
            self::STATUS_ACTIVE,
            self::STATUS_REPAID,
            self::STATUS_REBUY,
            self::STATUS_REPAID_EARLY,
        ];
    }


    public static function getMainStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_FINISH,
        ];
    }

    /**
     * @return array
     */
    public static function getFinalStatuses(): array
    {
        return [
            self::STATUS_REBUY,
            self::STATUS_REPAID,
            self::STATUS_REPAID_EARLY,
        ];
    }

    /**
     * @return array
     */
    public static function getPaymentStatuses(): array
    {
        return [
            self::PAY_STATUS_CURRENT,
            self::PAY_STATUS_1_15,
            self::PAY_STATUS_16_30,
            self::PAY_STATUS_31_60,
            self::PAY_STATUS_LATE,
        ];
    }

    /**
     * @return array
     */
    public static function getFinalPaymentStatuses(): array
    {
        return [
            lcfirst(self::LABEL_REPURCHASED),
            lcfirst(self::STATUS_REPAID_EARLY_LABEL),
            lcfirst(self::STATUS_REPAID_LATE_LABEL),
            lcfirst(self::LABEL_FINISHED),
        ];
    }

    public function installments()
    {
        return Installment::where('loan_id', $this->loan_id)
            ->orderBy('seq_num', 'ASC')
            ->get()
            ->all();
    }

    public function getUnpaidInstallments()
    {
        return Installment::where('loan_id', $this->loan_id)
            ->where('paid', '=', 0)
            ->orderBy('installment_id', 'ASC')
            ->get()
            ->all();
    }

    public function getLateInstallments(int $afterInstallmentId = null)
    {
        $builder = Installment::where('loan_id', $this->loan_id);
        $builder->where('paid', '=', 0);
        if ($afterInstallmentId) {
            $builder->where('installment_id', '>', $afterInstallmentId);
        }
        $builder->where('due_date', '<', Carbon::today()->format('Y-m-d'));
        $builder->orderBy('installment_id', 'ASC');

        return $builder->get()->all();
    }

    public function block()
    {
        $this->blocked = 1;
        $this->blocked_at = Carbon::now();
        $this->save();
    }

    public function unblock()
    {
        $this->blocked = 0;
        $this->blocked_at = null;
        $this->save();
    }

    public function repaid(bool $early = false, Carbon $repaymentDate = null)
    {
        if (empty($repaymentDate)) {
            $repaymentDate = Carbon::now();
        }

        $status = ($early ? self::STATUS_REPAID_EARLY : self::STATUS_REPAID);

        return $this->close($status, $repaymentDate);
    }

    public function rebuy(Carbon $repaymentDate = null)
    {
        if (empty($repaymentDate)) {
            $repaymentDate = Carbon::now();
        }
        return $this->close(self::STATUS_REBUY, $repaymentDate);
    }

    public function close(string $status, Carbon $repaymentDateTime)
    {
        if (!in_array($status, self::getFinalStatuses())) {
            throw new Exception("Could not close loan with NOT final status, provided status: " . $status);
        }

        $this->status = $status;
        $this->unlisted = 1;
        $this->unlisted_at = $repaymentDateTime;
        $this->final_payment_status = strtolower($this->getFinalPaymentStatus());
        $this->save();
    }

    /**
     * @return string
     */
    public function getFinalPaymentStatus()
    {
        if (Loan::STATUS_REBUY === $this->status) {
            return Loan::LABEL_REPURCHASED;
        }

        if ((Carbon::parse($this->final_payment_date)->startOfDay())->gt(Carbon::parse($this->unlisted_at))) {
            return Loan::STATUS_REPAID_EARLY_LABEL;
        }

        if ((Carbon::parse($this->final_payment_date)->startOfDay())->lt(Carbon::parse($this->unlisted_at))) {
            return Loan::STATUS_REPAID_LATE_LABEL;
        }

        return Loan::LABEL_FINISHED;
    }

    /**
     * @return mixed
     */
    public function getPaymentStatus()
    {
        return $this->isUnlisted() ? $this->final_payment_status : $this->payment_status;
    }

    public function isBlocked()
    {
        return (1 == $this->blocked);
    }

    /**
     * @return bool
     */
    public function isUnlisted()
    {
        return (1 == $this->unlisted);
    }

    public function sellAvailableAmount(float $amount): bool
    {
        if (!$this->isAvailableAmount($amount)) {
            return false;
        }

        $this->reduceAmountAvailable($amount);
        $this->save();

        return true;
    }

    public function reduceAmountAvailable(float $amount)
    {
        $this->amount_available = $this->amount_available - $amount;
    }

    /**
     * @param float $amount
     *
     * @return bool
     */
    public function isAvailableAmount(float $amount): bool
    {
        return ($this->amount_available >= $amount);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(
            Country::class,
            'country_id',
            'country_id'
        );
    }

    /**
     * @return array
     */
    public static function getLoansCountries(): array
    {
        return DB::select(
            DB::raw(
                "
                select
                    distinct l.country_id,
                    c.name
                from loan as l
                join country c on l.country_id = c.country_id
            "
            )
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function originator()
    {
        return $this->belongsTo(
            Originator::class,
            'originator_id',
            'originator_id'
        );
    }

    /**
     * @return array
     */
    public static function getLoansOriginators(): array
    {
        return DB::select(
            DB::raw(
                "select distinct  o.name, o.originator_id from loan as l
                        join originator o on l.originator_id = o.originator_id"
            )
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function investments()
    {
        return $this->hasMany(
            Investment::class,
            'loan_id',
            'loan_id'
        )->orderBy('investment_id', 'ASC');
    }

    public function distinctInvestments()
    {
        $builder = DB::table('investment');
        $builder->select(DB::raw('
            investment_id,
            investor_id,
            sum(amount) as amount,
            sum(percent) as percent
        '));
        $builder->where([
            ['loan_id', '=', $this->loan_id],
            ['active', '=', '1'],
            ['deleted', '=', '0'],
        ]);
        $builder->groupby( 'investment_id', 'investor_id');

        $records = Investment::hydrate($builder->get()->all());
        return $records->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function myLoanInvestments(int $investorId)
    {
        return $this->investments->where('investor_id', $investorId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function investorsLoanShare(int $investorId)
    {
        return $this->investments->where('investor_id', '!=', $investorId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency()
    {
        return $this->belongsTo(
            Currency::class,
            'currency_id',
            'currency_id'
        );
    }

    /**
     * @return mixed
     */
    public function getAssignedOriginationFeePercent()
    {
        return \SettingFacade::getOriginatorFeePercent();
    }

    /**
     * @return float|int
     *
     * @throws ProblemException
     */
    public function getLenderSkin()
    {
        return Calculator::getOriginatorAmount($this->remaining_principal, $this->getAssignedOriginationFeePercent());
    }

    /**
     * @return int|mixed
     *
     * @throws ProblemException
     */
    public function getAvailablePercentForInvest()
    {
        return Calculator::round(100 - $this->getLoanUsedPercent());
    }

    /**
     * @return mixed
     *
     * @throws ProblemException
     */
    public function getLoanUsedPercent()
    {
        $shares = $this->getInvestorSharedPercent();
        $shares += $this->getAssignedOriginationFeePercent();
        return $shares;
    }

    /**
     * @return int
     */
    public function getInvestorSharedCount()
    {
        return DB::table('investment')->where('loan_id', $this->loan_id)->count();
    }

    /**
     * @return float|int
     */
    public function getInvestorSharedAmount($order = 'desc')
    {
        return DB::table('investment')->where('loan_id', $this->loan_id)->orderBy('amount', $order)->get()->sum(
            'amount'
        );
    }

    /**
     * @return int
     */
    public function getInvestors()
    {
        $result = DB::select(
            DB::raw(
                "
                select distinct i.investor_id,concat_ws(' ', c.first_name,c.middle_name, c.last_name) as investor_names,
                sum(i.amount) as total_amount,
                sum(i.percent) as total_percent,
                c.residence
                from investment as i
                join investor c on i.investor_id = c.investor_id
                where i.loan_id = '" . $this->loan_id . "'
                group by i.investor_id, c.first_name,c.middle_name, c.last_name, c.residence
            "
            )
        );
        return Investor::hydrate($result);
    }

    /**
     * @return mixed
     */
    public function getInvestorSharedPercent()
    {
        $result = DB::table('investment')
            ->where('loan_id', $this->loan_id)
            ->get()
            ->sum('percent');

        return Calculator::round($result);
    }

    public function getFirstUnpaidInstallment($afterNow = false): ?Installment
    {
        if ($afterNow) {
            return Installment::where('loan_id', '=', $this->loan_id)
                ->where('paid', '=', 0)
                ->where('due_date', '>=', Carbon::now()->format('Y-m-d'))
                ->orderBy('lender_installment_id', 'ASC')
                ->first();
        }

        return Installment::where('loan_id', '=', $this->loan_id)
            ->where('paid', '=', 0)
            ->orderBy('seq_num', 'ASC')
            ->first();
    }

    public function addPayment(
        Installment $nextInstallment = null,
        Carbon $repaymentDate = null
    ) {
        // regular installment
        if ($nextInstallment) {
            $this->payment_status = $nextInstallment->status;
            $this->remaining_principal = $nextInstallment->remaining_principal;
            $this->amount_available = Calculator::getAvailableAmount(
                $nextInstallment->remaining_principal,
                $this->getLoanUsedPercent()
            );

            $now = !empty($repaymentDate) ? $repaymentDate : Carbon::now();
            $this->overdue_days = InstallmentCalculator::simpleDateDiff(
                $now,
                Carbon::parse($nextInstallment->due_date)
            );
        }

        $this->prepaid_schedule_payments = $this->prepaid_schedule_payments + 1;
        $this->save();
    }

    public function investorInstallments(bool $paid = null)
    {
        $builder = $this->hasMany(
            InvestorInstallment::class,
            'loan_id',
            'loan_id'
        );

        if (null !== $paid) {
            $builder->where('paid', (int)$paid);
        }

        $builder->orderBy('installment_id', 'asc');

        return $builder;
    }

    public static function getPaymentStatusByDate(string $date, Carbon $nowDate = null)
    {
        $now = (null !== $nowDate ? $nowDate : Carbon::today());
        $date = Carbon::parse($date);
        $date->hour(00);
        $date->minute(00);
        $date->second(00);

        if ($now->lte($date)) {
            return self::PAY_STATUS_CURRENT;
        }

        $diffInDays = InstallmentCalculator::simpleDateDiff(
            $now,
            $date
        );

        switch (true) {
            case $diffInDays > 0 && $diffInDays <= 15:
                $status = self::PAY_STATUS_1_15;
                break;
            case $diffInDays > 15 && $diffInDays <= 30:
                $status = self::PAY_STATUS_16_30;
                break;
            case $diffInDays > 30 && $diffInDays <= 60:
                $status = self::PAY_STATUS_31_60;
                break;
            case $diffInDays > 60:
                $status = self::PAY_STATUS_LATE;
                break;
            default:
                $status = self::PAY_STATUS_NONE;
                break;
        }

        return $status;
    }

    public function unlistedLoan()
    {
        return $this->hasOne(
            UnlistedLoan::class,
            'lender_id',
            'lender_id'
        )->where('handled', 0);
    }

    public function contracts()
    {
        return $this->hasMany(
            LoanContract::class,
            'loan_id',
            'loan_id'
        );
    }

    public function isFinished()
    {
        if (
            $this->status == Loan::STATUS_REBUY
            || $this->status == Loan::STATUS_REPAID
            || $this->status == Loan::STATUS_REPAID_EARLY
        ) {
            return true;
        }

        return false;
    }

    public function getAvailablePercent()
    {
        return (100 - (($this->remaining_principal - $this->amount_available) / $this->remaining_principal) * 100);
    }

    /**
     * @return HasMany
     */
    public function loansAmountAvailable(): HasMany
    {
        return $this->hasMany(
            LoanAmountAvailable::class,
            'loan_id',
            'loan_id'
        );
    }

    public static function getBlockedLoans(array $loanIds)
    {
        return Loan::whereIn('loan_id', $loanIds)->lockForUpdate()->get();
    }

    /**
     * @return int|mixed
     */
    public  function getInvestmentsPercent()
    {
        $builder = DB::table('investment');
        $builder->select(DB::raw('
            sum(percent) as percent
        '));
        $builder->where([
            ['loan_id', '=', $this->loan_id],
        ]);

       return $builder->first()->percent ?? 0;
    }
}
