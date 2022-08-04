<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class CronLog extends BaseModel
{
    protected $table = 'cron_log';
    protected $primaryKey = 'cron_log_id';

    protected $fillable = [
        'command',
        'file',
        'message',
        'total',
        'imported',
        'attempt',
        'total_exec_time',
        'last_exec_time',
    ];

    protected $with = [
        'creator',
        'updater',
    ];

    public function finish(
        string $startAt,
        int $total = null,
        int $imported = null,
        string $msg = null
    ): ?CronLog
    {
        if (empty($this->attempt)) {
            $this->attempt = 1;
        } else {
            $this->attempt += 1;
        }

        if (null !== $total) {
            $this->total = $total;
        }

        if (null !== $imported) {
            $this->imported = empty($this->imported)
                ? $imported
                : $this->imported + $imported;
        }

        if (!empty($msg)) {
            $this->message = (string) $msg;
        }

        $now = microtime(true);
        $this->last_exec_time = round(($now - $startAt), 2);
        $this->total_exec_time = round(($now - $this->created_at->getTimeStamp()), 2);

        $this->save();

        return $this;
    }
}
