<?php


namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Common\Exports\StrategiesBalanceExport;
use Modules\Common\Services\InvestStrategyService;
use Modules\Common\Services\LogService;
use Modules\Core\Services\StorageService;

class StrategiesBalance extends CommonCommand
{
    protected $name = 'script:strategies-balance';
    protected $signature = 'script:strategies-balance {rewrite?}';
    protected $description = 'Prepare, check and send strategies balance';
    protected $logChannel = 'strategies_balance';

    protected LogService $logService;
    protected InvestStrategyService $strategyService;

    private Carbon $date;
    private StorageService $storageService;

    private string $fileName;
    private string $pathToFolder;

    private string $balancePath;
    private array $balanceData;

    private float $start;

    public function __construct(
        LogService $logService,
        InvestStrategyService $strategyService
    )
    {
        $this->logService = $logService;
        $this->strategyService = $strategyService;

        $this->date = Carbon::now();

        $this->fileName = 'strategies_balance_' . $this->date->format('Y-m-d');
        $this->pathToFolder = StorageService::PATH_TO_STRATEGIES_BALANCE;

        $this->storageService = \App::make(StorageService::class);

        $this->originatorName = 'Stik Credit';

        parent::__construct();
    }

    public function handle(): bool
    {
        $this->log("----- START");
        $this->start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());

        $rewrite = ('1' == $this->argument('rewrite'));

        if ($this->isFileAlreadyExists()) {
            // already sent, nothing todo here
            if (! $rewrite) {
                $this->log('Nothing todo. Strategies Balance file already exists: ' . $this->fileName);
                $log->finish($this->start, 0, 0, 'Nothing todo. Strategies Balance file already exists: ' . $this->fileName);
                return false;
            }

            // rename existing, make place for new generating
            $this->renameAlreadyExistingFile();
        }

        $this->prepareStrategyBalanceData(
            $this->strategyService->getStrategiesBalance()
        );

        $this->generateBalanceFile();

        if ($this->storageService::hasFile($this->balancePath) && $this->isReceiversSet($log)) {
            $this->sendFile();
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isFileAlreadyExists(): bool
    {
        return $this->storageService::hasFile($this->pathToFolder . $this->fileName . '.xlsx');
    }

    private function renameAlreadyExistingFile(): void
    {
        $this->storageService::renameFile(
            $this->pathToFolder,
            $this->fileName . '.xlsx',
            $this->fileName . '_' . time() . '.xlsx'
        );
    }

    private function prepareStrategyBalanceData($balanceData): void
    {
        $balance = [];

        foreach ($balanceData as $item) {
            $totallyFine = 'FALSE';

            if( ($item->total_received + $item->total_received_lost_installments) == $item->total_received_inv_installments) {
                $totallyFine = 'TRUE';
            }

            $balance[] = [
                'investor' => $item->investor,
                'strategy_id' => $item->strategy_id,
                'strategy_name' => $item->strategy_name,
                'reinvest' => $item->reinvest,
                'max_portfolio_size' => $item->max_portfolio_size,
                'portfolio_size' => $item->portfolio_size,
                'total_invested' => $item->total_invested,
                'total_received' => $item->total_received,
                'total_invested_investments' => $item->total_invested_investments,
                'total_received_inv_installments' => $item->total_received_inv_installments,
                'total_outstanding_inv_installments' => $item->total_outstanding_inv_installments,
                'lost_installments_payments' => $item->total_received_lost_installments,
                'totally_fine' => $totallyFine,
            ];
        }

        $this->balanceData = $balance;
    }

    private function generateBalanceFile(): void
    {
        $exportClass = new StrategiesBalanceExport($this->balanceData);

        $this->balancePath = $this->storageService->generate(
            $this->fileName,
            ['collectionClass' => $exportClass],
            'xlsx',
            $this->pathToFolder
        );
    }

    /**
     * @param $log
     * @return bool
     */
    private function isReceiversSet($log): bool
    {
        $settlementConfig = config('mail.settlement');
        if (empty($settlementConfig['receivers'])) {
            $this->log('No receivers for sending. Strategies balance file: ' . $this->balancePath);
            $log->finish($this->start, 0, 0, 'No receivers for sending. Strategies balance file: ' . $this->balancePath);

            return false;
        }

        return true;
    }


    private function sendFile(): void
    {
        $this->sendBalanceFile();
        $settlementConfig = config('mail.settlement');

        $this->log('File generated: ' . $this->balancePath . ', sent to: ' . implode(', ', $settlementConfig['receivers']));
    }

    private function sendBalanceFile(): void
    {
        $env = strtoupper(env('APP_ENV'));

        $subject = 'Daily Strategies Balance(' . $env . ') - '.$this->date->format('d/m/Y');

        if ($this->storageService::hasFile($this->balancePath)) {
            $attachmentPath = storage_path($this->balancePath);
            $settlementConfig = config('mail.settlement');

            Mail::raw($subject,
                function ($message) use (
                    $settlementConfig,
                    $attachmentPath,
                    $subject
                ) {
                    $message->from($settlementConfig['sender']['from'], $settlementConfig['sender']['name']);
                    $message->to($settlementConfig['receivers']);
                    $message->subject($subject);
                    $message->attach($attachmentPath, ['as' => 'strategies_balance.xlsx', 'mime' => 'xlsx']);
                }
            );
        }
    }
}
