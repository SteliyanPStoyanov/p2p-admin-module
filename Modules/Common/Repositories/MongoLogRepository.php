<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use DB;
use Modules\Common\Entities\CronLog;
use Modules\Core\Models\BaseLoggerModel;
use Modules\Core\Repositories\BaseRepository;


class MongoLogRepository extends BaseRepository
{
    protected CronLog  $cronLog;

    public function __construct(CronLog $cronLog)
    {
        $this->cronLog = $cronLog;
    }

    /**
     * @param int $limit
     * @param BaseLoggerModel $loggerModel
     * @param array $where
     * @param array|string[] $order
     *
     * @return mixed
     */
    public function getAll(
        int $limit,
        BaseLoggerModel $loggerModel,
        array $where = []
    ) {
        return $loggerModel::orderBy('_id', 'DESC')->where($where)->paginate($limit);
    }

    /**
     * @param BaseLoggerModel $adapter
     * @param $id
     *
     * @return mixed
     */
    public function getById(BaseLoggerModel $adapter, $id)
    {
        return $adapter::find($id);
    }

    /**
     * @param $mongoLog
     *
     * @return mixed
     */
    public function delete($mongoLog)
    {
        return $mongoLog->delete();
    }

    /**
     * @param BaseLoggerModel $adapter
     *
     * @return mixed
     */
    public function deleteAll(BaseLoggerModel $adapter)
    {
        return $adapter::truncate();
    }
}
