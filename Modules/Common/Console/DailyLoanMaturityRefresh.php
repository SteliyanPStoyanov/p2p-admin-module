<?php

namespace Modules\Common\Console;

use Modules\Common\Services\ImportService;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\LogService;
use Modules\Common\Services\PortfolioService;

class DailyLoanMaturityRefresh extends CommonCommand
{
    protected $name = 'script:daily-maturity-refresh';
    protected $signature = 'script:daily-maturity-refresh {portfolioId?}';
    protected $logChannel = 'daily_maturity_refresh';
    protected $description = 'Check maturity of loans and update portfolios with investments on these loans';

    protected LogService $logService;
    protected PortfolioService $portfolioService;

    /**
     * Create a new command instance.
     *
     * @param LogService $logService
     * @param PortfolioService $portfolioService
     */
    public function __construct(
        LogService $logService,
        PortfolioService $portfolioService
    ) {
        $this->logService = $logService;
        $this->portfolioService = $portfolioService;

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

        // CLI param
        $portfolioId = $this->argument('portfolioId');
        $portfolioId = !empty($portfolioId) ? (int) $portfolioId : null;

        try {
            $portfolios = $this->portfolioService->getPortfoliosWithMaturityRanges($portfolioId);
            $updated = $this->portfolioService->massUpdatePortfolios($portfolios);
        } catch (\Throwable $e) {
            $this->log(
                'Maturity update failed. Error' . $e->getMessage()
                . ', file: ' . $e->getFile() . ', line: ' . $e->getLine()
            );
        }

        $log->finish($start, $updated, $updated, 'Finished maturity refresh. Updated: ' . $updated);
        $this->log('Finished maturity refresh. Updated: ' . $updated);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');

        return true;
    }
}
