<?php

namespace Modules\Common\Jobs\InvestAll;

use \Exception;
use \Throwable;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\Investment;
use Modules\Common\Services\InvestService;

/**
 * Creates investor installments
 */
class InvestAllPlansJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        SerializesModels,
        Queueable;

    private $investments = [];
    private $date = null;

    public function __construct(array $investments, string $date = null)
    {
        $this->investments = $investments;
        $this->date = (is_null($date) ? Carbon::now() : Carbon::parse($date));
    }

    public function handle()
    {
        dump('---- Investor plans ----');
        $start = microtime(true);


        if (empty($this->investments)) {
            dump('Nothing todo, empty array of investments');
            return ;
        }


        $service = \App::make(InvestService::class);
        foreach ($this->investments as $investment) {
            $this->procced($investment, $service);
        }


        dump('Total exec.time: ' . round((microtime(true) - $start), 2) . ' sec(s)');
    }

    private function procced(Investment $investment, InvestService $service): bool
    {
        try {

            dump('Investor #' . $investment->investor_id);
            dump('Investment #' . $investment->investment_id);
            dump('Loan #' . $investment->loan_id . ', amount = ' . $investment->amount);


            if ($service->hasPlanForInvestment($investment->investment_id)) {
                dump('Nothing todo, plan is already generated'); // proceed only newly created investments. Don't touch existing once
                return true;
            }


            // get loan
            $loan = $investment->loan()->first();
            if (empty($loan->loan_id)) {
                throw new Exception("Failed to get loan");
            }


            if ($loan->unlisted == 1) {
                dump('Nothing todo, loan is repaid');
                return true;
            }


            // get loan installments
            $installments = $loan->installments();
            if (empty($installments)) {
                throw new Exception("Failed to get installments, loan #" . $loan->getId());
            }


            // prepare investor installments
            $import = $service->prepareInvestorInstallments(
                $loan,
                $investment,
                $installments,
                $this->date
            );
            if (empty($import)) {
                throw new Exception("Failed to prepare investor installments");
            }
            dump('Installments count = ' . count($import));


            // add investor installments
            $result = $service->createInvestorInstallments($import);
            dump('Created = ' . intval($result));
            if (!$result) {
                throw new Exception("Failed to create investor installments");
            }

            return true;

        } catch (Throwable $e) {
            $msg = 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();

            Log::channel('invest_all')->error(
                'Error (PlansJob, investment #'
                . $investment->investment_id . '): ' . $msg
            );

            dump($msg);
        }

        return false;
    }
}
