<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\File;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\LogService;
use Modules\Core\Services\StorageService;
use Throwable;

/**
 * In CRM we could upload a file with loan IDs, which we don't want to offer in Afranga
 * So this command will unlist these loans, reading them from uloaded file(s)
 */
class ImportUnlistedLoans extends CommonCommand
{
    private $storageService;
    private $importService;
    private $logService;

    protected $logChannel = 'import_unlisted_loans';

    protected $name = 'script:unlisted-loans:import';
    protected $signature = 'script:unlisted-loans:import {file?}';
    protected $description = 'Import unlisted loans using pivot file';

    /**
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

        // CLI param
        $filePath = $this->argument('file');

        // get imported files on current date
        $files = $this->storageService->getImportUnlistedLoanFiles($filePath);
        if (empty($files)) {
            $this->log('No files to import. ');
            return true;
        }


        // loop through files and parse one by one for credit/percent
        foreach ($files as $num => $file) {
            $startImport = microtime(true);
            $lenderIds = $this->storageService->getParsedUnlistedData($file);

            if (empty($lenderIds)) {
                $this->log('No data in file: ' . $file);
                $log = $this->logService->createCronLog(
                    $this->getNameForDb(),
                    $file,
                    'No data in file'
                );
                $this->storageService->moveImportedFileWithUnlistedLoans($file);
                continue;
            }


            if (true === $this->importUnlistedLoans(
                $lenderIds,
                $file,
                $startImport
            )) {
                $this->storageService->moveImportedFileWithUnlistedLoans($file);
            }
        }


        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }

    private function importUnlistedLoans(
        array $lenderIds,
        string $file,
        $startImport
    )
    {
        $log = $this->logService->createCronLog($this->getNameForDb(), $file);
        $file = $this->storageService->getFileNameFromPath($file);


        try {
            // check if we already have them in Afranga
            $existingLoans = $this->importService->getExistingUnListedLoans($lenderIds);
            $this->log('Existing unlisted loans count: ' . count($existingLoans) . ', file:' . $file);

            // remove loans that are already imported
            $lenderIds = array_diff($lenderIds, $existingLoans);
            if (empty($lenderIds)) {
                // log
                $log->finish($startImport, 0, 0, 'all unlisted loans are already imported');
                $this->log('Imported: ' . 0 . ' - all unlisted loans are already imported');

                return true;
            }

            // add additional fields to loans
            $import = $this->importService->prepareUnlistedLoans($lenderIds);

            // multipple insert
            $imported = $this->importService->unlistedLoansMassInsert($import);
            $importedCount = empty($imported) ? 0 : count($import);

        } catch (Throwable $e) {
            $this->log('Error: ' . $e->getMessage());
            $log->finish($startImport, null, null, 'Error: ' . $e->getMessage());
            return false;
        }


        // log
        $log->finish($startImport, $importedCount, $importedCount, 'Imported: ' . $importedCount);
        $this->log('Imported: ' . $importedCount);

        return true;
    }
}
