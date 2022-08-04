<?php

namespace Modules\Common\Entities;

use Modules\Common\Entities\LoginAttempt;
use Modules\Common\Entities\RegistrationAttempt;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class BlockedIp extends BaseModel implements LoggerInterface
{
    const UPDATED_AT = null;
    public const BLOCKED_IP_REASON_LOGIN = 'login';
    public const BLOCKED_IP_REASON_REGISTER = 'register';
    public const BLOCKED_IP_REASON_FORGOT_PASSWORD = 'forgot_password';

    /**
     * @var string
     */
    protected $table = 'blocked_ip';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'ip',
        'blocked_till',
        'reason',
    ];

    public function getRelatedRecord()
    {
        if (self::BLOCKED_IP_REASON_LOGIN == $this->reason) {
            return LoginAttempt::where('ip', $this->ip)
                ->orderBy('id', 'DESC')
                ->first();
        }

        return RegistrationAttempt::where('ip', $this->ip)
            ->orderBy('id', 'DESC')
            ->first();
    }
}
