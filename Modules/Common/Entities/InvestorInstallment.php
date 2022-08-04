<?php

namespace Modules\Common\Entities;

use Carbon\Carbon;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Common\Observers\InvestorInstallmentObserver;
use Modules\Core\Models\BaseModel;

class InvestorInstallment extends BaseModel implements LoggerInterface
{

    public const INVESTOR_INSTALLMENT_PAID_ID = 1;
    public const INVESTOR_INSTALLMENT_NOT_PAID = 0;

    protected $table = 'investor_installment';

    protected $primaryKey = 'investor_installment_id';

    protected $guarded = [
        'investor_installment',
        'active',
        'deleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'enabled_at',
        'enabled_by',
        'disabled_at',
        'disabled_by',
    ];


    public static function boot() 
    {
        parent::boot();
        self::observe(InvestorInstallmentObserver::class);
    }

    public function loan()
    {
        return Loan::where('loan_id', $this->loan_id)->first();
    }

    public function loanRelation()
    {
        return $this->belongsTo(
            Loan::class,
            'loan_id',
            'loan_id'
        );
    }

    public function installment()
    {
        return Installment::where('installment_id', $this->installment_id)->first();
    }

    public function investor()
    {
        return Investor::where('investor_id', $this->investor_id)->first();
    }

    public function investment()
    {
        return Investment::where('investment_id', $this->investment_id)->first();
    }

    public function pay(Carbon $repaymentDate = null)
    {
        if (null === $repaymentDate) {
            $repaymentDate = Carbon::now();
        }

        $this->paid = 1;
        $this->paid_at = $repaymentDate;

        $this->save();
    }

    public function getInstallmentIncome()
    {
        if ($this->accrued_interest < $this->interest) {
            return $this->accrued_interest;
        }

        return ($this->interest + $this->late_interest);
    }

    public function getInstallmentInterest()
    {
        if ($this->accrued_interest < $this->interest) {
            return $this->accrued_interest;
        }

        return $this->interest;
    }
}
