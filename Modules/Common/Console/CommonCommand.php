<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\CronLog;
use Modules\Common\Entities\CronStatus;

class CommonCommand extends Command
{
    public const NAMESPACE = 'Modules\Common\Console\\';

    protected $logChannel = 'command'; // should register in /config/logging.php

    public function __construct()
    {
        parent::__construct();
    }

    public function log(string $msg)
    {
        $this->info($msg);
        Log::channel($this->logChannel)->info($msg);
    }

    public function getToday()
    {
        return Carbon::today()->format('Y-m-d');
    }

    public function getYesterdayDate()
    {
        return Carbon::yesterday()->format('Y-m-d');
    }

    public function showMemoryUsage()
    {
        $usage = (memory_get_peak_usage(true) / 1024 / 1024);
        $this->info('Maximum memory usage: ' . $usage . ' MBs');
    }

    public function parseDate(string $input = null): ?string
    {
        if (preg_match('/^(20[0-9]{2}\-[0-9]{2}\-[0-9]{2})$/', $input, $m)) {
            return $m[0];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return string
     */
    public function getNameForDb(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * @return mixed
     */
    public function lastDbRecord()
    {
        return DB::selectOne(
            '
                    SELECT
                       cl.cron_log_id,
                       cl.total_exec_time,
                       cl.message,
                       cl.created_at
                    FROM cron_log AS cl
                    WHERE
                        cl.command = :command
                    ORDER BY
                        cl.created_at DESC
                    LIMIT 1
                '
            ,
            ['command' => $this->getNameForDb()]
        );
    }

    /**
     * @return bool
     */
    public function isInManualExecution(): bool
    {
        return (bool)DB::selectOne(
            '
                SELECT
                    COUNT(cmrs.cron_manual_run_status_id) AS count
                FROM
                    cron_manual_run_status AS cmrs
                WHERE
                    cmrs.command = :command
                    AND cmrs.status = :running
            ',
            ['command' => $this->getNameForDb(), 'running' => CronStatus::TYPE_RUNNING]
        )->count;
    }

    /**
     * @return CronStatus
     */
    public function createRunStatus()
    {
        $status = new CronStatus();
        $status->fill(
            [
                'command' => $this->getNameForDb(),
                'status' => CronStatus::TYPE_RUNNING,
            ]
        );
        $status->save();

        return $status;
    }

    public function closeRunStatus(CronStatus $cronStatus)
    {
        $cronStatus->status = CronStatus::TYPE_FREE;
        $cronStatus->save();

        return $cronStatus;
    }
}
