<?php

namespace Modules\Common\Services;

use Modules\Common\Repositories\HistoryLogRepository;
use Modules\Common\Repositories\MongoLogRepository;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Models\BaseLoggerModel;
use Modules\Core\Models\InvestorLogger;
use Modules\Core\Models\PivotLogger;
use Modules\Core\Models\SystemLogger;
use Modules\Core\Services\BaseService;

class MongoLogService extends BaseService
{
    protected const ADAPTERS = [
        'investor_logger' => InvestorLogger::class,
        'system_logger' => SystemLogger::class,
        'pivot_logger' => PivotLogger::class,
    ];

    protected MongoLogRepository $mongoLogRepository;

    public function __construct(MongoLogRepository $mongoLogRepository)
    {
        $this->mongoLogRepository = $mongoLogRepository;

        parent::__construct();
    }

    /**
     * @param int $length
     * @param array $data
     * @param string $adapterKey
     *
     * @return mixed
     */
    public function getByWhereConditions(
        int $length,
        array $data,
        string $adapterKey
    ) {
        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }

        $where = [];
        if (!empty($data['table'])) {
            $where[] = [
                'table',
                'like',
                '%' . $data['table'] . '%',
            ];
        }

        if (!empty($data['action'])) {
            $where[] = [
                'action',
                '=',
                $data['action'],
            ];
        }

        if (!empty($data['investor_id'])) {
            $where[] = [
                'investor_id',
                '=',
                intval($data['investor_id']),
            ];
        }

        if (!empty($data['loan_id'])) {
            $where[] = [
                'loan_id',
                '=',
                intval($data['loan_id']),
            ];
        }

        if (!empty($data['created_at']) && preg_match(self::$dateRangeRegex, $data['created_at'])) {
            $dates = $this->extractDates($data['created_at']);
            $where[] = [
                'created_at',
                '>',
                new \DateTime($dates['from']),
            ];
            $where[] = [
                'created_at',
                '<',
                new \DateTime($dates['to']),
            ];
        }

        return $this->mongoLogRepository->getAll($length, $this->getAdapter($adapterKey), $where);
    }

    /**
     * @param string $adapterKey
     *
     * @return BaseLoggerModel
     */
    protected function getAdapter(string $adapterKey): BaseLoggerModel
    {
        if (!array_key_exists($adapterKey, self::ADAPTERS)) {
            return new SystemLogger();
        }

        $adapterClass = self::ADAPTERS[$adapterKey];

        return new $adapterClass();
    }

    public function delete($adapterKey, $id)
    {
        if ($id == 0) {
            return $this->mongoLogRepository->deleteAll($this->getAdapter($adapterKey));
        }

        $mongoLog = $this->getById($adapterKey, $id);

        return $this->mongoLogRepository->delete($mongoLog);
    }

    protected function getById($adapterKey, $id)
    {
        $mongoLog = $this->mongoLogRepository->getById($this->getAdapter($adapterKey), $id);
        if (empty($mongoLog)) {
            throw new ProblemException(__('common.MongoLogNotFound'));
        }

        return $mongoLog;
    }
}
