<?php

namespace Modules\Common\Observers;

use Modules\Common\Entities\Wallet;

class WalletObserver
{
    /**
     * @param Wallet $baseModel
     *
     * @return void
     */
    public function updating($baseModel)
    {
        $baseModel->total_amount = $baseModel->deposit - $baseModel->withdraw + $baseModel->income;
    }
}
