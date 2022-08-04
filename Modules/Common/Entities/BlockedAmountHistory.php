<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class BlockedAmountHistory extends BaseModel implements LoggerInterface
{
    public const STATUS_BLOCKED = 'blocked';
    public const STATUS_PAID = 'paid';
    public const STATUS_RETURNED = 'returned';

    /**
     * @var string
     */
    protected $table = 'blocked_amount_history';

    /**
     * @var string
     */
    protected $primaryKey = 'blocked_amount_history_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'investor_id',
        'wallet_id',
        'task_id',
        'amount',
        'status',
    ];

    public static function getStatuses(): array
    {
        return [
            self::STATUS_BLOCKED,
            self::STATUS_PAID,
            self::STATUS_RETURNED,
        ];
    }
}
