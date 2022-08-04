<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Dompdf\Renderer\Block;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\BlockedIp;
use Modules\Common\Entities\LoginAttempt;
use Modules\Core\Repositories\BaseRepository;

class LoginAttemptRepository extends BaseRepository
{
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
        array $order = ['id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $builder = DB::table('login_attempt');
        $builder->select(
            DB::raw(
                '
            login_attempt.*
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
        $records = LoginAttempt::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * @param array $data
     *
     * @return LoginAttempt
     */
    public function create(array $data)
    {
        $loginAttempt = new LoginAttempt();
        $loginAttempt->fill($data);
        $loginAttempt->save();

        return $loginAttempt;
    }

    /**
     * @param int $loginAttemptId
     *
     * @return mixed
     */
    public function getById(int $loginAttemptId)
    {
        return LoginAttempt::where(
            'id',
            '=',
            $loginAttemptId
        )->first();
    }

    /**
     * @param LoginAttempt $loginAttempt
     *
     * @throws \Exception
     */
    public function delete(LoginAttempt $loginAttempt)
    {
        $loginAttempt->delete();
    }

    public function deleteAll()
    {
        LoginAttempt::query()->update(
            [
                'active' => 0,
                'deleted' => 1,
                'deleted_at' => Carbon::now(),
                'deleted_by' => Auth::user()->administrator_id,
            ]
        );
    }
}
