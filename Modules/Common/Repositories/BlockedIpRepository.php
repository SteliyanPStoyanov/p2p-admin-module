<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\BlockedIp;
use Modules\Common\Entities\LoginAttempt;
use Modules\Core\Repositories\BaseRepository;

class BlockedIpRepository extends BaseRepository
{

    /**
     * @param array $data
     *
     * @return BlockedIp
     */
    public function create(array $data)
    {
        $blockedIp = new BlockedIp();
        $blockedIp->fill($data);
        $blockedIp->save();

        return $blockedIp;
    }

    /**
     * @param string $ip
     * @param string $reason
     *
     * @return mixed
     */
    public function get(string $ip, string $reason)
    {
        $ip = BlockedIp::where(
            [
                ['ip', '=', $ip],
                ['reason', '=', $reason],
                ['deleted', '=', 0],
            ]
        )->get();

        return $ip->last();
    }

    /**
     * @param int $limit
     * @param array $where
     * @param array|string[] $order
     * @param bool $showDeleted
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['blocked_ip.id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $builder = DB::table('blocked_ip');
        $builder->select(
            DB::raw(
                '
            blocked_ip.ip,
            blocked_ip.id,
            blocked_ip.reason,
            login_attempt.email,
            blocked_ip.active
            '
            )
        );

        $builder->leftJoin('login_attempt', 'login_attempt.ip', '=', 'blocked_ip.ip');
        $builder->groupBy('blocked_ip.id', 'blocked_ip.ip', 'login_attempt.email');


        if (!empty($where)) {
            $builder->where($where);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $result = $builder->paginate($limit);
        $records = BlockedIp::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * @param int $blockedId
     *
     * @return mixed
     */
    public function getById(int $blockedId)
    {
        return BlockedIp::where(
            'id',
            '=',
            $blockedId
        )->first();
    }

    public function deleteAll()
    {
        BlockedIp::query()->update(
            [
                'deleted' => 1,
                'deleted_at' => Carbon::now(),
                'deleted_by' => Auth::user()->administrator_id,
                'blocked_till' => null,
                'created_at' => null,
                'active' => 0,
            ]
        );
    }

    /**
     * @param BlockedIp $blockedIp
     */
    public function delete(BlockedIp $blockedIp)
    {
        BlockedIp::where('ip', '=', $blockedIp->ip)->update(
            [
                'deleted' => 1,
                'deleted_at' => Carbon::now(),
                'deleted_by' => Auth::user()->administrator_id,
                'blocked_till' => null,
                'created_at' => null,
                'active' => 0
            ]
        );

        LoginAttempt::where('ip', '=', $blockedIp->ip)->update(
            [
                'active' => 0,
                'deleted' => 1,
                'deleted_at' => Carbon::now(),
                'deleted_by' => Auth::user()->administrator_id,
            ]
        );
    }
}
