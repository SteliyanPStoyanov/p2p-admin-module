<?php

namespace Modules\Common\Console;

use Carbon\Carbon;
use Modules\Common\Console\CommonCommand;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\LogService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * CMD: docker-compose exec afranga_app php artisan script:repaid-installments:import
 * CMD: docker-compose exec afranga_app php artisan script:repaid-installments:import 2021-01-10 105431
 */
class ImportRepaidInstallments extends CommonCommand
{
    private $importService;
    private $logService;
    private $limit = 200;

    protected $name = 'script:repaid-installments:import';
    protected $signature = 'script:repaid-installments:import {date?} {loanId?}';
    protected $description = 'Import repaid-installments to Afranga from Nefin';
    protected $logChannel = 'import_payments';

    /**
     * Create a new command instance.
     *
     * @param ImportService $importService
     * @param LogService $logService
     */
    public function __construct(
        ImportService $importService,
        LogService $logService
    ) {
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
        $date = $this->parseDate($this->argument('date'));
        $date = !empty($date) ? Carbon::parse($date) : Carbon::today();

        return $this->importRepaidInstallments($date, $loanId);
    }

    public function importRepaidInstallments(Carbon $date, int $loanId = null)
    {
        $this->log("----- START");
        $start = microtime(true);
        $log = $this->logService->createCronLog($this->getNameForDb());


        $unpaidInstallmentsCount = $this->importService->getUnpaidInstallmentsCount();
        $this->log('Total unpaid payments: ' . $unpaidInstallmentsCount);
        if (empty($unpaidInstallmentsCount)) {
            $log->finish($start, 0, 0, 'There are no unpaid payments(total)');
            return true;
        }

        $imported = 0;
        $paymentsCount = 0;

        $this->importService->getUnpaidInstallments($loanId)->chunkById(
            $this->limit,
            function ($unpaidInstallments) use ($log, $start, &$imported, &$paymentsCount, $unpaidInstallmentsCount) {
                $this->log('Chunk unpaid payments: ' . count($unpaidInstallments));

                if (empty($unpaidInstallmentsCount)) {
                    $log->finish($start, 0, 0, 'There are no unpaid payments(chunk)');
                    return true;
                }


                $unpaidInstallmentsArr = [
                    'site' => [],
                    'office' => [],
                ];
                foreach ($unpaidInstallments as $unpaidInstallment) {
                    $key = (
                        Loan::DB_OFFICE == $unpaidInstallment->from_db
                        ? 'office'
                        : 'site'
                    );

                    $unpaidInstallmentsArr[$key][$unpaidInstallment->lender_installment_id] = $unpaidInstallment->installment_id;
                }


                // check which installments from selected chunk are paid in Nefin
                $repaidInstallments = [];
                if (!empty($unpaidInstallmentsArr['site'])) {
                    $repaidInstallments += $this->importService->getRepaidInstallments(
                        array_keys($unpaidInstallmentsArr['site'])
                    );
                }
                if (!empty($unpaidInstallmentsArr['office'])) {
                    // set db according to file type: site OR office
                    $this->importService->setDb(Loan::DB_OFFICE);

                    $repaidInstallments += $this->importService->getRepaidInstallments(
                        array_keys($unpaidInstallmentsArr['office'])
                    );
                }


                $paymentsCount += count($repaidInstallments);
                $this->log('Paid installments: ' . $paymentsCount);

                if (empty($repaidInstallments)) {
                    $log->finish($start, 0, 0, 'There are no paid payments(Nefin)');
                    return true;
                }


                // save recently paid installments
                $imported += $this->importService->addRepaidInstallments($repaidInstallments);
                $this->log('Imported payments: ' . $paymentsCount);
            },
            'installment.installment_id',
            'installment_id'
        );

        $log->finish($start, $paymentsCount, $paymentsCount, 'Imported: ' . $paymentsCount);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');

        return $imported;
    }
}
