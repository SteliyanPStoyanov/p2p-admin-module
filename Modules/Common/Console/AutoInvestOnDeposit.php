<?php
declare(strict_types=1);

namespace Modules\Common\Console;

use Modules\Common\Services\LogService;
use Modules\Common\Services\TransactionService;

/**
 * When investor do a deposit, after 15 min we check if he has auto-invest starategies
 * If YES - we start the auto invest chain
 */
class AutoInvestOnDeposit extends CommonCommand
{
    protected $name = 'script:auto-invest-deposit-added';
    protected $signature = 'script:auto-invest-deposit-added {delayInMinutes?}';
    protected $description = 'Resume autoinvest after deposit loaded';
    protected $logChannel = 'invest/resume_autoinvest';

    protected LogService $logService;
    protected TransactionService $transactionService;

    protected float $start;

    public function __construct(
        LogService $logService,
        TransactionService $transactionService
    )
    {
        $this->logService = $logService;
        $this->transactionService = $transactionService;
        parent::__construct();
    }

    public function handle(): bool
    {
        $this->log("----- START -----");
        $this->start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());


        $beforeMin = (int) $this->argument('delayInMinutes');
        if ($beforeMin < 1) {
            $beforeMin = \SettingFacade::getInvestDelayAfterDeposit();
        }
        $this->log("Delay: {$beforeMin} min(s)");


        $investors = $this->transactionService
            ->getInvestorsMadeDepositWithAutoInvestStrategies($beforeMin);
        $this->log("Total investors: " . count($investors));


        foreach ($investors as $investor) {
            \Artisan::call('script:auto-invest ' . $investor->investor_id);
            $this->log("~ handle investor: " . $investor->investor_id);
        }


        $total = count($investors);
        $log->finish((string) $this->start, $total, $total, 'Start auto-investing for ' . $total . ' investor(s)');
        $this->log('Exec.time: ' . round((microtime(true) - $this->start), 2) . ' second(s)');
        return true;
    }
}
