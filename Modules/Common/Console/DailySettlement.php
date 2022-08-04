<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Originator;
use Modules\Common\Exports\SettlementExport;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\LogService;
use Modules\Common\Services\OriginatorService;
use Modules\Common\Services\SettlementService;
use Modules\Core\Services\StorageService;

class DailySettlement extends CommonCommand
{
    protected $name = 'script:daily-settlement';
    protected $signature = 'script:daily-settlement {originatorId?} {date?} {rewrite?}';
    protected $description = 'Prepare and send settlement on daily basis';
    protected $logChannel = 'settlement';

    protected LogService $logService;
    protected SettlementService $settlementService;
    protected OriginatorService $originatorService;

    public function __construct(
        LogService $logService,
        SettlementService $settlementService,
        OriginatorService $originatorService
    ) {
        $this->logService = $logService;
        $this->settlementService = $settlementService;
        $this->originatorService = $originatorService;

        parent::__construct();
    }

    public function handle()
    {
        $this->log("----- START");
        $start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());



        // parse params
        $rewrite = ('1' == $this->argument('rewrite'));
        $date = $this->parseDate($this->argument('date'));

        $reportDate = Carbon::yesterday();
        if (!empty($date)) {
            $reportDate = Carbon::parse($date);
        }

        $reportEndDate = $reportDate->format('Y-m-d');
        $originatorIdArg = $this->argument('originatorId');
        $originatorId = is_numeric($originatorIdArg) && $originatorIdArg > 0 ? $originatorIdArg : Originator::ID_ORIG_STIKCREDIT;
        $originator = $this->originatorService->getById(intval($originatorId));
        if (empty($originator)) {
            $this->log('Originator ID: ' . $originatorId . ' does not exists.');
            $log->finish($start, 0, 0, 'Originator ID: ' . $originatorId . ' does not exists.');
            return false;
        }


        $originatorName = $originator->name;
        if (preg_match("/stikcredit/i", $originatorName)) {
            $originatorName = 'Stik Credit';
        }


        // check if we already have such settlement
        $fileName = $originatorName . ' Daily Settlement Report ' . $reportDate->format('Ymd');
        $pathToFolder = StorageService::PATH_TO_SETTLEMENT;
        $storageService = \App::make(StorageService::class);

        if ($storageService::hasFile($pathToFolder . $fileName . '.xlsx')) {
            // settlement already sent, nothing todo
            if (!$rewrite) {
                $this->log('Nothing todo. Settlement already exists: ' . $fileName);
                $log->finish($start, 0, 0, 'Nothing todo. Settlement already exists: ' . $fileName);
                return false;
            }

            // rename existing, make place for new generating
            $storageService::renameFile(
                $pathToFolder,
                $fileName . '.xlsx',
                $fileName . '_' . time() . '.xlsx'
            );
        }


        // define main values
        $investedAmount = $this->settlementService->getInvestedAmountForDate($reportEndDate, $originator);
        $investmentsCount = $this->settlementService->getInvestmentCount($reportEndDate, $originator);
        $averageInvestment = $this->settlementService->getAverageInvestmentForDate($reportEndDate, $originator);

        $rebuyAmounts = $this->settlementService->getRebuyAmountsForDate($reportEndDate, $originator);
        $rebuyPrincipal = $this->format($rebuyAmounts['principal']);
        $rebuyAccrInterest = $this->format($rebuyAmounts['accrued_interest']);
        $rebuyInterest = $this->format($rebuyAmounts['interest']);
        $rebuyLateInterest = $this->format($rebuyAmounts['late_interest']);
        $rebuyTotalInterest = ($rebuyAccrInterest + $rebuyInterest);

        $repaidAmounts = $this->settlementService->getRepaidAmountsForDate($reportEndDate, $originator);
        $repaidPrincipal = $this->format($repaidAmounts['principal']);
        $repaidAccrInterest = $this->format($repaidAmounts['accrued_interest']);
        $repaidInterest = $this->format($repaidAmounts['interest']);
        $repaidLateInterest = $this->format($repaidAmounts['late_interest']);
        $repaidTotalInterest = ($repaidAccrInterest + $repaidInterest);

