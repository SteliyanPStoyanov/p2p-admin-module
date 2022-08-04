<?php

namespace Modules\Common\Console;


use Modules\Common\Services\InvestorService;
use Modules\Common\Services\LogService;
use Throwable;

class BonusHandle extends CommonCommand
{
    protected $name = 'script:bonus:handle';
    protected $signature = 'script:bonus:handle';
    protected $logChannel = 'bonus_handle';
    protected $description = 'Creating Tasks for bonus';

    protected LogService $logService;
    protected InvestorService $investorService;

    public function __construct(
        LogService $logService,
        InvestorService $investorService
    ) {
        $this->logService = $logService;
        $this->investorService = $investorService;
        parent::__construct();
    }


    public function handle()
    {
        $this->log("----- START");
        $log = $this->logService->createCronLog($this->getNameForDb());
        $start = microtime(true);
        $tasksCount = 0;

        try {
            $tasksCount = $this->investorService->createBonusTasks();
            $this->log('Created ' . $tasksCount . ' task(s)');
        } catch (Throwable $e) {
            $this->log('Error. Failed to created bonus taks, msg: ' . $e->getMessage());
        }

        $log->finish($start, $tasksCount, $tasksCount, 'Finished creating tasks for pay bonus for investors');
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }
}
