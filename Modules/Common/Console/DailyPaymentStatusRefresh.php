<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\LogService;
use Throwable;

class DailyPaymentStatusRefresh extends CommonCommand
{
    protected $name = 'script:daily-payment-status-refresh';
    protected $signature = 'script:daily-payment-status-refresh {date?} {loanId?}';
    protected $logChannel = 'daily_repayment';
    protected $description = 'Update payment status of the loan and investor quality ranges';

    protected LogService $logService;
    protected LoanService $loanService;

    /**
     * Create a new command instance.
     *
     * @param LogService $logService
     * @param LoanService $loanService
     */
    public function __construct(
        LogService $logService,
        LoanService $loanService
    ) {
        $this->logService = $logService;
        $this->loanService = $loanService;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws Throwable
     */
    public function handle()
    {
        $this->log("----- START");
        $log = $this->logService->createCronLog($this->getNameForDb());
        $start = microtime(true);


        // CLI params
        $loanId = $this->argument('loanId');
        $loanId = !empty($loanId) ? (int) $loanId : null;
        $date = $this->parseDate($this->argument('date'));
        $date = !empty($date) ? Carbon::parse($date) : Carbon::today();


        $toBeDone = 0;
        $updatedLoans = [
            'overdue_days' => 0,
            'payment_status' => 0,
        ];


        try {

            $activeLoans = $this->loanService->getActiveLoansWithFirstUnpaidInstallment($loanId);
            $toBeDone = $activeLoans->count();

            $updatedLoans = $this->loanService->refreshPaymentStatuses($activeLoans, $date);

        } catch (Throwable $e) {
            $this->log(
                'Payment status update failed. Error' . $e->getMessage()
                . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
        }


        $msg = 'Payment status refreshed (overdue_days: '
            . $updatedLoans['overdue_days'] . ', payment_status: '
            . $updatedLoans['payment_status'] . ')';
        $log->finish($start, $toBeDone, $updatedLoans['payment_status'], $msg);
        $this->log($msg);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');


        return true;
    }
}
