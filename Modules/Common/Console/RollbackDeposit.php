<?php

namespace Modules\Common\Console;

use DB;
use Exception;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Entities\WalletRollbackHistory;
use Modules\Common\Services\LogService;
use Modules\Common\Services\TransactionService;
use Modules\Common\Services\WalletService;
use Throwable;

class RollbackDeposit extends CommonCommand
{
    protected $name = 'script:rollback-deposit';
    protected $signature = 'script:rollback-deposit {investorId} {transactionId}';
    protected $description = 'Prepare, check and rollback deposit';
    protected $logChannel = 'rollback_deposit';

    protected LogService $logService;
    protected TransactionService $transactionService;
    protected WalletService $walletService;

    private $start;
    private string $originatorName;
    private Wallet $wallet;
    private $transaction = null;

    public function __construct(
        LogService $logService,
        TransactionService $transactionService,
        WalletService $walletService
    ) {
        $this->logService = $logService;
        $this->transactionService = $transactionService;
        $this->walletService = $walletService;
        $this->originatorName = 'Stik Credit';

        parent::__construct();
    }

    public function handle()
    {
        $this->log("---- START ----");
        $this->start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());

        // parse params
        $investorId = (int)$this->argument('investorId');
        $transactionId = (int)$this->argument('transactionId');
        if (empty($investorId) || empty($transactionId)) {
            die('Wrong input params: investorId/transactionId');
        }

        DB::beginTransaction();

        try {
            // check if transaction is valid
            if (!$this->isTransactionExists($investorId, $transactionId)) {
                $msg = sprintf(
                    'Investor #%s transaction #%s not exists',
                    $investorId,
                    $transactionId
                );
                throw new Exception($msg);
            }

            $transaction = $this->getLockedTransaction($transactionId);
            // check if last transaction
            if (!$this->isLastTransaction($transaction->investor_id, $transaction->created_at)) {
                $msg = sprintf(
                    'Investor #%s made transactions after the deposit',
                    $investorId
                );
                throw new Exception($msg);
            }

            $wallet = $this->getLockedWallet($transaction->wallet_id);
            if (!$this->walletHasUntouchedDepositedAmount($wallet)) {
                $msg = sprintf(
                    'Investor #%s wallet balance is wrong',
                    $investorId
                );
                throw new Exception($msg);
            }


            $walletBefore = $wallet->toArray();
            $walletBefore['type'] = WalletRollbackHistory::TYPE_BEFORE_ROLLBACK;

            $wallet->deposit -= $transaction->amount;
            $wallet->total_amount -= $transaction->amount;
            $wallet->uninvested -= $transaction->amount;

            if ($wallet->save()) {
                $this->log('Investor #' . $investorId . ' wallet is change. Rollback amount ' . $transaction->amount);

                try {
                    $transaction->delete();
                } catch (Throwable $e) {
                    $msg = $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
                    $this->log($msg);
                }

                $this->log(
                    'Investor #' . $investorId . ' transactions is deactivate. Transaction ID #' . $transaction->transaction_id
                );

                try {
                    WalletRollbackHistory::create($walletBefore);
                } catch (Throwable $e) {
                    $msg = $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
                    $this->log($msg);
                }

                $walletAfter = $wallet->getOriginal();
                $walletAfter['type'] = WalletRollbackHistory::TYPE_AFTER_ROLLBACK;

                try {
                    WalletRollbackHistory::create($walletAfter);
                } catch (Throwable $e) {
                    $msg = $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
                    $this->log($msg);
                }
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();

            $this->log(
                'Error! '
                . 'investor #' . $investorId . ', '
                . 'transaction # = ' . $transactionId . ', '
                . 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );

            return false;
        }

        $log->finish($this->start, 1, 1, 'Rollback deposit Investor #' . $investorId);
        $this->log('Exec.time: ' . round((microtime(true) - $this->start), 2) . ' second(s)');
        return true;
    }

    /**
     * @param int $investorId
     * @param int $transactionId
     * @return bool
     */
    private function isTransactionExists(int $investorId, int $transactionId): bool
    {
        $this->transaction = $this->transactionService->getByInvestorAndTransactionId($investorId, $transactionId);
        return !empty($this->transaction->transaction_id);
    }

    /**
     * @param int $investorId
     * @param string $created_at
     * @return bool
     */
    private function isLastTransaction(int $investorId, string $created_at): bool
    {
        return (false === $this->transactionService->investorHasTransactionsAfter(
                $investorId,
                $created_at
            ));
    }

    /**
     * @param Wallet $wallet
     * @return bool
     */
    private function walletHasUntouchedDepositedAmount(Wallet $wallet): bool
    {
        $amount = $this->transaction->amount;

        $walletNew = [];
        $walletNew['deposit'] = $wallet->deposit - $amount;
        $walletNew['total_amount'] = $wallet->total_amount - $amount;
        $walletNew['uninvested'] = $wallet->uninvested - $amount;

        if (
            $walletNew['deposit'] < 0
            || $walletNew['uninvested'] < 0
            || $walletNew['total_amount'] < 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param int $walletId
     * @return Wallet
     */
    public function getLockedWallet(int $walletId): Wallet
    {
        return Wallet::where('wallet_id', $walletId)->lockForUpdate()->first();
    }

    /**
     * @param int $transactionId
     * @return Transaction
     */
    public function getLockedTransaction(int $transactionId): Transaction
    {
        return Transaction::where('transaction_id', $transactionId)->lockForUpdate()->first();
    }
}
