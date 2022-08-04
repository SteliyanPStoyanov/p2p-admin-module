<?php

namespace Modules\Common\Observers;

use Carbon\Carbon;
use Modules\Common\Entities\Transaction;
use Modules\Common\Jobs\AffiliateJob;

class TransactionObserver
{
    const QUEUE_NAME = 'affiliate';

    public function created(Transaction $transaction)
    {
        $types = [
            Transaction::TYPE_DEPOSIT,
            Transaction::TYPE_INVESTMENT,
        ];

        if (
            in_array($transaction->type, $types)
            && $transaction->investor->isActiveAffiliate()
        ) {
            AffiliateJob::dispatch($transaction , $transaction->investor)
                ->onQueue(self::QUEUE_NAME)
                ->delay(Carbon::now()->addSeconds(30));
        }
    }
}
