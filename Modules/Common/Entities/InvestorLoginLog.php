<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class InvestorLoginLog extends BaseModel
{
    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'investor_login_log';

    /**
     * @var string
     */
    protected $primaryKey = 'investor_login_log_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'investor_id',
        'ip',
        'device',
    ];
}
