<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Common\Exports\LoanOutstandingAmountExport;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\LogService;
use Modules\Core\Services\StorageService;

class LoanOutstandingAmountChecker extends CommonCommand
{
    protected $name = 'script:loans:bad-invested-amount';
    protected $signature = 'script:loans:bad-invested-amount';
    protected $description = 'Search loans with wrong invested amount / available amount';
    protected $logChannel = 'loans_outstanding_amount';

    protected LogService $logService;
    protected LoanService $loanService;

    private string $fileName;
    private Carbon $dateLoan;
    private string $pathToFolder;
    private $storageService;
    private string $originatorName;
    private $loans;
    private string $loansPath;
    private $start;

    public function __construct(
        LogService $logService,
        LoanService $loanService
    )
    {
        $this->logService = $logService;
        $this->loanService = $loanService;

        $this->dateLoan = Carbon::now();
        $this->fileName = 'loans_bad_invested_amount_' . $this->dateLoan->format('Y-m-d-H-i-s');
        $this->pathToFolder = StorageService::PATH_TO_LOAN_OUTSTANDING;

        $this->storageService = \App::make(StorageService::class);

        $this->originatorName = 'Stik Credit';
        parent::__construct();
    }

    public function handle()
    {
        $this->log("----- START");
        $this->start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());


        $this->loans = $this->loanService->getLoansWithBadInvestedAmount();
        $this->generateLoansFile($this->loans);


        // if file generated we will sent it to specific receivers
        if (
            $this->storageService::hasFile($this->loansPath)
            && $this->isReceiversSet($log)
        ) {
            $this->sendFile();
        }

        $log->finish($this->start, 1, 1, 'Loans with wrong invested amount / available amount');
        $this->log('Exec.time: ' . round((microtime(true) - $this->start), 2) . ' second(s)');

        return true;
    }

    private function generateLoansFile(): void
    {
        $exportClass = new LoanOutstandingAmountExport($this->loans);

        $this->loansPath = $this->storageService->generate(
            $this->fileName,
            ['collectionClass' => $exportClass],
            'xlsx',
            $this->pathToFolder
        );
    }


    private function sendFile(): void
    {
        $this->sendLoansFile();
        $settlementConfig = config('mail.log_monitor');

        $path = $this->loansPath;

        $this->log('File generated: ' . $path . ', sent to: ' . implode(', ', $settlementConfig['receivers']));
    }

    private function sendLoansFile()
    {
        $env = strtoupper(env('APP_ENV'));

        $filePath = $this->loansPath;
        $subject = 'Loans with wrong available/invested amount(' . $env . ') - ' . $this->dateLoan->format('d/m/Y');

        if ($this->storageService::hasFile($filePath)) {
            $attachmentPath = storage_path($filePath);
            $settlementConfig = config('mail.log_monitor');

            Mail::raw($subject,
                function ($message) use (
                    $settlementConfig,
                    $attachmentPath,
                    $subject
                ) {
                    $message->from($settlementConfig['sender']['from'], $settlementConfig['sender']['name']);
                    $message->to($settlementConfig['receivers']);
                    $message->subject($subject);
                    $message->attach($attachmentPath, ['as' => 'loans_bad_amount.xlsx', 'mime' => 'xlsx']);
                }
            );
        }
    }

    private function isReceiversSet($log): bool
    {
        $settlementConfig = config('mail.log_monitor');
        if (empty($settlementConfig['receivers'])) {
            $this->log('No receivers for sending. Loans outstanding amount file: ' . $this->loansPath);
            $log->finish($this->start, 0, 0, 'No receivers for sending. Loans outstanding amount file: ' . $this->loansPath);

            return false;
        }

        return true;
    }
}
