<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class InvestorAgreement extends BaseModel implements LoggerInterface
{
    const UPDATED_AT = null;

    protected $table = 'investor_agreement';

    protected $primaryKey = 'investor_agreement_id';

    protected $guarded = [
        'investor_agreement_id',
        'created_at',
        'created_by',
    ];
}
