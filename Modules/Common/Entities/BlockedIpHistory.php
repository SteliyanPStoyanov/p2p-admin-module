<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class BlockedIpHistory extends BaseModel
{
    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'blocked_ip_history';

    /**
     * @var string
     */
    protected $primaryKey = 'history_id';
}
