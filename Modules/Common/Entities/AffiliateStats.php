<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class AffiliateStats extends BaseModel implements LoggerInterface
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'affiliate_stats';

    /**
     * @var string
     */
    protected $primaryKey = 'affiliate_stats_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'investor_id',
        'affiliate_id',
        'send_data',
        'api_address',
        'response',
        'send_at',
        'received_at'
    ];

    /**
     * @return BelongsTo
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(
            Affiliate::class,
            'affiliate_id',
            'affiliate_id'
        );
    }
}
