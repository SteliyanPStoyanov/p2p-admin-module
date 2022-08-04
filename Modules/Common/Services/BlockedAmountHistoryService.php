<?php

namespace Modules\Common\Services;

use Modules\Common\Entities\Task;
use Modules\Common\Entities\Wallet;
use Modules\Common\Repositories\BlockedAmountHistoryRepository;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;

class BlockedAmountHistoryService extends BaseService
{
    protected BlockedAmountHistoryRepository $blockedAmountHistoryRepository;

    public function __construct(
        BlockedAmountHistoryRepository $blockedAmountHistoryRepository
    ) {
        $this->blockedAmountHistoryRepository = $blockedAmountHistoryRepository;

        parent::__construct();
    }

    /**
     * @param array $data
     *
     * @return \Modules\Common\Entities\BlockedAmountHistory
     */
    public function create(array $data)
    {
        return $this->blockedAmountHistoryRepository->create($data);
    }

    /**
     * @param $task
     * @param string $status
     *
     * @return mixed|\Modules\Common\Entities\BlockedAmountHistory
     *
     * @throws ProblemException
     */
    public function finalize(Task $task, string $status)
    {
        $blockedAmountHistory = $task->blockedAmountHistory;
        if (empty($blockedAmountHistory)) {
            throw new ProblemException('There is no blocked amount.');
        }

        return $this->blockedAmountHistoryRepository->finish($blockedAmountHistory, $status);
    }
}
