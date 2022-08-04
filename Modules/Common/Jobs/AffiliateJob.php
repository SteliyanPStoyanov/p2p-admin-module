<?php

namespace Modules\Common\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Modules\Common\Entities\Affiliate;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Transaction;
use Throwable;

class AffiliateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Transaction $transaction;
    protected Investor $investor;

    public int $timeout = 50;

    /**
     * AffiliateJob constructor.
     * @param Transaction $transaction
     * @param Investor $investor
     */
    public function __construct(
        Transaction $transaction,
        Investor $investor
    ) {
        $this->transaction = $transaction;
        $this->investor = $investor;
    }

    public function handle()
    {
        try {
            $affiliateDescription = json_decode($this->investor->getAffiliateDescription());

            if ($this->investor->isActiveAffiliate() === true) {
                if (!array_key_exists($affiliateDescription->utm_source, Affiliate::AFFILIATE_SOURCE)) {
                    throw new Exception('This affiliate source not exists !');
                }

                $affiliateClassName = Affiliate::AFFILIATE_SOURCE[$affiliateDescription->utm_source];

                if ($this->transaction->type === Transaction::TYPE_DEPOSIT) {
                    self::isDepositDone($affiliateClassName, $affiliateDescription);
                }

                if ($this->transaction->type === Transaction::TYPE_INVESTMENT) {
                    self::isInvestmentDone($affiliateClassName, $affiliateDescription);
                }
            }
        } catch (Throwable $e) {
            Log::channel('affiliate')->error(
                'Failed to save change log. ' . $e->getMessage()
            );
        }
    }

    /**
     * @param $affiliateClassName
     * @param $affiliateDescription
     */
    public function isDepositDone($affiliateClassName, $affiliateDescription)
    {
        $affiliateClassName = new $affiliateClassName((array)$affiliateDescription);

        $affiliateClassName->sendDepositPost($this->transaction);
    }

    /**
     * @param $affiliateClassName
     * @param $affiliateDescription
     */
    public function isInvestmentDone($affiliateClassName, $affiliateDescription)
    {
        $affiliateClassName = new $affiliateClassName((array)$affiliateDescription);

        $affiliateClassName->sendInvestmentPost($this->transaction);
    }

    /**
     * @param Throwable $exception
     */
    public function failed(Throwable $exception)
    {
        Log::channel('affiliate')->error(
            'Failed to save change log. ' . $exception->getMessage()
        );
    }
}
