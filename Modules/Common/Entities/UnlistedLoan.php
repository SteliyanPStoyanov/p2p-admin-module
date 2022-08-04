<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class UnlistedLoan extends BaseModel implements LoggerInterface
{
    public const STATUS_DEFAULT = 'default';
    public const STATUS_NOT_EXISTS = 'not_exists';
    public const STATUS_ALREADY_UNLISTED = 'already_unlisted';

    protected $table = 'unlisted_loan';

    protected $primaryKey = 'unlisted_loan_id';

    protected $fillable = [
        'lender_id',
        'handled',
    ];

    public static function getStatuses()
    {
        return [
            self::STATUS_DEFAULT,
            self::STATUS_ALREADY_UNLISTED,
            self::STATUS_NOT_EXISTS,
        ];
    }
}
