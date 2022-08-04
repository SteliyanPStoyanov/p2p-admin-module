<?php

namespace Modules\Common\Console;

use \Exception;
use Modules\Common\Console\CommonCommand;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\LogService;
use Modules\Core\Services\StorageService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

/**
 * CMD: docker-compose exec afranga_app php artisan script:loans:import - will search files for yesterday in /storage/import/loans/loans_[Y-m-d H:i:s].csv
 * CMD: docker-compose exec afranga_app php artisan script:loans:import 2020-11-14 - will parse file: /storage/import/loans/loans_2020-11-14 [H:i:s].csv
 */
class ImportLoans extends CommonCommand
{
    private $storageService;
    private $importService;
    private $logService;
    private $limit = 500;

    protected $logChannel = 'import_new_loans';

    protected $name = 'script:loans:import';
    protected $signature = 'script:loans:import {filename?} {limit?}';
    protected $description = 'Import new loans to Afranga from Nefin using pivot file';

    /**
     * Create a new command instance.
     *
     * @param ImportService $importService
     * @param StorageService $storageService
     * @param LogService $logService
     */
    public function __construct(
        ImportService $importService,
        StorageService $storageService,
        LogService  $logService
    )
    {
        $this->importService = $importService;
        $this->storageService = $storageService;
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
        $start = microtime(true);


        // parse params
        $filename = $this->argument('filename');
        if (
            !empty($this->argument('limit'))
            && intval($this->argument('limit')) > 0
        ) {
            $this->limit = (int) $this->argument('limit');
        }


        // get imported files on current date
        $files = $this->storageService->getImportLoanFilesByFileName($filename);
        $this->log('There are ' . count($files) . ' imported files, with name: ' . $filename);
        if (empty($files)) {
            $log = $this->logService->createCronLog(
                    $this->getNameForDb(),
                    null,
                    'No new uploaded files for import'
                );
            return true;
        }


        // loop through files and parse one by one for credit/percent
        foreach ($files as $num => $file) {
            $startImport = microtime(true);
            $creditIdsAndPercents = $this->storageService->getParsedData($file);

            if (empty($creditIdsAndPercents)) {
                $this->log('No data in file: ' . $file);
                $log = $this->logService->createCronLog(
                    $this->getNameForDb(),
                    $file,
                    'No data in file'
                );
                $this->storageService->moveImportedFileWithLoans($file, StorageService::IMPORTED_LOANS_DIR);
                continue;
            }


            // get db name, from where we will take the data
            $dbName = (
                $this->storageService->loansFileFromOffice($file)
                ? Loan::DB_OFFICE
                : Loan::DB_SITE
            );


            if (true === $this->importLoans(
                $creditIdsAndPercents,
                $file,
                $dbName,
                $startImport
            )) {
                $this->storageService->moveImportedFileWithLoans($file, StorageService::IMPORTED_LOANS_DIR);
            }
        }


        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }

    private function importLoans(
        array $creditIdsAndPercents,
        string $file,
        string $dbName,
        $startImport
    )
    {
        $log = $this->logService->createCronLog($this->getNameForDb(), $file);
        $file = $this->storageService->getFileNameFromPath($file);
        $lenderIds = array_keys($creditIdsAndPercents); // credit ids from Nefin


        try {
            // check if we already have them in Afranga
            $existingLoans = $this->importService->getExistingLoans($lenderIds);
            $this->log('Existing loans count: ' . count($existingLoans) . ', file:' . $file);


            // remove loans that are already imported
            $lenderIds = array_diff($lenderIds, $existingLoans);
            if (empty($lenderIds)) {
                // log
                $log->finish($startImport, 0, 0, 'all loans are already imported');
                $this->log('Imported: ' . 0 . ' - all loans are already imported');

                return true;
            }


            // set db according to file type: site OR office
            $this->importService->setDb($dbName);


            $importedCountTotal = 0;
            $lenderIdChunks = array_chunk($lenderIds, $this->limit);
            foreach ($lenderIdChunks as $key => $lenderIdChunk) {

                // get loans fron Nefin
                $loansToImport = $this->importService->getLoans($lenderIdChunk);
                $this->log('Loans count from Nefin: ' . count($loansToImport) . ', file:' . $file);

                // add additional fields to loans
                $import = $this->importService->prepareLoans(
                    $loansToImport,
                    $creditIdsAndPercents,
                    $dbName
                );

                // multipple insert
                $imported = $this->importService->loansMassInsert($import);
                $importedCount = empty($imported) ? 0 : count($import);
                $importedCountTotal += $importedCount;
            }

        } catch (Throwable $e) {
            $this->log('Error: ' . $e->getMessage());
            $log->finish($startImport, null, null, 'Error: ' . $e->getMessage());
            return false;
        }


        // log
        $log->finish($startImport, count($lenderIds), $importedCountTotal, 'Imported: ' . $importedCountTotal);
        $this->log('Imported: ' . $importedCountTotal);
        return true;
    }
}
