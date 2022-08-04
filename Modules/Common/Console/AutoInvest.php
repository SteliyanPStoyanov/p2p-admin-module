<?php

namespace Modules\Common\Console;

use \Throwable;
use Modules\Common\Console\CommonCommand;
use Modules\Common\Services\AutoInvestService;
use Modules\Common\Services\InvestmentService;
use Modules\Common\Services\LogService;

class AutoInvest extends CommonCommand
{
    protected $name = 'script:auto-invest';
    protected $signature = 'script:auto-invest {investorId?} {investStrategyId?} {multiRun?}';
    protected $description = 'Run all active invest strategies from high to low priority,'
                           . 'Also could run all investor strategies or specific strategy.';
    protected $logChannel = 'daily_auto_invest';

    protected $autoInvestService = null;
    protected $investmentService = null;
    protected $importService = null;
    protected $logService = null;

    public function __construct(
        AutoInvestService $autoInvestService,
        InvestmentService $investmentService,
        LogService $logService
    )
    {
        parent::__construct();
        $this->autoInvestService = $autoInvestService;
        $this->investmentService = $investmentService;
        $this->logService = $logService;
    }

    public function handle()
    {
        $investorId = (int) $this->argument('investorId');
        $investStrategyId = (int) $this->argument('investStrategyId');

        $multiRun = true;
        if (
            !is_null($this->argument('multiRun'))
            && 0 == $this->argument('multiRun')
        ) {
            $multiRun = false;
        }

        return $this->autoInvest($investorId, $investStrategyId, $multiRun);
    }

    public function autoInvest(
        int $investorId = 0,
        int $investStrategyId = 0,
        bool $multiRun = true
    ): bool
    {
        $start = microtime(true);
        $this->log("----- START -----");
        $this->log("Params: investorId={$investorId}, strategyId={$investStrategyId}");


        $log = $this->logService->newLog(
            $this->getNameForDb(),
            ($investorId > 0 || $investStrategyId > 0) // manual or auto
        );


        try {
            // get investors with their auto invest strategies order by priority
            $investorsAndStrategies = $this->autoInvestService->getInvestorsWithHighestStrategy(
                $investorId,
                $investStrategyId
            );
            if (empty($investorsAndStrategies)) {
                $this->log('There are no invest strategies ($investorsAndStrategies = 0)');
                $log->finish($start, 0, 0, 'There are no invest strategies ($investorsAndStrategies = 0)');
                return false;
            }

            // shuffle investors and loop their strategies
            shuffle_assoc($investorsAndStrategies);

        } catch (\Throwable $e) {
            $msg = 'Error' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine();

            $this->log($msg);
            $log->finish($start, 0, 0, $msg);

            return false;
        }


        $total = 0;
        $handled = 0;
        foreach ($investorsAndStrategies as $investorId => $strategy) {
            $this->log('- Proceeding investor #' . $investorId . ', strategy #' . $strategy->getId());

            $total++;

            $done = $this->investmentService->massInvestByStrategy(
                $strategy,
                $multiRun // after finishing run next strategy
            );
            if (false === $done) {
                $this->log('Failed auto investing, Investor #' . $investorId . ', Strategy #' . $strategy->getId());
                continue;
            }

            $handled++;
        }


        $msg = 'Total strategies = ' . $total . ', handled = ' . $handled;
        $log->finish($start, $total, $handled, $msg);
        $this->log($msg);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');


        return true;
    }
}
