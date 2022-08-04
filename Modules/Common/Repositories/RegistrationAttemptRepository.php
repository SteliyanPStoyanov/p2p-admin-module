<?php

namespace Modules\Common\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\RegistrationAttempt;
use Modules\Core\Repositories\BaseRepository;

class RegistrationAttemptRepository extends BaseRepository
{
    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $builder = DB::table('registration_attempt');
        $builder->select(
            DB::raw(
                '
            registration_attempt.*
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
        $records = RegistrationAttempt::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * @param array $data
     *
     * @return RegistrationAttempt
     */
    public function create(array $data)
    {
        $registrationAttempt = new RegistrationAttempt();
        $registrationAttempt->fill($data);
        $registrationAttempt->save();

        return $registrationAttempt;
    }

    public function getById(int $registrationAttemptId)
    {
        return RegistrationAttempt::where(
            'id',
            '=',
            $registrationAttemptId
        )->first();
    }

    public function delete(RegistrationAttempt $registrationAttempt)
    {
        $registrationAttempt->delete();
    }

    public function deleteAll()
    {
        RegistrationAttempt::query()->update(
            [
                'deleted' => 1,
                'active' => 0,
                'deleted_at' => Carbon::now(),
                'deleted_by' => Auth::user()->administrator_id,
            ]
        );
    }
}
