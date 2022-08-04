<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseAuthModel;

class InvestorBonus extends BaseAuthModel implements LoggerInterface
{
    protected $guard = 'investor_bonus';
    protected $table = 'investor_bonus';
    protected $primaryKey = 'investor_bonus_id';

    protected $fillable = [
        'investor_id',
        'from_investor_id',
        'amount',
        'handled',
        'date',
        'investor_bonus_id',
    ];

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
     public function investorReferral(): BelongsTo
     {
        return $this->belongsTo(
            Investor::class,
            'from_investor_id',
            'investor_id'
        );
    }
}
