<?php

namespace Modules\Common\Jobs\InvestAll;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\Affiliate;
use Modules\Common\Services\InvestService;
use Modules\Common\Services\InvestorService;
use \Throwable;

/**
 * This job always exec after InvestAllJob
 * Idea of it is to update addition relations:
 * - 'investment_id' - transaction
 * - 'investment_id' - loan_amount_available
 *
 * AND create jobs for investor_installments & loan contracts
 */
class InvestAllRelationJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        SerializesModels,
        Queueable;

    const CHUNK_SIZE = 25;
    const QUEUE1 = 'investor_plans';
    const QUEUE2 = 'loan_contracts';
    const QUEUE3 = 'affiliate';

    private $investorId = null;
    private $runOtherJobs = true;
    private $isSecondaryMarket = false;

    public function __construct(int $investorId, bool $runOtherJobs = true, bool $isSecondaryMarket = false)
    {
        $this->investorId = $investorId;
        $this->runOtherJobs = $runOtherJobs;
        $this->isSecondaryMarket = $isSecondaryMarket;
    }

    public function handle(InvestService $service)
    {
        dump('---- Relations ----');
        $start = microtime(true);


        try {
            dump('Investor #' . $this->investorId);


            // check if we have uncompeted investments
            $investments = $service->getInvestmentsNotLinkedToTransactions(
                $this->investorId
            );
            if (empty($investments)) {
                dump('No records without relations');
                return;
            }
            dump('Uncompleted investment count = ' . count($investments));


            // strategy here is to do chunk plans/contracts
            // since one by one is prety slow
            // but proceed all in once will throw a timeout exception,
            // since it could be thousands items
            $chunks = $investments->chunk(self::CHUNK_SIZE);
            $chunks->toArray();

            $investor = \App::make(InvestorService::class)->getById($this->investorId);
            $isAffiliate = ($investor->isActiveAffiliate() === true);

            // add jobs for creating: investor plans and loan contracts
            foreach ($chunks as $chunk) {
                $investments = $chunk->all();

                InvestAllPlansJob::dispatch($investments)->onQueue(self::QUEUE1);

                if (false == $this->isSecondaryMarket) {
                    InvestAllLoanContractJob::dispatch($investments)->onQueue(self::QUEUE2);
                    if ($isAffiliate) {
                        InvestAllAffiliateJob::dispatch($investments)->onQueue(self::QUEUE3);
                    }
                }
            }
            dump('Added jobs for: plans/contracts');


            // relations
            DB::beginTransaction();
            try {
                $service->updateLoansAmountWithoutRelations($this->investorId);
                $service->updateTransactionsWithoutRelations($this->investorId);

                DB::commit();
                dump('Updated investments/transaction relations');
            } catch (Throwable $e) {
                DB::rollback();
                throw $e;
            }
        } catch (Throwable $e) {
            $msg = 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
            Log::channel('invest_all')->error('Error (RelationJob): ' . $msg);
            dump($msg);
        }


        dump('Total exec.time: ' . round((microtime(true) - $start), 2) . ' sec(s)');
    }
}
