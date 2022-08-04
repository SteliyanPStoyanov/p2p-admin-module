<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Agreement extends BaseModel implements LoggerInterface
{
    public const TYPE_REGISTRATION = 'registration';
    public const TYPE_NOTIFICATION = 'notification';

    public const USER_AGREEMENT_ID = 1;
    public const RECEIVE_MARKETING_COMMUNICATION_ID = 2;
    public const RECEIVE_FUNDS_NOTIFICATION_ID = 3;
    public const WITHDRAW_REQUEST_NOTIFICATION_ID = 4;
    public const NEW_DEVICE_NOTIFICATION = 5;

    protected $table = 'agreement';

    protected $primaryKey = 'agreement_id';

    protected $guarded = [
        'agreement_id',
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

    public static function getTypes(): array
    {
        return [
            self::TYPE_NOTIFICATION,
            self::TYPE_REGISTRATION,
        ];
    }
}
