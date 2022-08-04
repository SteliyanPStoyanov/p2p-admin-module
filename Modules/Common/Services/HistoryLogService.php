<?php

namespace Modules\Common\Services;

use Modules\Common\Repositories\HistoryLogRepository;
use Modules\Core\Services\BaseService;

class HistoryLogService extends BaseService
{
    private HistoryLogRepository $historyLogRepository;

    public function __construct(HistoryLogRepository $historyLogRepository)
    {
        $this->historyLogRepository = $historyLogRepository;

        parent::__construct();
    }

    /**
     * @param int $length
     * @param array $data
     *
     * @return mixed
     */
    public function getByWhereConditions(
        int $length,
        array $data
    ) {
        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }

        $whereConditions = $this->getWhereConditions(
            $data,
            [
                'cron_log.command',
            ],
            'cron_log'
        );

        return $this->historyLogRepository->getAll($length, $whereConditions);
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ): array {
        $where = [];

        if (!empty($data['createdAt'])) {
                $where[] = [
                    $prefix.'.created_at',
                    '>=',
                    dbDate($data['createdAt'], '00:00:00'),
                ];

                $where[] = [
                    $prefix.'.created_at',
                    '<=',
                    dbDate($data['createdAt'], '23:59:59'),
                ];

            unset($data['createdAt']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }
}
