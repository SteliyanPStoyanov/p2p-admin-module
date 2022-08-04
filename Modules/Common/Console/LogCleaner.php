<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Common\Entities\Currency;
use Modules\Common\Exports\SettlementExport;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\LogService;
use Modules\Common\Services\SettlementService;
use Modules\Core\Services\StorageService;

class LogCleaner extends CommonCommand
{
    public const PROTECTED_LOGS = [
        'stack',
        'single',
        'emergency',
        'log_cleaner',
    ];

    public const PATH_NAME = 'path';

    protected $name = 'logs:clear';
    protected $signature = 'logs:clear {log?}';
    protected $description = 'Clear log';
    protected $logChannel = 'log_cleaner';

    protected LogService $logService;

    public function __construct(
        LogService $logService
    ) {
        $this->logService = $logService;

        parent::__construct();
    }

    public function handle()
    {
        $this->log("----- START");
        $start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());

        $logArg = $this->argument('log');
        $channels = config('logging.channels');
        $logsToDelete = $this->getLogPaths($channels, $logArg);
        $deletedLogs = $this->deleteLogs($logsToDelete);

        $log->finish($start, count($logsToDelete), $deletedLogs, 'Logs cleaned.');
        $this->log('Deleted count: ' . $deletedLogs);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }

    /**
     * @param array $channels
     * @param null|string $log
     *
     * @return array
     */
    protected function getLogPaths(array $channels, string $log = null): array
    {
        $result = [];

        if ($log !== null) {
            if (
                !in_array($log, self::PROTECTED_LOGS)
                && array_key_exists($log, $channels)
                && array_key_exists(self::PATH_NAME, $channels[$log])
            ) {
                $result[] = $channels[$log][self::PATH_NAME];
            }

            return $result;
        }

        foreach ($channels as $key=>$channel) {
            if (!in_array($key, self::PROTECTED_LOGS) && array_key_exists(self::PATH_NAME, $channel)) {
                $result[] = $channel[self::PATH_NAME];
            }
        }

        return $result;
    }

    /**
     * @param array $logsToDelete
     *
     * @return int
     */
    protected function deleteLogs(array $logsToDelete)
    {
        $deleted = 0;
        foreach ($logsToDelete as $path) {

            if (file_exists($path)) {
                exec('cat /dev/null > ' . $path);
                $this->log('Deleted: ' . $path);

                $deleted++;
            }
        }

        return $deleted;
    }
}
