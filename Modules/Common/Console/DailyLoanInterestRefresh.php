<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\LogService;
use Throwable;

class DailyLoanInterestRefresh extends CommonCommand
{
    protected $name = 'script:daily-interest-refresh';
    protected $signature = 'script:daily-interest-refresh {loanId?} {date?}';
    protected $logChannel = 'daily_interest_refresh';
    protected $description = 'Refresh accrued_interest and late_interest for investments';

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


        // Взимаме всички активни заеми, които имат инвестиции.
        // взимаме от invest_installment първа не платена вноска и смятаме:
        // ако due_date не е настъпила - update на accrued_interest
        // ако due_date е минала - update на late_interest

        try {
            $loans = $this->loanService->getLoansWithInvestments($loanId);
            $updatedLoans = $this->loanService->recalcInterest($loans, $date);
        } catch (Throwable $e) {
            $this->log(
                'Interest update failed. Error' . $e->getMessage()
                . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
        }

        $log->finish($start, $loans->count(), $updatedLoans, 'Finished interest refresh. Refreshed: ' . $updatedLoans);
        $this->log('Finished interest refresh. Refreshed: ' . $updatedLoans);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');

        return true;
    }
}
