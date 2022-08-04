<?php

namespace Modules\Common\Services;

use Modules\Common\Entities\CronLog;
use Modules\Core\Services\BaseService;

class LogService extends BaseService
{
    public function newLog(string $name, bool $isManual = false): CronLog
    {
        return $this->createCronLog($name, null, null, $isManual);
    }

    /**
     * @param string $command
     * @param string|null $file
     * @param string|null $msg
     * @param bool $isManual
     *
     * @return CronLog
     */
    public function createCronLog(
        string $command,
        string $file = null,
        string $msg = null,
        bool $isManual = false
    ): CronLog
    {
        $obj = new CronLog();
        $obj->command = $command;
        $obj->file = $file;
        $obj->total = 0;
        $obj->imported = 0;
        $obj->attempt = 0;
        $obj->total_exec_time = 0;
        $obj->last_exec_time = 0;
        if (!empty($msg)) {
            $obj->message = $msg;
        }
        $obj->manual_run = $isManual;
        $obj->save();

        return $obj;
    }
}
