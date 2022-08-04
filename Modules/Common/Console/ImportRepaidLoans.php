<?php

namespace Modules\Common\Console;

use Modules\Common\Console\CommonCommand;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\LogService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * CMD: docker-compose exec afranga_app php artisan script:repaid-loans:import
 * CMD: docker-compose exec afranga_app php artisan script:repaid-loans:import 105431
 */
class ImportRepaidLoans extends CommonCommand
{
    private $importService;
    private $logService;
    private $limit = 500;

    protected $name = 'script:repaid-loans:import';
    protected $signature = 'script:repaid-loans:import {lenderId?} {limit?}';
    protected $logChannel = 'import_repaid_loans';
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @param ImportService $importService
     * @param LogService $logService
     */
    public function __construct(
        ImportService $importService,
        LogService  $logService
    )
    {
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
        $lenderId = $this->argument('lenderId');
        if (
            !empty($this->argument('limit'))
            && intval($this->argument('limit')) > 0
        ) {
            $this->limit = (int) $this->argument('limit');
        }


        if (is_numeric($lenderId)) {
            return $this->importRepaidLoan($lenderId);
        }

        return $this->importRepaidLoans();
    }

    public function importRepaidLoans()
    {
        $this->log("----- START");
        $start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());



        $loansCount = $this->importService->getActiveLoansCount(true);
        $this->log('Total active loans: ' . $loansCount);
        if (empty($loansCount)) {
            $log->finish($start, 0, 0, 'There are no active loans(total)');
            return true;
        }


        $imported = 0;
        $this->importService->getActiveLoansDBSource(true)->chunkById(
            $this->limit,
            function($loans) use(&$imported, $log ,$start) {
                $loans = $loans->all();


                $this->log('Chunk active loans: ' . count($loans));
                if (empty($loans)) {
                    $log->finish($start, 0, 0, 'There are no active loans(chunk)');
                    return true;
                }


                $lenderIds = [
                    'site' => [],
                    'office' => [],
                ];
                array_walk($loans, function ($loan, $key) use (&$lenderIds) {
                    if (Loan::DB_OFFICE == $loan->from_db) {
                        $lenderIds['office'][$loan->lender_id] = $loan->lender_id;
                    } else {
                        $lenderIds['site'][$loan->lender_id] = $loan->lender_id;
                    }
                });


                $repaidLoans = [];
                if (!empty($lenderIds['site'])) {
                    $repaidLoans += $this->importService->getRepaidLoans($lenderIds['site']);
                }
                if (!empty($lenderIds['office'])) {
                    // set db according to file type: site OR office
                    $this->importService->setDb(Loan::DB_OFFICE);

                    $repaidLoans += $this->importService->getRepaidLoans($lenderIds['office']);
                }


                $repaidLoansCount = count($repaidLoans);
                $this->log('Repaid loans: ' . $repaidLoansCount);


                $importedChunk = $this->importService->addRepaidLoans($repaidLoans);
                $this->log('Imported repaid loans: ' . $repaidLoansCount);

                $imported += $repaidLoansCount;
            },
            'loan.loan_id',
            'loan_id'
        );


        $log->finish($start, $imported, $imported, 'Imported: ' . $imported);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return $imported;
    }

    public function importRepaidLoan(int $lenderId)
    {
    }
}
