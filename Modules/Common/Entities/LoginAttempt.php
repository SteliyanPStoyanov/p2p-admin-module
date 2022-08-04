<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class LoginAttempt extends BaseModel implements LoggerInterface
{
    const CREATED_AT = null;
    const UPDATED_AT = null;
    const MAX_ATTEMPT = 3;
    const ATTEMPT_TIME = 10; //min

    /**
     * @var string
     */
    protected $table = 'login_attempt';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'datetime',
        'email',
        'ip',
        'device',
    ];
}
