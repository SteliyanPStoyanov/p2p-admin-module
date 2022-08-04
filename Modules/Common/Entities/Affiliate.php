<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Affiliate extends BaseModel implements LoggerInterface
{

    public const AFFILIATE_SOURCE = [
        'doaff' => 'Modules\Common\Affiliates\DoAffiliate'
    ];

    /**
     * @var string
     */
    protected $table = 'affiliate';

    /**
     * @var string
     */
    protected $primaryKey = 'affiliate_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'affiliate_id',
        'affiliate_description'
    ];

    /**
     * @return HasMany
     */
    public function affiliateInvestors(): HasMany
    {
        return $this->hasMany(
            AffiliateInvestor::class,
            'affiliate_id',
            'affiliate_id'
        );
    }

    /**
     * @return HasMany
     */
    public function affiliateStats(): HasMany
    {
        return $this->hasMany(
            AffiliateStats::class,
            'affiliate_id',
            'affiliate_id'
        );
    }
}
