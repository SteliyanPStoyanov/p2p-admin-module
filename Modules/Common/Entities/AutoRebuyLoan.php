<?php

namespace Modules\Common\Entities;

use Carbon\Carbon;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class AutoRebuyLoan extends BaseModel implements LoggerInterface
{
    const UPDATED_AT = null;

    protected $table = 'auto_rebuy_loan';

    protected $primaryKey = 'auto_rebuy_loan_id';

    protected $guarded = [
        'auto_rebuy_loan_id',
        'created_at',
        'created_by',
    ];
}
