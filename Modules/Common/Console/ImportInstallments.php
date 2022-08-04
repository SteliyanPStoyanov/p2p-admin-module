<?php

namespace Modules\Common\Console;

use \Exception;
use Modules\Common\Console\CommonCommand;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\LogService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

/**
 * CMD: docker-compose exec afranga_app php artisan script:installments:import
 * CMD: docker-compose exec afranga_app php artisan script:installments:import 105431
 */
class ImportInstallments extends CommonCommand
{
    private $importService;
    private $logService;
    private $limit = 200;

    protected $logChannel = 'import_installments';

    protected $name = 'script:installments:import';
    protected $signature = 'script:installments:import {lenderId?} {limit?}';
    protected $description = 'Import installments from Nefin for loans in Afranga without installments';

    /**
     * Create a new command instance.
     *
     * @param ImportService $importService
     * @param LogService $logService
     */
    public function __construct(ImportService $importService, LogService $logService)
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
        $this->log("----- START");

        // parse params
        $lenderId = $this->argument('lenderId');
        if (
            !empty($this->argument('limit'))
            && intval($this->argument('limit')) > 0
        ) {
            $this->limit = (int)$this->argument('limit');
        }


        // import installment of current loan (by lenderID)
        if (is_numeric($lenderId)) {
            return $this->importInstallmentsForLoan($lenderId);
        }

        return $this->importInstallments();
    }

    private function importInstallments()
    {
        $start = microtime(true);


        // create main log point
        $log = $this->logService->createCronLog($this->getNameForDb());
        $counts = [
            'loans' => 0,
            'installments' => 0,
        ];


        try {
            // check for counts first, which we need to handle(totals)
            $newloansCount = $this->importService->getNewLoansCount();
            $this->log('Loans without installments(total) = ' . $newloansCount);


            // nothing todo, if all loans have installments
            if (empty($newloansCount)) {
                $log->finish($start, 0, 0, 'No loans without installments');
                return true;
            }

            $loansCountToImport = 0;
            $installmentsToImportCount = 0;


            // Loans from site
            // get array chunk of loans without installments, where lender_id is array key: [lender_id] => loan;
            $this->importService->getNewLoans()->chunkById(
                $this->limit,
                function ($loans) use (&$loansCountToImport, &$counts, &$installmentsToImportCount) {

                    $newLoans = [];
                    foreach ($loans as $loan) {
                        $newLoans[$loan->lender_id] = $loan;
                    }

                    $loansCountToImport += count($newLoans);
                    $this->log('Loans without installments(chunk) = ' . count($newLoans) . ', limit = ' . $this->limit);


                    // get installments from Nefin for new loans, by lender_id
                    $lenderIds = array_keys($newLoans);
                    $installmentsToImport = $this->importService->getInstallmentsForCredits(
                        $lenderIds,
                        (50 * $this->limit)
                    );
                    $installmentsToImportCount += count($installmentsToImport);
                    $this->log('Installments chunk for import = ' . count($installmentsToImport));


                    // import installments and update loans
                    $chunkCount = $this->importService->addInstallmentsAndUpdateLoans(
                        $installmentsToImport,
                        $newLoans
                    );
                    $this->log('Site Imported: ' . $chunkCount['installments'] . ' installments, for ' . $chunkCount['loans'] . ' loans');

                    $counts['loans'] += $chunkCount['loans'];
                    $counts['installments'] += $chunkCount['installments'];
                },
                'loan_id'
            );



            // set db according to file type: site OR office
            $this->importService->setDb(Loan::DB_OFFICE);

            // Loans from offices
            // get array chunk of loans without installments, where lender_id is array key: [lender_id] => loan;
            $this->importService->getNewLoans(true)->chunkById(
                $this->limit,
                function ($loans) use (&$loansCountToImport, &$counts, &$installmentsToImportCount) {

                    $newLoans = [];
                    foreach ($loans as $loan) {
                        $newLoans[$loan->lender_id] = $loan;
                    }

                    $loansCountToImport += count($newLoans);
                    $this->log('Loans without installments(chunk) = ' . count($newLoans) . ', limit = ' . $this->limit);


                    // get installments from Nefin for new loans, by lender_id
                    $lenderIds = array_keys($newLoans);
                    $installmentsToImport = $this->importService->getInstallmentsForCredits(
                        $lenderIds,
                        (50 * $this->limit)
                    );
                    $installmentsToImportCount += count($installmentsToImport);
                    $this->log('Installments chunk for import = ' . count($installmentsToImport));


                    // import installments and update loans
                    $chunkCount = $this->importService->addInstallmentsAndUpdateLoans(
                        $installmentsToImport,
                        $newLoans
                    );
                    $this->log('Office Imported: ' . $chunkCount['installments'] . ' installments, for ' . $chunkCount['loans'] . ' loans');

                    $counts['loans'] += $chunkCount['loans'];
                    $counts['installments'] += $chunkCount['installments'];
                }
            );

        } catch (Throwable $e) {
            $this->log('Error: ' . $e->getMessage());
            $log->finish($start, null, null, 'Error: ' . $e->getMessage());
            return false;
        }


        // $log
        $msg = 'Imported: ' . $counts['installments'] . ' installment(s), ';
        $msg .= 'for ' . $counts['loans'] . ' loan(s)';
        $log->finish($start, $installmentsToImportCount, $counts['installments'], $msg);
        $this->log($msg);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 3) . ' second(s)');


        return true;
    }

    private function importInstallmentsForLoan(int $lenderId)
    {
    }
}
