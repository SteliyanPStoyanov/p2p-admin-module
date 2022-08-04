<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class AffiliateInvestor extends BaseModel implements LoggerInterface
{

    /**
     * @var string
     */
    protected $table = 'affiliate_investor';

    /**
     * @var string
     */
    protected $primaryKey = 'affiliate_investor_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'investor_id',
        'affiliate_id',
        'client_id',
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
