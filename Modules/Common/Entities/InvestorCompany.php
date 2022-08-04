<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;

class InvestorCompany extends BaseModel
{
    const UPDATED_AT = null;
    const CREATED_AT = null;
    /**
     * @var string
     */
    protected $table = 'investor_company';

    /**
     * @var string
     */
    protected $primaryKey = 'investor_company_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'investor_id',
        'name',
        'number',
        'address',
        'country_id',
    ];

    /**
     * @return BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(
            Country::class,
            'country_id',
            'country_id'
        );
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
}
