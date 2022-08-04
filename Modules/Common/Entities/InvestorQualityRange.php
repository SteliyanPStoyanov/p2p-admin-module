<?php

namespace Modules\Common\Entities;

use Carbon\Carbon;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class InvestorQualityRange extends BaseModel implements LoggerInterface
{
    protected $table = 'investor_quality_range';

    protected $primaryKey = 'investor_quality_range_id';

    protected $guarded = [
        'investor_quality_range_id',
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
}
