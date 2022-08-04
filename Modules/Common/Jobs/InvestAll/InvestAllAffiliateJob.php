<?php

namespace Modules\Common\Jobs\InvestAll;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Common\Affiliates\DoAffiliate;
use Modules\Common\Entities\Affiliate;
use Modules\Common\Entities\Investment;
use \Exception;
use \Throwable;

class InvestAllAffiliateJob implements ShouldQueue
{
    use Dispatchable,
        InteractsWithQueue,
        SerializesModels,
        Queueable;

    private array $investments = [];
    private $affiliateDescription = null;

    /**
     * InvestAllAffiliateJob constructor.
     * @param array $investments
     */
    public function __construct(array $investments)
    {
        $this->investments = $investments;
    }

    public function handle()
    {
        dump('---- Affiliate Investment Post Send ----');
        $start = microtime(true);

        if (empty($this->investments)) {
            dump('Nothing todo, empty array of investments');
            return;
        }

        foreach ($this->investments as $investment) {
            $this->proceed($investment);
        }

        dump('Total exec.time: ' . round((microtime(true) - $start), 2) . ' sec(s)');
    }

    /**
     * @param Investment $investment
     * @return bool
     */
    private function proceed(Investment $investment): bool
    {
        try {

            $transaction = $investment->getTransactionByKey();
            if (empty($transaction->transaction_id)) {
                throw new Exception("Failed to get transaction");
            }
            dump('Transaction #' . $transaction->getId());


            $affiliateDescription = $this->getDesc($transaction);
            if (!array_key_exists($affiliateDescription->utm_source, Affiliate::AFFILIATE_SOURCE)) {
                throw new Exception('This affiliate source not exists !');
            }


            $affiliateClassName = Affiliate::AFFILIATE_SOURCE[$affiliateDescription->utm_source];
            $affiliateClassName = new $affiliateClassName((array)$affiliateDescription);
            $affiliateClassName->sendInvestmentPost($transaction);

            return true;

        } catch (Throwable $e) {
            $msg = 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
            Log::channel('affiliate')->error('Error (affiliate): ' . $msg);
            dump($msg);
        }

        return false;
    }

    /**
     * Since the investments/transactions here always come for 1 investor
     * We get affiliate desctiption only once
     */
    private function getDesc($transaction)
    {
        if (null === $this->affiliateDescription) {
            $this->affiliateDescription = json_decode(
                $transaction->investor->getAffiliateDescription()
            );
        }

        return $this->affiliateDescription;
    }
}
