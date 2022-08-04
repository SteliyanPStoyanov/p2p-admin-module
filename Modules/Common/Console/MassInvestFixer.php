<?php

namespace Modules\Common\Console;

use Modules\Common\Jobs\InvestAll\InvestAllLoanContractJob;
use Modules\Common\Jobs\InvestAll\InvestAllPlansJob;
use Modules\Common\Jobs\InvestAll\InvestAllRelationJob as Queue;
use Modules\Common\Services\InvestService;

/**
 * search transactions without investment_id -> update
 * search loan_amount_available without investment_id -> update
 * search investments without investor plan -> add
 * search investments without loan contracts -> add
 */
class MassInvestFixer extends CommonCommand
{
    private $service = null;
    private $types = [
        'transaction' => 'getLostInvestmentsVsTransactions',
        'loan_amount_available' => 'getLostInvestmentsVsLoanAmountStats',
        'investor_installment' => 'getLostInvestmentsVsInvestorPlans',
        'loan_contract' => 'getLostInvestmentsVsLoanContracts',
    ];

    protected $name = 'script:mass-invest:fixer';
    protected $signature = 'script:mass-invest:fixer {investorId?} {type?}';
    protected $logChannel = 'mass_invest_fixer';
    protected $description = 'Search lost records without links, send report';

    public function __construct()
    {
        parent::__construct();
        $this->service = \App::make(InvestService::class);
    }

    public function handle()
    {
        $this->log("----- Mass Invest Checker -----");
        $start = microtime(true);


        $investorId = (int) $this->argument('investorId');
        $type = $this->argument('type');
        $types = $this->types;
        if (!is_null($type)) {
            if (!array_key_exists($type, $this->types)) {
                $type = null;
            } else {
                $types = [$type => $this->types[$type]];
            }
        }
        $this->log("investorId = " . $investorId);
        $this->log("type(s): " . implode(', ', $types));


        foreach ($types as $type => $method) {
            $this->proceed($type, $investorId);
        }


        $this->log('Exec.time: ' . round((microtime(true) - $start), 2) . ' second(s)');
    }

    public function proceed(string $type, int $investorId = null): bool
    {
        $method = $this->types[$type];
        $investments = $this->service->$method($investorId);
        $this->log("count = " . count($investments) . ', type: ' . $type);

        if (empty($investments)) {
            $this->log("- skipped, no records for type: " . $type);
            return false;
        }


        if ('transaction' == $type) {
            $investorIds = $this->getUniqueInvestorIds($investments);
            foreach ($investorIds as $invId) {
                $this->service->updateTransactionsWithoutRelations($invId);
            }
            $this->log("- done, type: " . $type);
            return true;
        }


        if ('loan_amount_available' == $type) {
            $investorIds = $this->getUniqueInvestorIds($investments);
            foreach ($investorIds as $invId) {
                $this->service->updateLoansAmountWithoutRelations($invId);
            }
            $this->log("- done, type: " . $type);
            return true;
        }


        if ('investor_installment' == $type) {
            $chunks = array_chunk($investments, 50);
            foreach ($chunks as $chunk) {
                InvestAllPlansJob::dispatch($chunk)->onQueue(Queue::QUEUE1);
            }
            $this->log("- done, type: " . $type);
            return true;
        }


        if ('loan_contract' == $type) {
            $chunks = array_chunk($investments, 10);
            foreach ($chunks as $chunk) {
                InvestAllLoanContractJob::dispatch($chunk)->onQueue(Queue::QUEUE2);
            }
            $this->log("- done, type: " . $type);
            return true;
        }
    }

    private function getUniqueInvestorIds(array $investments): array
    {
        $ids = [];

        foreach ($investments as $investment) {
            $ids[$investment->investor_id] = $investment->investor_id;
        }

        return $ids;
    }
}
