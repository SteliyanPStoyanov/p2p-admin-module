<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\AutoRebuyLoanService;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\LogService;
use Throwable;

class DailyAutoRebuy extends CommonCommand
{
    protected $name = 'script:loans:auto-rebuy';
    protected $signature = 'script:loans:auto-rebuy {loanId?} {date?}';
    protected $logChannel = 'daily_auto_rebuy';
    protected $description = 'Script for auto-rebuying loans with overdue > max_overdue setting or specified loanId';

    protected $chunkSize = 10;

    protected LoanService $loanService;
    protected DistributeService $distributeService;
    protected AutoRebuyLoanService $autoRebuyLoanService;
    protected LogService $logService;

    /**
     * Create a new command instance.
     *
     * @param LoanService $loanService
     * @param DistributeService $distributeService
     * @param AutoRebuyLoanService $autoRebuyLoanService
     * @param LogService $logService
     */
    public function __construct(
        LoanService $loanService,
        DistributeService $distributeService,
        AutoRebuyLoanService $autoRebuyLoanService,
        LogService $logService
    ) {
        $this->loanService = $loanService;
        $this->distributeService = $distributeService;
        $this->autoRebuyLoanService = $autoRebuyLoanService;
        $this->logService = $logService;

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
        $start = microtime(true);
        $this->log("----- START");
        $log = $this->logService->createCronLog($this->getNameForDb());


        // CLI params
        $loanId = $this->argument('loanId');
        $loanId = !empty($loanId) ? (int)$loanId : null;


        // we should create payments as payments done yesterday, since we do update on next day
        $dateParam = $this->parseDate($this->argument('date'));
        $date = !empty($dateParam) ? Carbon::parse($dateParam) : Carbon::yesterday()->endOfDay();
        $dateCompare = !empty($dateParam) ? Carbon::parse($dateParam) : Carbon::yesterday();
        $dateCompare->hour(00);
        $dateCompare->minute(00);
        $dateCompare->second(00);

        $rebuyed = 0;
        $toRebuy = 0;
        $maxOverDueDays = (int) \SettingFacade::getSettingValue(
            Setting::MAX_ACCEPTABLE_OVERDUE_DAYS_KEY
        );


        // get loans with overdue & manualy imported as unlisted
        try {

            $this->loanService->getLoansForAutoRebuy($maxOverDueDays, $loanId)->chunkById(
                $this->chunkSize,
                function($loans) use(&$toRebuy, &$rebuyed, $log, $start, $date, $dateCompare) {

                    if (empty($loans)) {
                        $log->finish($start, 0, 0, 'There are no loans for rebuying');
                        return true;
                    }
                    $toRebuy += count($loans);

                    foreach ($loans as $loan) {

                        DB::beginTransaction();

                        try {
                            $loan = (Loan::hydrate([(array) $loan]))->first();
                            $investments = $loan->distinctInvestments();
                            $earlyRepayment = $dateCompare->gt(Carbon::parse($loan->final_payment_date));

                            // do repayment for investors
                            $this->distributeService->distributeInvestments(
                                $loan,
                                $investments,
                                $earlyRepayment,
                                true,
                                $date
                            );

                            $loan->rebuy($date);

                            DB::commit();

                            $rebuyed += 1;

                        } catch (Throwable $e) {
                            DB::rollBack();
                            $this->log('ERROR: Loan #' . $loan->loan_id . ' could not be rebuyed ' . $e->getMessage());
                            continue;
                        }
                    }
                },
                'loan.loan_id',
                'loan_id'
            );

        } catch (Throwable $e) {
            $msg = 'ERROR: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
            $this->log($msg);
        }

        if (empty($msg)) {
            $msg = 'Rebuyed loans: ' . $rebuyed . ' (Total = ' . $toRebuy . ')';
        }

        $log->finish($start, $toRebuy, $rebuyed, $msg);
        $this->log($msg);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }
}
