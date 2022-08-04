<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Common\Console\CommonCommand;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\LogService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * When we receive repaid installments we save them in repaid_installment table
 * Then we loop over them and distribute payments interests for investors
 *
 * CMD: docker-compose exec afranga_app php artisan script:repaid-installments:distribute
 * CMD: docker-compose exec afranga_app php artisan script:repaid-installments:distribute 105431
 */
class DistributeInstallments extends CommonCommand
{
    private $distributeService;
    private $importService;
    private $logService;
    private $limit = 200;

    protected $name = 'script:repaid-installments:distribute';
    protected $signature = 'script:repaid-installments:distribute {date?} {loanId?}';
    protected $description = 'Distribute installments imported from Nefin';
    protected $logChannel = 'distr_installments';

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
        // CLI params
        $loanId = $this->argument('loanId');
        $loanId = !empty($loanId) ? (int)$loanId : null;
        // $date = $this->parseDate($this->argument('date'));
        // // we should create payments as payments done yesterday, since we do update on next day
        // $date = !empty($date) ? Carbon::parse($date) : Carbon::yesterday()->endOfDay();
        $date = Carbon::yesterday()->endOfDay();

        return $this->distributeInstallments($date, $loanId);
    }

    public function distributeInstallments(Carbon $date, int $loanId = null)
    {
        $this->log("----- START");
        $start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());


        $newRepInstCount = $this->importService->getNewRepaidInstallmentsCount();
        $this->log('Total new installments: ' . $newRepInstCount);
        if (empty($newRepInstCount)) {
            $log->finish($start, 0, 0, 'There are no new installments(total)');
            return true;
        }

        $distributed = 0;
        $this->importService->getNewRepaidInstallments($loanId)->chunkById(
            $this->limit,
            function ($newRepInst) use (&$log, $start, &$distributed, $date) {

                $newRepInst = $newRepInst->all();
                $this->log('Chunk new installments: ' . count($newRepInst));

                if (empty($newRepInst)) {
                    $log->finish($start, 0, 0, 'There are no new installments(chunk)');
                    return true;
                }

                $distributedChunk = $this->distributeService->distributeInstallments(
                    $newRepInst,
                    $date
                );

                $this->log('Distributed installments(chunk): ' . $distributedChunk);
                $distributed += $distributedChunk;
            },
            'repaid_installment_id'
        );

        $this->log('Distributed installments: ' . $distributed);
        $log->finish($start, $newRepInstCount, $distributed, 'Distributed: ' . $distributed);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');

        return $distributed;
    }
}
