<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class InvestorInstallmentHistory extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'investor_installment_history';

    /**
     * @var string
     */
    protected $primaryKey = 'history_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'history_id',
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
        'archived_at',
        'archived_by'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function investor()
    {
        return $this->belongsTo(
            Investor::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loan()
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
}
