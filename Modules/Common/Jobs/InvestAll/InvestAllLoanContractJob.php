<?php

namespace Modules\Common\Jobs\InvestAll;

use \Exception;
use \Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\Investment;
use Modules\Common\Services\PDFCreatorService;

/**
 * Creates loan contracts
 */
class InvestAllLoanContractJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        SerializesModels,
        Queueable;

    private $investments = [];

    public function __construct(array $investments)
    {
        $this->investments = $investments;
    }

    public function handle()
    {
        dump('---- Loan contracts ----');
        $start = microtime(true);


        if (empty($this->investments)) {
            dump('Nothing todo, empty array of investments');
            return ;
        }


        $service = \App::make(PDFCreatorService::class);
        foreach ($this->investments as $investment) {
            $this->procced($investment, $service);
        }


        dump('Total exec.time: ' . round((microtime(true) - $start), 2) . ' sec(s)');
    }

    private function procced(Investment $investment, PDFCreatorService $service): bool
    {
        try {

            dump('Investor #' . $investment->investor_id);
            dump('Investment #' . $investment->investment_id);


            if ($service->hasContractForInvestment($investment->investment_id)) {
                dump('Nothing todo, contract is already generated');
                return true;
            }


            $transaction = $investment->getTransactionByKey();
            if (empty($transaction->transaction_id)) {
                throw new Exception("Failed to get transaction");
            }
            dump('Transaction #' . $transaction->getId());


            // add loan contract
            $result = $service->generateAssignmentAgreement(
                $investment,
                $transaction
            );
            dump('Created = ' . intval($result));
            if (!$result) {
                throw new Exception("Failed to create loan contract");
            }

            return true;

        } catch (Throwable $e) {
            $msg = 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
            Log::channel('invest_all')->error('Error (ContractsJob): ' . $msg);
            dump($msg);
        }

        return false;
    }
}
