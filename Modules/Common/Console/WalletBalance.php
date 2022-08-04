<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Modules\Common\Entities\CronLog;
use Modules\Common\Exports\WalletsExport;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\LogService;
use Modules\Common\Services\WalletService;
use Modules\Core\Services\StorageService;

class WalletBalance extends CommonCommand
{
    protected $name = 'script:wallet-balance';
    protected $signature = 'script:wallet-balance {rewrite?}';
    protected $description = 'Prepare, check and send wallet balance';
    protected $logChannel = 'wallet_balance';

    protected LogService $logService;
    protected WalletService $walletService;

    private $fileName;
    private $pathToFolder;
    private $dateWallet;
    private $storageService;

    private $originatorName;

    private $wallets;
    private $wrongWallets;

    private $walletsPath;
    private $wrongWalletsPath;

    private $start;

    public function __construct(
        LogService $logService,
        WalletService $walletService
    )
    {
        $this->logService = $logService;
        $this->walletService = $walletService;

        $this->dateWallet = Carbon::now();

        $this->fileName = 'wallet_' . $this->dateWallet->format('Y-m-d');
        $this->pathToFolder = StorageService::PATH_TO_WALLET;

        $this->storageService = \App::make(StorageService::class);

        $this->originatorName = 'Stik Credit';

        parent::__construct();
    }

    public function handle()
    {
        $this->log("----- START");
        $this->start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());

        // parse params
        $rewrite = ('1' == $this->argument('rewrite'));
// $rewrite = true;
        if ($this->isFileAlreadyExists()) {
            // settlement already sent, nothing to do here
            if (! $rewrite) {
                $this->log('Nothing todo. Wallets file already exists: ' . $this->fileName);
                $log->finish($this->start, 0, 0, 'Nothing todo. Wallets file already exists: ' . $this->fileName);
                return false;
            }

            // rename existing, make place for new generating
            $this->renameAlreadyExistingFile();

            if ($this->isWrongWalletsFileAlreadyExists()) {
                $this->renameAlreadyExistingWrongWalletsFile();
            }
        }

        $this->prepareWalletsData(
            $this->walletService->getWalletsBalance()
        );

        $this->generateWalletsFile();

        if (count($this->wrongWallets)) {
            $this->generateWrongWalletsFile();
        }

        // if file generated we will sent it to specific receivers
        if ($this->storageService::hasFile($this->walletsPath) && $this->isReceiversSet($log)) {
            $this->sendFile();
        }

        if (count($this->wrongWallets) && $this->storageService::hasFile($this->wrongWalletsPath) && $this->isReceiversSet($log)) {
            $this->sendFile(true);
        }

        $log->finish($this->start, 1, 1, 'Sent settlement');
        $this->log('Exec.time: ' . round((microtime(true) - $this->start), 2) . ' second(s)');

