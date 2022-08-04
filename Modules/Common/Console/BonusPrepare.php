<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Modules\Common\Entities\Investor;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\LogService;
use Throwable;

class BonusPrepare extends CommonCommand
{
    protected $name = 'script:bonus:prepare';
    protected $signature = 'script:bonus:prepare {date?}';
    protected $logChannel = 'bonus_prepare';
    protected $description = 'Bonus for investors';

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
        // CLI param
        $dateParam = $this->parseDate($this->argument('date'));
        $date = !empty($dateParam) ? Carbon::parse($dateParam) : Carbon::today();

        $this->log("----- START");
        $log = $this->logService->createCronLog($this->getNameForDb());
        $start = microtime(true);
        $count = 0;

        try {
            $investors = $this->investorService->getCalculatedReferralsForBonus($date);
            $count = count($investors);
            $this->log(
                'Success fetch: ' . $count . ' investor(s) for bonus for period' . ' ' . $date->format('d-m-Y')
            );
        } catch (Throwable $e) {
            $this->log('Bonus To Investors failed. Error' . $e->getMessage());
        }

        $log->finish(
            $start,
            $count,
            $count,
            'Created bonuses = ' . $count . ', for period' . ' ' . $date->format('d-m-Y')
        );
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }
}
