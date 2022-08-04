<?php

namespace Modules\Common\Repositories;

use Modules\Common\Entities\BlockedAmountHistory;
use Modules\Core\Repositories\BaseRepository;

class BlockedAmountHistoryRepository extends BaseRepository
{
    /**
     * @param array $data
     *
     * @return BlockedAmountHistory
     */
    public function create(array $data)
    {
        $blockedAmountHistory = new BlockedAmountHistory();
        $blockedAmountHistory->fill($data);
        $blockedAmountHistory->save();

        return $blockedAmountHistory;
    }

    /**
     * @param BlockedAmountHistory $blockedAmountHistory
     * @param string $status
     *
     * @return mixed
     */
    public function finish(BlockedAmountHistory $blockedAmountHistory, string $status)
    {
        $blockedAmountHistory->status = $status;
        $blockedAmountHistory->save();

        return $blockedAmountHistory;
    }
}
