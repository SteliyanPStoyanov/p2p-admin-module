<?php

namespace Modules\Common\Console;

use \Exception;
use Modules\Common\Console\CommonCommand;
use Modules\Common\Services\ImportService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * CMD: docker-compose exec afranga_app php artisan script:loans:add-contract-numbers
 * CMD: docker-compose exec afranga_app php artisan script:loans:add-contract-numbers 123321
 */
class AddContractNumbers extends CommonCommand
{
    private $importService;
    private $limit = 1000;

    protected $name = 'script:loans:add-contract-numbers';
    protected $signature = 'script:loans:add-contract-numbers {loan_id?} {limit?}';
    protected $description = 'Add contract numbers to loans without it';

    /**
     * Create a new command instance.
     *
     * @param ImportService $importService
     */
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
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

        $toBeDone = 0;
        $done = 0;
        $this->importService->getLoansWithoutContractId()->chunkById(
            $this->limit,
            function ($loans) use (&$toBeDone, &$done) {
                $loansArr = [];
                foreach ($loans as $loan) {
                    $loansArr[$loan->lender_id] = $loan;
                }

                $toBeDone += count($loansArr);
                $this->log('Loans without contract_id: ' . $toBeDone);
                if (empty($loansArr)) {
                    $this->log('Nothing todo');
                    return false;
                }


                $contractNumbers = $this->importService->getContractNumbersByCreditIds(
                    array_keys($loansArr)
                );
                if (empty($contractNumbers)) {
                    $this->log('Could not find contract, numbers');
                    return false;
                }


                foreach ($loansArr as $lenderId => $loan) {
                    if (!empty($contractNumbers[$lenderId])) {
                        $loan->contract_id = $contractNumbers[$lenderId];
                        $loan->save();
                        $done++;

                        $this->log('Loan #' . $loan->loan_id . ' -> ' . $contractNumbers[$lenderId]);
                    }
                }
            },
            'loan_id',
        );

        $this->log('To be done: ' . $toBeDone . ', Done: ' . $done);
        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
        return true;
    }
}
