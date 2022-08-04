<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use DB;
use Modules\Common\Entities\CronLog;
use Modules\Core\Repositories\BaseRepository;


class HistoryLogRepository extends BaseRepository
{
    protected CronLog  $cronLog;

    public function __construct(CronLog $cronLog)
    {
        $this->cronLog = $cronLog;
    }

    /**
     * @param int $limit
     * @param array $where
     * @param array|string[] $order
     *
     * @return mixed
     */
    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['cron_log_id' => 'DESC', 'created_at' => 'ASC']
    ) {
        $builder = DB::table('cron_log');
        $builder->select(
            DB::raw('cron_log.*')
        );

        if (!empty($where)) {
            $builder->where($where);
        }

        $builder->where('cron_log.manual_run', 0);
        $builder->where('cron_log.command', '!=', 'ResumeAutoInvest');
        $builder->where('cron_log.command', '!=', 'AutoInvestOnDeposit');

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $result = $builder->paginate($limit);
        $records = CronLog::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }
}
