<?php

namespace Modules\Admin\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Core\Repositories\BaseRepository;

class AdministratorRepository extends BaseRepository
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
        array $order = ['active' => 'DESC', 'administrator_id' => 'DESC'],
        bool $showDeleted = false
    ) {
        $where = $this->checkForDeleted($where, $showDeleted, 'administrator');

        $builder = DB::table('administrator');
        $builder->select(
            DB::raw(
                '
            distinct administrator.administrator_id,
            administrator.*,
            string_agg(a.first_name, a.last_name) AS creator_names  ,
            string_agg(ad.first_name, ad.last_name) AS updater_names
            '
            )
        );
        $builder->leftJoin(
            'administrator AS a',
            'administrator.created_by',
            '=',
            'a.administrator_id'
        );
        $builder->leftJoin(
            'administrator AS ad',
            'administrator.updated_by',
            '=',
            'ad.administrator_id'
        );

        if (!empty($where)) {
            $builder->where($where);
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $builder->orderBy($key, $direction);
            }
        }

        $builder->groupBy('administrator.administrator_id');

        $result = $builder->paginate($limit);
        $records = Administrator::hydrate($result->all());
        $result->setCollection($records);

        return $result;
    }

    /**
     * [getById description]
     *
     * @param int $administratorId [description]
     *
     * @return Administrator|null
     */
    public function getById(int $administratorId)
    {
        return Administrator::where(
            'administrator_id',
            '=',
            $administratorId
        )->first();
    }

    /**
     * @param Administrator $administrator
     *
     * @throws \Exception
     */
    public function delete(Administrator $administrator)
    {
        $administrator->delete();
    }

    /**
     * @param Administrator $administrator
     */
    public function disable(Administrator $administrator)
    {
        $administrator->disable();
    }

    /**
     * @param Administrator $administrator
     */
    public function enable(Administrator $administrator)
    {
        $administrator->enable();
    }

    /**
     * @param array $data
     *
     * @return Administrator
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function create(array $data)
    {
        $administrator = new Administrator();
        $administrator->fill($data);
        $administrator->save();

        if (!empty($data['roles'])) {
            $administrator->adopt('roles', $data['roles']);
        }
        if (!empty($data['permissions'])) {
            $administrator->adopt('permissions', $data['permissions']);
        }

        return $administrator;
    }

    /**
     * @param Administrator $administrator
     * @param array $data
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function update(Administrator $administrator, array $data)
    {
        $administrator->fill($data);
        $administrator->save();
        if (!empty($data['roles'])) {
            $administrator->adopt('roles', $data['roles']);
        }
        if (!empty($data['permissions'])) {
            $administrator->adopt('permissions', $data['permissions']);
        }
    }

    /**
     * @param string $username
     *
     * @return Administrator
     */
    public function getAdministratorByUsername(string $username)
    {
        return Administrator::firstWhere('username', $username);
    }


    /**
     * @param int $investorId
     * @param array $data
     *
     * @return mixed
     */
    public function administratorUpdate(int $administratorId, array $data)
    {
        return Administrator::where('administrator_id', '=', $administratorId)->update($data);
    }

}