        $netSettlement = ($investedAmount - ($rebuyPrincipal + $rebuyTotalInterest + $repaidPrincipal + $repaidTotalInterest + $rebuyLateInterest + $repaidLateInterest));
        $netInvestmentsSum = $investedAmount - ($rebuyPrincipal + $repaidPrincipal);


        $univestedFunds = $this->settlementService->getUninvestedFundsForDate();


        $openBalance = $this->settlementService->getOutstandingBalanceFromTransaction(
            $originator,
            $reportDate->format('Y-m-d') . " 00:00:00"
        );
        $closeBalance = $this->settlementService->getOutstandingBalanceFromTransaction(
            $originator,
            $reportDate->format('Y-m-d') . " 23:59:59"
        );

        if ($rebuyPrincipal > 0) {
            $rebuyPrincipal *= -1;
        }
        if ($repaidPrincipal > 0) {
            $repaidPrincipal *= -1;
        }
        if ($rebuyTotalInterest > 0) {
            $rebuyTotalInterest *= -1;
        }
        if ($repaidTotalInterest > 0) {
            $repaidTotalInterest *= -1;
        }
        if ($rebuyLateInterest > 0) {
            $rebuyLateInterest *= -1;
        }
        if ($repaidLateInterest > 0) {
            $repaidLateInterest *= -1;
        }

        // create excel file
        $data = [
            'date' => $reportDate->format('d/m/Y'),

            'originatorName' => $originator->name, // TODO
            'currency' => strtoupper(Currency::LABEL_EURO), // TODO

            'originator_id' => $originator->originator_id, // TODO
            'currency_id' => strtoupper(Currency::ID_EUR), // TODO

            'total_invested_amount' => $investedAmount,
            'net_invested_amount' => $netInvestmentsSum,
            'avg_investment' => $averageInvestment,
            'investments_count' => $investmentsCount,

            'rebuy_principal' => $rebuyPrincipal,
            'rebuy_interest' => $rebuyTotalInterest,
            'rebuy_late_interest' =>$rebuyLateInterest,

            'repaid_principal' => $repaidPrincipal,
            'repaid_interest' => $repaidTotalInterest,
            'repaid_late_interest' => $repaidLateInterest,

            'net_settlement' => $netSettlement,
            'open_balance' => $openBalance,
            'close_balance' => $closeBalance,

            'univested_funds' => $univestedFunds,

            'from' => $reportDate->format('Y-m-d') . " 00:00:00",
            'to' => $reportDate->format('Y-m-d') . " 23:59:59",
        ];

        $this->settlementService->saveSettlement($reportEndDate, $data);

        dump($data);

        $exportClass = new SettlementExport($data);
        $filePath = $storageService->generate(
            $fileName,
            ['collectionClass' => $exportClass],
            'xlsx',
            $pathToFolder
        );



        // if file generated we will sent it to specific receivers
        if ($storageService::hasFile($filePath)) {
            $env = strtoupper(env('APP_ENV'));

            $settlementConfig = config('mail.settlement');
            if (empty($settlementConfig['receivers'])) {
                $this->log('No receivers for sending. Settlement file: ' . $filePath);
                $log->finish($start, 0, 0, 'No receivers for sending. Settlement file: ' . $filePath);
                return false;
            }

            $attachmentPath = storage_path($filePath);
            Mail::raw('Please find attached xslx file with daily settlement. ' . $env . ' environment.',
                function ($message) use (
                    $settlementConfig,
                    $reportDate,
                    $attachmentPath,
                    $env,
                    $fileName
                ) {
                    $message->from($settlementConfig['sender']['from'], $settlementConfig['sender']['name']);
                    $message->to($settlementConfig['receivers']);
                    $message->subject('Daily Settlement(' . $env . '): ' . $reportDate->format('d/m/Y'));
                    $message->attach($attachmentPath, ['as' => $fileName . '.xlsx', 'mime' => 'xlsx']);
                }
            );
        }



        $log->finish($start, 1, 1, 'Sent settlement');
        $this->log('File generated: ' . $filePath . ', sent to: ' . implode(', ', $settlementConfig['receivers']));
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }

    private function format($amount = null)
    {
        if (null === $amount) {
            return 0;
        }

        if ('' === $amount) {
            return 0;
        }

        return $amount;
    }
}
