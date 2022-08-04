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

class LogMonitor extends LogCleaner
{
    protected $name = 'script:logs:monitor';
    protected $signature = 'script:logs:monitor {date?}'; // Example -> script:logs:monitor 2021-01-25
    protected $description = 'Check all logs for date for errors';
    protected $logChannel = 'log_monitor';

    public function handle()
    {
        $this->log("----- START");
        $start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());

        $date = $this->parseDate($this->argument('date'));
        $date = !empty($date) ? Carbon::parse($date) : Carbon::yesterday();

        $channels = config('logging.channels');

        $logs = $this->getLogPaths($channels);

        list($errors, $errCount) = $this->getErrorsFromLogs($logs, $date);

        if (empty($errors)) {
              $this->log('Log Monitor not found logs to send.');
            return false;
        }

        $html = $this->prepareHtml($errors);
        $env = strtoupper(env('APP_ENV'));

        $to = config('mail.log_monitor')['receivers'];
        $from = config('mail.log_monitor')['sender'];
        $title = 'Log Monitor(' . $env . '): ' . $date->format('Y-m-d');

        Mail::send([], [], function($message) use($to, $from, $title, $html) {
            $message->from($from['from'], $from['name']);
            $message->to($to);
            $message->subject($title);
            $message->setBody($html, 'text/html');
        });

        $log->finish($start, count($logs), $errCount, 'Found ' . $errCount);
        $this->log('Count: ' . $errCount);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }

    /**
     * Return array with format: [actual errors, error count]
     *
     * @param array $logs
     * @param Carbon $date
     *
     * @return array
     */
    protected function getErrorsFromLogs(array $logs, Carbon $date): array
    {
        $errors = [];
        $errorCount = 0;
        $pattern = '/(\[' . $date->format('Y-m-d') . ' [0-9]{2}:[0-9]{2}:[0-9]{2}\])(.*?)(error)/i';

        foreach ($logs as $log) {
            if (!file_exists($log)) {
                continue;
            }

            foreach ($this->errorSearcher($log, $pattern) as $value) {
                $errors[$log][] = $value;
                $errorCount++;
            }
        }

        return [$errors, $errorCount];
    }

    /**
     * @param $file
     * @param $regex
     *
     * @return \Generator
     */
    protected function errorSearcher($file, $regex): \Generator
    {
        $fh = fopen($file, 'r');
        while (!feof($fh)) {
            $line = fgets($fh, 4096);
            if (preg_match($regex, $line)) {
                yield $line;
            }
        }

        fclose($fh);
    }

    /**
     * @param array $errors
     *
     * @return string
     */
    protected function prepareHtml(array $errors): string
    {
        $html = '
            <html>
            <table>
                <tr>
                    <td>File</td>
                    <td>Error</td>
                </tr>
                ';

        foreach ($errors as $fileName => $allErrors) {
            foreach ($allErrors as $error) {
                $html .= '
                    <tr>
                        <td>' . $fileName . '</td>
                        <td>' . $error . '</td>
                    </tr>
                ';
            }
        }

        $html .= '
            </table>
            </html>
        ';

        return $html;
    }
}
