<?php

namespace Modules\Common\Jobs\InvestAll;

use \Throwable;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Services\InvestmentService;
use Modules\Common\Services\InvestService;
use Modules\Core\Services\CacheService;
use Modules\Common\Jobs\InvestAll\InvestAllRelationJob;
use Modules\Common\Jobs\InvestAll\BunchHandlerFactory;

class InvestAllJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        SerializesModels,
        Queueable;

    const DELAY_SEC = 60;
    const REL_QUEUE = 'relations';

    protected $investmentBunch = null;
    protected $handler = null;
    protected $loanId = null;

    public function __construct(
        InvestmentBunch $investmentBunch,
        int $loanId = null
    )
    {
        $this->investmentBunch = $investmentBunch;
        $this->loanId = $loanId;
    }

    public function handle(InvestService $investService)
    {
        $msg = '';

        try {
            // when get the bunch from the queue let actualize it
            $this->investmentBunch->refresh();
            $this->initDump();


            // get bunch handler, which will do the main job
            $this->handler = BunchHandlerFactory::build($this->investmentBunch);
            $this->dumpMoneyConditions();


            // check if bunch is good for running
            if (!$this->handler->couldRunBunch()) {

                if ($this->handler->shouldRetry()) {
                    dump('Delayed in queue for ' . self::DELAY_SEC . ' sec');
                    return self::pushToQueue($this->investmentBunch, $this->loanId);
                }

                $this->endJob('Invested = NO | ' . $this->handler->getJsonErrors());
                return false;
            }


            // try to invest
            $investResult = $this->handler->doMassInvest();
            $msg = 'Invested = ' . ($investResult ? 'YES' : 'NO') . (
                true === $investResult
                ? ' | ' . 'Invest Info: ' . sprintf(
                    'Invest Info: %s | Details: %s',
                    $this->handler->getJsonInvestedLoans(),
                    $this->handler->getJsonDetails()
                )
                : ' | ' . sprintf(
                    'Errors: %s | Details: %s',
                    $this->handler->getJsonErrors(),
                    $this->handler->getJsonDetails()
                )
            );


            // if we invested successfully,
            // we run another job to create other relations for investments
            if ($investResult) {
                if ($this->investmentBunch->cart_secondary_id) {

                    InvestAllRelationJob::dispatch($this->investmentBunch->cartSecondary->investor_id, true, true)->onQueue(self::REL_QUEUE);

                    $sellersIds = $this->investmentBunch->getSellerThroughBuyersCart();
                    foreach ($sellersIds as $sellerId) {
                        InvestAllRelationJob::dispatch($sellerId, true, true)->onQueue(self::REL_QUEUE);
                    }
                } else {
                    $id = $this->investmentBunch->investor_id;
                    InvestAllRelationJob::dispatch($id)->onQueue(self::REL_QUEUE);
                }


            }

        } catch (Throwable $e) {
            $msg = 'Error' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine();

            $this->log('Error. handle(). Msg: ' . $msg);
        }

        $this->endJob($msg);
    }

    public static function pushToQueue(
        InvestmentBunch $investmentBunch,
        int $loanId = null,
        bool $runNow = false
    ): bool
    {
        $queueName = self::getQueueName($investmentBunch);

        if (!$runNow) {
            $now = Carbon::now();
            self::dispatch($investmentBunch, $loanId)
                ->onQueue($queueName)
                ->delay($now->addSeconds(self::DELAY_SEC));

            return true;
        }

        self::dispatch($investmentBunch, $loanId)->onQueue($queueName);
        return true;
    }

    public static function getQueueName(InvestmentBunch $investmentBunch): string
    {
        if (empty($investmentBunch->invest_strategy_id)) {
            return 'invests';
        }

        return 'auto_invests';
    }

    /**
     * (OLD)
     * Unique investing flow, where we do one investment in one job operation
     * and send next job to queue for continue buying
     * - search loan for invest
     * - define amount for buying
     * - do invest
     * - put next job to the queue
     */
    private function uniqueInvestFlow()
    {
        // get loan for investing
        $loan = $this->handler->getLoan($this->loanId);
        if (empty($loan->loan_id)) {
            $this->endJob($this->handler->getJsonErrors());
            return false;
        }
        dump('$loan = ' . $loan->loan_id);


        // get amount for investing
        $amount = $this->handler->getAmountToBuy($loan);
        if (empty($amount)) {
            $this->endJob($this->handler->getJsonErrors());
            return false;
        }
        dump('$amount = ' . $amount);


        // investing
        $invested = $investService->invest(
            $this->investmentBunch->investor_id,
            $loan->loan_id,
            $amount,
            Carbon::now(),
            $this->investmentBunch->getId()
        );
        dump('$invested = ' . (int) $invested);


        // start next loop
        self::pushToQueue($this->investmentBunch, $loan->loan_id, true);
    }

    public function endJob(string $msg = ''): bool
    {
        DB::beginTransaction();

        // close investment_bunch
        // release investor(running_bunch_id)
        try {

            $this->investmentBunch->finish($msg);
            $this->handler->getLockedInvestor()->removeRunningBunchId();
            DB::commit();

        } catch (Throwable $e) {
            DB::rollback();

            $this->log(
                'Error. endJob(). Failed to finish bunch: ' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine()
            );

            return false;
        }

        try {
            // clear dashboard cache
            (new CacheService())->remove(
                config('profile.profileDashboard')
                . $this->investmentBunch->investor_id
            );

            // run next strategy for multi bunch
            if ($this->handler->shouldRunNextStrategy()) {

                $nextStrategy = $this->handler->getNextStrategy();
                if (!empty($nextStrategy->invest_strategy_id)) {
                    $service = \App::make(InvestmentService::class);
                    $service->massInvestByStrategy($nextStrategy, true);
                }
            }

        } catch (Throwable $e) {

            $this->log(
                'Error. endJob(): ' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine()
            );

            return false;
        }


        if (!empty($msg)) {
            dump('MSG: ' . $msg);
        }

        dump('-- END JOB --');
        return true;
    }

    private function initDump(): void
    {
        dump("-------------------------------------------------");
        dump('Investor # ' . $this->investmentBunch->investor_id);
        if (!empty($this->investmentBunch->invest_strategy_id)) {
            dump('Strategy # ' . $this->investmentBunch->invest_strategy_id);
            dump('Multi-Run = ' . $this->investmentBunch->multi_run);
        }
        dump('Bunch # ' . $this->investmentBunch->getId());
    }

    private function dumpMoneyConditions(): void
    {
        if (null === $this->handler) {
            return ;
        }

        $wallet =  $this->handler->getWallet();
        if (!empty($wallet->wallet_id)) {
            dump('Wallet.uninvested = ' . $wallet->uninvested);
        }

        $strategy =  $this->handler->getStrategy();
        if (!empty($strategy->invest_strategy_id)) {
            dump('Strategy.max_portfolio_size = ' . $strategy->max_portfolio_size
                    . ' | portfolio_size = ' . $strategy->portfolio_size);
        }
    }

    private function log(string $msg): void
    {
        dump('log(): ' . $msg);
        Log::channel('invest_all')->error($msg);
    }
}
