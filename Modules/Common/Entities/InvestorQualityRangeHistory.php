<?php

namespace Modules\Common\Entities;

use Carbon\Carbon;
use Modules\Core\Models\BaseModel;

class InvestorQualityRangeHistory extends BaseModel
{
    protected $table = 'investor_quality_range_history';

    protected $primaryKey = 'history_id';

    protected $guarded = [
        'history_id',
    ];
}
