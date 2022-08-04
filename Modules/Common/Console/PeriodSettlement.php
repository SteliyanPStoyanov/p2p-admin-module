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

class PeriodSettlement extends CommonCommand
{
    protected $name = 'script:period-settlement';
    protected $signature = 'script:period-settlement {period?} {fromDate?} {toDate?}';
    protected $description = 'Prepare and send settlement on period basis : weekly , monthly , customName fromDate toDate';
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

        $toDate = $this->parseDate($this->argument('toDate'));
        $fromDate = $this->parseDate($this->argument('fromDate'));
        $period = $this->argument('period');
        $originatorId = Originator::ID_ORIG_STIKCREDIT;

        if (
            (empty($toDate) && !empty($fromDate))
            || (empty($fromDate) && !empty($toDate))
        ) {
            $this->log('Error: Two params should be provided in case of custom exec.');
            $log->finish($start, 0, 0, 'Error: Two params should be provided in case of custom exec.');
            return false;
        }

        // restructure the dates
        if ($period == 'monthly') {
            $fromDate = new Carbon('first day of last month');
            $fromDate->startOfMonth();
            $toDate = new Carbon('last day of last month');
            $toDate->endOfMonth();
        }
        if ($period == 'weekly') {
            $fromDate = (new Carbon)->subWeek()->startOfDay();
            $toDate = (new Carbon)->subDay()->endOfDay();
        }
        // restructure the dates
        if (!empty($toDate) && !empty($fromDate)) {
            $fromDate = Carbon::parse($fromDate);
            $fromDate->startOfDay();
            $toDate = Carbon::parse($toDate);
            $toDate->endOfDay();
        }

        // get originator
        $originator = $this->originatorService->getById($originatorId);
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
        $reportDate = (Carbon::now())->format('Y-m-d');
        $fileName = $this->getFileName($originatorName, $period, $fromDate, $toDate);
        $pathToFolder = StorageService::PATH_TO_SETTLEMENT;
        $storageService = \App::make(StorageService::class);
        if ($storageService::hasFile($pathToFolder . $fileName . '.xlsx')) {
            // rename existing, make place for new generating
            $storageService::renameFile(
                $pathToFolder,
                $fileName . '.xlsx',
                $fileName . '_' . time() . '.xlsx'
            );
        }


        // get all variables/values for report
        $data = $this->getData($originator, $fromDate, $toDate);

        // create excel file
        $exportClass = new SettlementExport($data, $period);
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

            $emailReceivers = $settlementConfig['receivers'];
            if ($period == 'monthly') {
                $emailReceivers = $settlementConfig['monthly_receivers'];
            }

            $attachmentPath = storage_path($filePath);
            $subject = ucfirst($period) . ' Settlement(' . $env . '), period: '
                . $fromDate->format('d-m-Y') . '-' . $toDate->format('d-m-Y');

            Mail::raw(
                'Please find attached xslx file with ' . $period . ' settlement. ' . $env . ' environment.',
                function ($message) use (
                    $settlementConfig,
                    $attachmentPath,
                    $fileName,
                    $subject,
                    $emailReceivers
                ) {
                    $message->subject($subject);
                    $message->from($settlementConfig['sender']['from'], $settlementConfig['sender']['name']);
                    $message->to($emailReceivers);
                    $message->attach($attachmentPath, ['as' => $fileName . '.xlsx', 'mime' => 'xlsx']);
                }
            );
        }


        $log->finish($start, 1, 1, 'Sent ' . $period . ' settlement');
        $this->log('File generated: ' . $filePath . ', sent to: ' . implode(', ', $emailReceivers));
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }

    private function getData(
        Originator $originator,
        Carbon $fromDate,
        Carbon $toDate
    ): array {
        $reportDate = Carbon::today();
        $from = $fromDate->format('Y-m-d');
        $to = $toDate->format('Y-m-d');


        // define main values
        $investedAmount = $this->settlementService->getInvestedAmountForPeriod($from, $to, $originator);
        $investmentsCount = $this->settlementService->getInvestmentCountForPeriod($from, $to, $originator);
        $averageInvestment = $this->settlementService->getAverageInvestmentForPeriod($from, $to, $originator);

        $rebuyAmounts = $this->settlementService->getRebuyAmountsForPeriod($from, $to, $originator);
        $rebuyPrincipal = $this->format($rebuyAmounts['principal']);
        $rebuyAccrInterest = $this->format($rebuyAmounts['accrued_interest']);
        $rebuyInterest = $this->format($rebuyAmounts['interest']);
        $rebuyLateInterest = $this->format($rebuyAmounts['late_interest']);
        $rebuyTotalInterest = ($rebuyAccrInterest + $rebuyInterest);

        $repaidAmounts = $this->settlementService->getRepaidAmountsForPeriod($from, $to, $originator);
        $repaidPrincipal = $this->format($repaidAmounts['principal']);
        $repaidAccrInterest = $this->format($repaidAmounts['accrued_interest']);
        $repaidInterest = $this->format($repaidAmounts['interest']);
        $repaidLateInterest = $this->format($repaidAmounts['late_interest']);
        $repaidTotalInterest = ($repaidAccrInterest + $repaidInterest);

        $netSettlement = ($investedAmount - ($rebuyPrincipal + $rebuyTotalInterest + $repaidPrincipal + $repaidTotalInterest + $rebuyLateInterest + $repaidLateInterest));
        $netInvestmentsSum = $investedAmount - ($rebuyPrincipal + $repaidPrincipal);

        $univestedFunds = $this->settlementService->getUninvestedFundsForDate();

        $openBalance = $this->settlementService->getOutstandingBalanceFromTransaction($originator, $from . " 00:00:00");
        $closeBalance = $this->settlementService->getOutstandingBalanceFromTransaction($originator, $to . " 23:59:59");

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

        return [
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
            'rebuy_late_interest' => $rebuyLateInterest,

            'repaid_principal' => $repaidPrincipal,
            'repaid_interest' => $repaidTotalInterest,
            'repaid_late_interest' => $repaidLateInterest,

            'net_settlement' => $netSettlement,
            'open_balance' => $openBalance,
            'close_balance' => $closeBalance,

            'univested_funds' => $univestedFunds,

            'from' => $from . " 00:00:00",
            'to' => $to . " 23:59:59",
        ];
    }

    private function getFileName(
        string $originatorName,
        string $period,
        Carbon $fromDate,
        Carbon $toDate
    ): string {
        return $originatorName . ' ' . ucfirst($period) . ' Settlement Report for period:'
            . $fromDate->format('Ymd') . '-' . $toDate->format('Ymd');
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
