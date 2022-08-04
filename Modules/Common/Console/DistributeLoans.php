<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Modules\Common\Console\CommonCommand;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\LogService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * When we receive repaid loans we save them in repaid_loan table
 * Then we loop over them and distribute payments interests for investors
 *
 * CMD: docker-compose exec afranga_app php artisan script:repaid-loans:distribute
 * CMD: docker-compose exec afranga_app php artisan script:repaid-loans:distribute 105431
 */
class DistributeLoans extends CommonCommand
{
    private $distributeService;
    private $importService;
    private $logService;
    private $limit = 200;

    protected $name = 'script:repaid-loans:distribute';
    protected $signature = 'script:repaid-loans:distribute {loanId?} {limit?}';
    protected $description = 'Distribute repaid loans imported from Nefin';
    protected $logChannel = 'distr_loans';

    /**
     * Create a new command instance.
     *
     * @param ImportService $importService
     * @param DistributeService $distributeService
     * @param LogService $logService
     */
    public function __construct(
        ImportService $importService,
        DistributeService $distributeService,
        LogService $logService
    ) {
        $this->distributeService = $distributeService;
        $this->importService = $importService;
        $this->logService = $logService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // parse params
        $loanId = (int) $this->argument('loanId');
        if (
            !empty($this->argument('limit'))
            && intval($this->argument('limit')) > 0
        ) {
            $this->limit = (int) $this->argument('limit');
        }

        return $this->distributeLoans($loanId);
    }

    public function distributeLoans(int $loanId = null)
    {
        $this->log("----- START");
        $start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());


        $repaidLoansCount = $this->importService->getNewRepaidLoansCount();
        $this->log('Total new repaid loans: ' . $repaidLoansCount);
        if (empty($repaidLoansCount)) {
            $log->finish($start, 0, 0, 'There are no new repaid loans(total)');
            return true;
        }


        $distributed = 0;
        $dateForTransaction = Carbon::yesterday()->endOfDay();
        $this->importService->getNewRepaidLoans($loanId)->chunkById(
            $this->limit,
            function ($repaidLoans) use (&$distributed, &$log, $start, $dateForTransaction) {

                $repaidLoans = $repaidLoans->all();
                $this->log('Chunk new repaid loans: ' . count($repaidLoans));

                if (empty($repaidLoans)) {
                    $log->finish($start, 0, 0, 'There are no new repaid loans(chunk)');
                    return true;
                }

                // we should create payments as payments done yesterday, since we do update on next day
                $distributedChunk = $this->distributeService->distributeLoans(
                    $repaidLoans,
                    $dateForTransaction
                );


                $this->log('Distributed repaid loans(chunk): ' . $distributedChunk);
                $distributed += $distributedChunk;
            },
            'repaid_loan_id'
        );


        $this->log('Distributed: ' . $distributed);
        $log->finish($start, $repaidLoansCount, $distributed, 'Distributed: ' . $distributed);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');


        return $distributed;
    }
}
