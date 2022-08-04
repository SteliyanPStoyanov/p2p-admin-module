<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Mail;
use Modules\Common\Entities\InvestorBonus;
use Modules\Common\Exports\InvestorBonusHandledExport;
use Modules\Common\Services\InvestorBonusService;
use Modules\Common\Services\LogService;
use Modules\Core\Services\StorageService;
use Throwable;

class BonusTracking extends CommonCommand
{
    protected $name = 'script:bonus-tracking';
    protected $signature = 'script:bonus-tracking {rewrite?}';
    protected $description = 'Check and send investors with bonus';
    protected $logChannel = 'bonus_tracking';

    protected LogService $logService;
    protected InvestorBonusService $investorBonusService;

    private string $fileName;
    private string $pathToFolder;
    private Carbon $dateNow;
    private $storageService;

    private $investorBonus;
    private $investorBonusPath;

    private $start;

    public function __construct(
        LogService $logService,
        InvestorBonusService $investorBonusService
    ) {
        $this->logService = $logService;
        $this->investorBonusService = $investorBonusService;


        $this->dateNow = Carbon::now();

        $this->fileName = 'bonus_tracking_' . $this->dateNow->format('Y-m-d');
        $this->pathToFolder = StorageService::PATH_TO_BONUS_TRACKING;

        $this->storageService = \App::make(StorageService::class);

        parent::__construct();
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws Exception
     */
    public function handle(): bool
    {
        $this->log("----- START");
        $this->start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());

        // parse params
        $rewrite = ('1' == $this->argument('rewrite'));

        if ($this->isFileAlreadyExists()) {
            if (!$rewrite) {
                $this->log('Nothing todo. Investor bonus file already exists: ' . $this->fileName);
                $log->finish(
                    $this->start,
                    0,
                    0,
                    'Nothing todo. Investor bonus file already exists: ' . $this->fileName
                );
                return false;
            }

            // rename existing, make place for new generating
            $this->renameAlreadyExistingFile();
        }

        $this->investorBonus = $this->investorBonusService->getInvestorsUnhandledBonus();

        if ($this->investorBonus->count() == 0) {
            $this->log('Nothing todo. Investor bonus is given try tomorrow!');
            $log->finish($this->start, 0, 0, 'Nothing todo. Investor bonus is given try tomorrow!');
            return false;
        }

        try {
           $this->generateFile();
        } catch (Throwable $e) {
            $msg = $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
            $this->log($msg);
        }

        // if file generated we will sent it to specific receivers
        if ($this->storageService::hasFile($this->investorBonusPath) && $this->isReceiversSet($log)) {
            $this->sendInvestorBonusFile();
        }

        $log->finish($this->start, 1, 1, 'Sent investor bonus file');
        $this->log('Exec.time: ' . round((microtime(true) - $this->start), 2) . ' second(s)');

        return true;
    }


    /**
     * @return bool
     */
    private function isFileAlreadyExists(): bool
    {
        return $this->storageService::hasFile($this->pathToFolder . $this->fileName . '.xlsx');
    }


    private function renameAlreadyExistingFile()
    {
        $this->storageService::renameFile(
            $this->pathToFolder,
            $this->fileName . '.xlsx',
            $this->fileName . '_' . time() . '.xlsx'
        );
    }


    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private function generateFile(): void
    {
        $exportClass = new InvestorBonusHandledExport($this->investorBonus);

        $this->investorBonusPath = $this->storageService->generate(
            $this->fileName,
            ['collectionClass' => $exportClass],
            'xlsx',
            $this->pathToFolder
        );
    }

    private function isReceiversSet($log): bool
    {
        $settlementConfig = config('mail.settlement');
        if (empty($settlementConfig['receivers'])) {
            $this->log('No receivers for sending. investor bonus file: ' . $this->investorBonusPath);
            $log->finish(
                $this->start,
                0,
                0,
                'No receivers for sending. Investor bonus file: ' . $this->investorBonusPath
            );

            return false;
        }

        return true;
    }

    private function sendInvestorBonusFile()
    {
        $env = strtoupper(env('APP_ENV'));

        $filePath = $this->investorBonusPath;
        $subject = 'Investor Bonus(' . $env . ') - ' . $this->dateNow->format('d/m/Y');

        if ($this->storageService::hasFile($filePath)) {
            $attachmentPath = storage_path($filePath);
            $settlementConfig = config('mail.settlement');

            Mail::raw(
                $subject,
                function ($message) use (
                    $settlementConfig,
                    $attachmentPath,
                    $subject
                ) {
                    $message->from($settlementConfig['sender']['from'], $settlementConfig['sender']['name']);
                    $message->to($settlementConfig['receivers']);
                    $message->subject($subject);
                    $message->attach($attachmentPath, ['as' => 'investorBonus.xlsx', 'mime' => 'xlsx']);
                }
            );
        }

        $settlementConfig = config('mail.settlement');

        $path = $this->investorBonusPath;

        $this->log('File generated: ' . $path . ', sent to: ' . implode(', ', $settlementConfig['receivers']));
    }

}
