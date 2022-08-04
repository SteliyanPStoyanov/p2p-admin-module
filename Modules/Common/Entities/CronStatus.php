<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class CronStatus extends BaseModel
{
    public const TYPE_RUNNING = 'running';
    public const TYPE_FREE = 'free';

    protected $table = 'cron_manual_run_status';

    protected $primaryKey = 'cron_manual_run_status_id';

    protected $guarded = [
        'cron_manual_run_status_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public static function getTypes(): array
    {
        return [
            self::TYPE_FREE,
            self::TYPE_RUNNING,
        ];
    }
}
