<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\LogService;
use Throwable;

class UpdateLoanFinalPaymentStatus extends CommonCommand
{
    protected $name = 'script:loans:update-final-payment-status';
    protected $signature = 'script:loans:update-final-payment-status';
    protected $logChannel = 'final_payment_status';
    protected $description = 'Script for updating unlisted loans with null final payment status';

    protected $chunkSize = 10;

    protected LoanService $loanService;
    protected LogService $logService;

    /**
     * Create a new command instance.
     *
     * @param LoanService $loanService
     * @param LogService $logService
     */
    public function __construct(
        LoanService $loanService,
        LogService $logService
    ) {
        $this->loanService = $loanService;
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

        $unlistedLoans = 0;
        $updated = 0;
        try {
            $this->loanService->getUnlistedLoansWithoutFinalPaymentStatus()->chunkById(
                $this->chunkSize,
                function($loans) use(&$unlistedLoans, &$updated, $log, $start) {
                    if (empty($loans)) {
                        $log->finish($start, 0, 0, 'There are no unlisted loans without final payment status');
                        return true;
                    }

                    $unlistedLoans += count($loans);

                    foreach ($loans as $loan) {
                        try {
                            $loan = (Loan::hydrate([(array) $loan]))->first();
                            $this->loanService->updateFinalPaymentStatus($loan, strtolower($loan->getFinalPaymentStatus()));

                            $updated++;

                        } catch (Throwable $e) {
                            $this->log('Error: Loan #' . $loan->loan_id . ' could not be changed: ' . $e->getMessage());
                            continue;
                        }
                    }
                },
                'loan.loan_id',
                'loan_id'
            );

        } catch (Throwable $e) {
            $msg = $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
            $this->log($msg);
        }

        if (empty($msg)) {
            $msg = 'Updated unlisted loans: ' . $updated . ' (Total = ' . $unlistedLoans . ')';
        }

        $log->finish($start, $unlistedLoans, $updated, $msg);
        $this->log($msg);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');

        return true;
    }
}
