<?php

namespace Modules\Common\Console;

use Modules\Common\Repositories\TransactionRepository;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\TransactionService;
use Modules\Common\Services\WalletService;

class CalculateInvestorBalance extends CommonCommand
{
    protected $name = 'script:calculate-investor-balance';
    protected $signature = 'script:calculate-investor-balance {investorId}';
    protected $description = 'Calculate wallet balance by investor, based on transactions';
    protected $logChannel = 'recalculate_wallet_balance';

    protected WalletService $walletService;
    protected TransactionService $transactionService;
    protected InvestorService $investorService;

    public function __construct(
        WalletService $walletService,
        TransactionService $transactionService,
        InvestorService $investorService
    ) {
        $this->walletService = $walletService;
        $this->transactionService = $transactionService;
        $this->investorService = $investorService;

        parent::__construct();
    }

    public function handle()
    {
        $start = microtime(true);
        $this->log("----- START -----");


        // CLI param
        $investorId = (int) $this->argument('investorId');
        if (empty($investorId)) {
            die('Error: no investor_id provided');
        }

        try {
            $walletBalance = $this->walletService->getByInvestorId($investorId);
            $transactionsBalance = $this->transactionService->calculatedWalletByTransactions(
                $investorId,
                $walletBalance->wallet_id
            );
            $transactionsBalance->income = $transactionsBalance->income + $transactionsBalance->bonus; // fix
            $installmentsInvestedAmount = $this->investorService->getInstallmentsOutstandingAmount($investorId);

            if (isset($walletBalance->wallet_id)) {
                $problems = [];

                $this->info('***' . 'Investor ID #' . $investorId . '***');
                $this->info(' ');

                $this->info('***' . '*******************************' . '***');
                $this->info('***' . 'Wallet balance' . '***');
                $this->info('---' . 'Wallet Id:' . ' ' . $walletBalance->wallet_id . '---');
                $this->info('---' . 'Total amount:' . ' ' . $walletBalance->total_amount . '---');
                $this->info('---' . 'Deposit:' . ' ' . $walletBalance->deposit . '---');
                $this->info('---' . 'Withdraw:' . ' ' . $walletBalance->withdraw . '---');
                $this->info('---' . 'Invested:' . ' ' . $walletBalance->invested . '---');
                $this->info('---' . 'Uninvested:' . ' ' . $walletBalance->uninvested . '---');
                $this->info('---' . 'Income:' . ' ' . $walletBalance->income . '---');
                $this->info('---' . 'Interest:' . ' ' . $walletBalance->interest . '---');
                $this->info('---' . 'Late interest:' . ' ' . $walletBalance->late_interest . '---');
                $this->info('---' . 'Bonus:' . ' ' . $walletBalance->bonus . '---');

                $this->info('***' . '*******************************' . '***');
                $this->info('***' . 'Balance according to transactions' . '***');
                $this->info('---' . 'Total Deposit:' . ' ' . $transactionsBalance->deposit_amount . '---');
                $this->info('---' . 'Total Withdraw:' . ' ' . $transactionsBalance->withdraw_amount . '---');
                $this->info('---' . 'Total Invested:' . ' ' . $transactionsBalance->investment_amount . '---');
                $this->info('---' . 'Total Repaid principal:' . ' ' . $transactionsBalance->repayment_principal . '---');
                $this->info('---' . 'Total Income:' . ' ' . $transactionsBalance->income . '---');
                $this->info('---' . 'Total Interest:' . ' ' . $transactionsBalance->interest . '---');
                $this->info('---' . 'Total Late interest:' . ' ' . $transactionsBalance->late_interest . '---');
                $this->info('---' . 'Total Bonus:' . ' ' . $transactionsBalance->bonus . '---');

                $this->info('***' . '*******************************' . '***');
                $this->info('---' . 'Outstanding Principal(installments):' . ' ' . $installmentsInvestedAmount);


                if ($walletBalance->deposit != $transactionsBalance->deposit_amount) {
                    $problems['deposit'] = 'Deposit, w:' . $walletBalance->deposit . ' != t:' . $transactionsBalance->deposit_amount;
                }
                if ($walletBalance->deposit != $transactionsBalance->deposit_amount) {
                    $problems['withdraw'] = 'Withdraw, w:' . $walletBalance->withdraw . ' != t:' . $transactionsBalance->withdraw_amount;
                }
                $investedTransac = $transactionsBalance->investment_amount - $transactionsBalance->repayment_principal;
                if ($walletBalance->invested != $investedTransac) {
                    $problems['invested'] = 'Withdraw, w:' . $walletBalance->invested . ' != t:' . $investedTransac;
                }
                $uninvestedTransac = $transactionsBalance->deposit_amount - $transactionsBalance->withdraw_amount - $transactionsBalance->investment_amount + $transactionsBalance->repayment_principal + $transactionsBalance->income;
                if ($walletBalance->uninvested != $uninvestedTransac) {
                    $problems['uninvested'] = 'Withdraw, w:' . $walletBalance->uninvested . ' != t:' . $uninvestedTransac;
                }
                if ($walletBalance->income != $transactionsBalance->income) {
                    $problems['income'] = 'Deposit, w:' . $walletBalance->income . ' != t:' . $transactionsBalance->income;
                }
                if ($walletBalance->interest != $transactionsBalance->interest) {
                    $problems['interest'] = 'Deposit, w:' . $walletBalance->interest . ' != t:' . $transactionsBalance->interest;
                }
                if ($walletBalance->late_interest != $transactionsBalance->late_interest) {
                    $problems['late_interest'] = 'Deposit, w:' . $walletBalance->late_interest . ' != t:' . $transactionsBalance->late_interest;
                }
                if ($walletBalance->bonus != $transactionsBalance->bonus) {
                    $problems['bonus'] = 'Deposit, w:' . $walletBalance->bonus . ' != t:' . $transactionsBalance->bonus;
                }
                if (null !== $installmentsInvestedAmount && $walletBalance->invested != $installmentsInvestedAmount) {
                    $problems['invested_installments'] = 'Deposit, w:' . $walletBalance->invested . ' != t:' . $installmentsInvestedAmount;
                }

                if (!empty($problems)) {
                    $this->info(' ');
                    $this->info('***' . '*******************************' . '***');
                    $this->info('---' . 'Problems:');

                    foreach ($problems as $key => $value) {
                        $this->info('- ' . $key . ': ' . $value);
                    }
                }
            }

        } catch (\Throwable $e) {
            $this->log(
                'Recalculate investor balance failed. Error' . $e->getMessage()
                . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
        }

        $this->log('Finished');
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
    }

}