        return true;
    }

    /**
     * @param bool $wrong if we should send a file containing wrong data
     */
    private function sendFile(bool $wrong = false): void
    {
        $this->sendWalletsFile($wrong);
        $settlementConfig = config('mail.log_monitor');

        $path = $this->walletsPath;
        if ($wrong) {
            $path = $this->wrongWalletsPath;
            $settlementConfig = config('mail.settlement');
        }

        $this->log('File generated: ' . $path . ', sent to: ' . implode(', ', $settlementConfig['receivers']));
    }

    private function isFileAlreadyExists()
    {
        return $this->storageService::hasFile($this->pathToFolder . $this->fileName . '.xlsx');
    }

    private function isWrongWalletsFileAlreadyExists()
    {
        return $this->storageService::hasFile($this->pathToFolder . 'wrong_' . $this->fileName . '.xlsx');
    }

    private function renameAlreadyExistingFile()
    {
        $this->storageService::renameFile(
            $this->pathToFolder,
            $this->fileName . '.xlsx',
            $this->fileName . '_' . time() . '.xlsx'
        );
    }

    private function renameAlreadyExistingWrongWalletsFile(): void
    {
        $this->storageService::renameFile(
            $this->pathToFolder,
            'wrong_'.$this->fileName . '.xlsx',
            'wrong_'.$this->fileName . '_' . time() . '.xlsx'
        );
    }

    private function prepareWalletsData($walletData)
    {
        $wallets = [];
        $wrongWallets = [];

        foreach($walletData as $wallet) {

            $totalInvested = $wallet->invested + $wallet->sm_buy - $wallet->sm_sell;
            $totalInvested = Calculator::round($totalInvested);

            $actualInvestedAmount = $totalInvested - $wallet->repaid_principal;
            $actualInvestedAmount = Calculator::round($actualInvestedAmount);

            $actualTotalAmount = $actualInvestedAmount + $wallet->wallet_uninvested + $wallet->sm_buy_premium - $wallet->sm_sell_premium;
            $actualTotalAmount = Calculator::round($actualTotalAmount);

            $balance = $wallet->balance + ($wallet->sm_sell + $wallet->sm_sell_premium) - ($wallet->sm_buy + $wallet->sm_buy_premium);
            $balance = Calculator::round($balance);

            $walletInvested = $wallet->wallet_invested - $wallet->sm_buy_premium;
            $walletInvested = Calculator::round($walletInvested);

            $el = [
                'investor_id' => $wallet->investor_id,
                'first_name' => $wallet->first_name,
                'last_name' => $wallet->last_name,
                'deposited' => $wallet->deposited,
                'invested' => $totalInvested,
                'repayments' => $wallet->repayments,
                'withdrawed' => $wallet->withdrawed,
                'repaid_principal' => $wallet->repaid_principal,
                'bonus' => $wallet->bonus,
                'balance' => $balance,
                'wallet_bonus' => $wallet->wallet_bonus,
                'wallet_deposited' => $wallet->wallet_deposited,
                'wallet_invested' => $walletInvested,
                'wallet_income' => $wallet->wallet_income,
                'wallet_uninvested' => $wallet->wallet_uninvested,
                'wallet_withdraw' => $wallet->wallet_withdraw,
                'wallet_total_amount' => $wallet->wallet_total_amount,
                'outstanding_principal' => $wallet->outstanding_principal,
                'actual_invested_amount' => $actualInvestedAmount,
                'actual_total_amount' => $actualTotalAmount,
                'secondary_market_buy' => $wallet->sm_buy,
                'secondary_market_sell' => $wallet->sm_sell,
                'secondary_market_buy_premium' => $wallet->sm_buy_premium,
                'secondary_market_sell_premium' => $wallet->sm_sell_premium,
                'reason' => [],
            ];

            if (bccomp($actualInvestedAmount, $el['wallet_invested'], 3)) {
                $el['reason'][] = 'invested';
            }
            if (bccomp($actualTotalAmount, $el['wallet_total_amount'], 3)) {
                $el['reason'][] = 'total_amount';
            }
            if (bccomp($el['deposited'], $el['wallet_deposited'], 3)) {
                $el['reason'][] = 'deposited';
            }
            if (bccomp($el['balance'], $el['wallet_uninvested'], 3)) {
                $el['reason'][] = 'uninvested';
            }
            if (bccomp($el['wallet_invested'], $el['outstanding_principal'], 3)) {
                $el['reason'][] = 'outstanding_principal/wallet_invested';
            }
            if (bccomp($el['outstanding_principal'], $actualInvestedAmount, 3)) {
                $el['reason'][] = 'outstanding_principal/actual_invested_amount';
            }

            if (!empty($el['reason'])) {
                $wrongWallets[] = $el;
            }

            $wallets[] = $el;
        }

        $this->wrongWallets = $wrongWallets;
        $this->wallets = $wallets;
    }

    private function generateWalletsFile(): void
    {
        $exportClass = new WalletsExport($this->wallets);

        $this->walletsPath = $this->storageService->generate(
            $this->fileName . '_' . date('Y-m-d'),
            ['collectionClass' => $exportClass],
            'xlsx',
            $this->pathToFolder
        );
    }

    private function generateWrongWalletsFile(): void
    {
        $exportClass = new WalletsExport($this->wrongWallets);

        $this->wrongWalletsPath = $this->storageService->generate(
            'wrong_' . $this->fileName . '_' . date('Y-m-d'),
            ['collectionClass' => $exportClass],
            'xlsx',
            $this->pathToFolder
        );
    }

    private function isReceiversSet($log): bool
    {
        $settlementConfig = config('mail.settlement');
        if (empty($settlementConfig['receivers'])) {
            $this->log('No receivers for sending. Settlement file: ' . $this->walletsPath);
            $log->finish($this->start, 0, 0, 'No receivers for sending. Settlement file: ' . $this->walletsPath);

            return false;
        }

        return true;
    }

    private function sendWalletsFile($wrongWallets = false)
    {
        $env = strtoupper(env('APP_ENV'));

        $filePath = $this->walletsPath;
        $subject = 'Daily Wallet Ballance(' . $env . ') - ' . $this->dateWallet->format('d/m/Y');
        $emailReceivers = config('mail.log_monitor.receivers');

        if ($wrongWallets) {
            $filePath = $this->wrongWalletsPath;
            $subject = 'Daily Wallet Balance Problem(' . $env . ') - ' . $this->dateWallet->format('d/m/Y');

            if (isProd()) {
                $emailReceivers = config('mail.settlement.receivers');
            }
        }

        if ($this->storageService::hasFile($filePath)) {
            $attachmentPath = storage_path($filePath);
            $settlementConfig = config('mail.settlement');

            Mail::raw($subject,
                function ($message) use (
                    $emailReceivers,
                    $settlementConfig,
                    $attachmentPath,
                    $subject
                ) {
                    $message->from($settlementConfig['sender']['from'], $settlementConfig['sender']['name']);
                    $message->to($emailReceivers);
                    $message->subject($subject);
                    $message->attach($attachmentPath, ['as' => 'wallets.xlsx', 'mime' => 'xlsx']);
                }
            );
        }
    }

}
