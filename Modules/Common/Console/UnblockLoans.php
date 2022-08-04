<?php

namespace Modules\Common\Console;

use Modules\Common\Services\LoanService;
use Modules\Common\Services\LogService;
use Throwable;

class UnblockLoans extends CommonCommand
{

    protected $logChannel = 'unblock_loans';

    protected $name = 'script:loans:unblock';
    protected $signature = 'script:loans:unblock';
    protected $description = 'Unblock Loans';

    protected LoanService $loanService;
    protected LogService $logService;


    public function __construct(
        LoanService $loanService,
        LogService $logService
    ) {
        $this->loanService = $loanService;
        $this->logService = $logService;
        parent::__construct();
    }

    public function handle()
    {
        $this->log("----- START");
        $start = microtime(true);

        $log = $this->logService->createCronLog($this->getNameForDb());

        try {
            $this->loanService->unblockLoans();
            $this->log('Success unblock loans');
        } catch (Throwable $e) {
            $this->log('Error: ' . $e->getMessage());
            $log->finish($start, null, null, 'Error: ' . $e->getMessage());
            return false;
        }

        $log->finish($start, 0, 0, 'Finished unblock loans success');
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }
}
