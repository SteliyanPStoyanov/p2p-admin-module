<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\InvestorLoginLog;
use Modules\Core\Repositories\BaseRepository;

class InvestorLoginLogRepository extends BaseRepository
{

    /**
     * @param array $data
     *
     * @return InvestorLoginLog
     */
    public function create(array $data)
    {
        $investorLoginLog = new InvestorLoginLog();
        $investorLoginLog->fill($data);
        $investorLoginLog->save();

        return $investorLoginLog;
    }

    /**
     * @param int $investorId
     * @param string $ipAddress
     *
     * @return bool
     */
    public function isExists(int $investorId, string $ipAddress)
    {
        return (InvestorLoginLog::where(
                [
                    'investor_id' => $investorId,
                    'ip' => $ipAddress,
                ]
            )->count()) > 0;
    }

    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['investor_login_log_id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $builder = DB::table('investor_login_log');
        $builder->select(
            DB::raw(
                '
            investor_login_log.*
            '
            )
        );

        if (!empty($where)) {
            $builder->where($where);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $result = $builder->paginate($limit);
        $records = InvestorLoginLog::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    public function getById(int $investorLoginLogId)
    {
        return InvestorLoginLog::where(
            'investor_login_log_id',
            '=',
            $investorLoginLogId
        )->first();
    }

    public function delete(InvestorLoginLog $investorLoginLog)
    {
        $investorLoginLog->delete();
    }
}
