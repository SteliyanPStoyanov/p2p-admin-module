<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Modules\Common\Observers\InvestmentObserver;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Investment extends BaseModel implements LoggerInterface
{
     public const STATUS_ACTIVE = 1;

    /**
     * @var string
     */
    protected $table = 'investment';

    /**
     * @var string
     */
    protected $primaryKey = 'investment_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'investment_id',
        'active',
        'deleted',
        // 'created_at', - // we use for test custom dates
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

        self::observe(InvestmentObserver::class);
    }

    /**
     * @return BelongsTo
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(
            Loan::class,
            'loan_id',
            'loan_id'
        );
    }

    /**
     * @return Wallet
     */
    public function wallet()
    {
        return Wallet::where(
            [
                'wallet_id' => $this->wallet_id,
            ]
        )->first();
    }

    /**
     * @return Investor
     */
    public function investor()
    {
        return Investor::where(
            [
                'investor_id' => $this->investor_id,
            ]
        )->first();
    }

    /**
     * @return BelongsTo
     */
    public function investmentBunch(): BelongsTo
    {
        return $this->belongsTo(
            InvestmentBunch::class,
            'investment_bunch_id',
            'investment_bunch_id'
        );
    }

     /**
     * @return InvestorInstallmentHistory
     * @return InvestorInstallment
     */
    public function getInvestorInstallments()
    {
        if ($this->loan->isFinished()) {
            return InvestorInstallmentHistory::where(
                [
                    'investment_id' => $this->investment_id,
                ]
            )->orderBy('installment_id', 'asc')->get()->all();
        }

        return InvestorInstallment::where(
            [
                'investment_id' => $this->investment_id,
            ]
        )->orderBy('installment_id', 'asc')->get()->all();
    }

    /**
     * @return InvestorInstallment
     */
    public function getFirstInvestorInstallment()
    {
        return InvestorInstallment::where(
            [
                'investment_id' => $this->investment_id,
            ]
        )->orderBy('investor_installment_id', 'ASC')->first();
    }

    /**
     *  Investor Outstanding Installments
     *  Sum
     */
    public function getInvestorOutstandingInstallments()
    {
        return DB::selectOne(
            DB::raw(
                " select sum(ii.principal)
                        from investor_installment ii
                        where
                              ii.investment_id = " . $this->investment_id . "
                            and ii.paid = 0
                            "
            )
        );
    }

    /**
     * @return Transaction
     */
    public function getTransactionByKey()
    {
        return Transaction::where('key', $this->key)->first();
    }
}
