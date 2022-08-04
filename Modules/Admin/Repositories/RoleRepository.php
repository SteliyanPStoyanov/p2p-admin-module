<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Entities\Role;
use \Modules\Core\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    /**
     * @param int $limit
     * @param array $where
     * @param array|string[] $order
     * @param bool $showDeleted
     * @return mixed
     */
    public function getAll(
        int $limit,
        array $where = [],
        array $order = ['active' => 'DESC', 'id' => 'DESC'],
        bool $showDeleted = false
    )
    {
        $builder = Role::orderByRaw(implode(', ', $this->prepareOrderStatement($order)));

        $builder->where('priority', '<=', \Auth::user()->getMaxPriority());

        if (!empty($where)) {
            $builder->where($where);
        }

        return $builder->paginate($limit);
    }

    /**
     * @param array $data
     *
     * @return Role
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function create(array $data)
    {
        $role = new Role();
        $role->fill($data);
        $role->save();
        $role->adopt('permissions', $data['permissions']);

        return $role;
    }

    /**
     * @param Role $role
     * @param array $data
     *
     * @return Role
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function edit(Role $role, array $data)
    {
        $role->fill($data);
        $role->save();
        $role->adopt('permissions', $data['permissions']);

        return $role;
    }

    /**
     * @param Role $role
     */
    public function delete(Role $role)
    {
        $role->delete();
    }

    /**
     * @param Role $role
     */
    public function enable(Role $role)
    {
        $role->enable();
    }

    /**
     * @param Role $role
     */
    public function disable(Role $role)
    {
        $role->disable();
    }

    /**
     * @param int $id
     *
     * @return Role
     */
    public function getRoleById(int $id)
    {
        return Role::findById($id);
    }

    /**
     * @param array $roleIds
     *
     * @return int|null
     */
    public function getBiggestPriorityRole(array $roleIds): ?int
    {
        return Role::whereIn('id', $roleIds)->max('priority');
    }
}
