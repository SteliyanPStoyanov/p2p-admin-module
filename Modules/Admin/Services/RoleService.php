<?php

namespace Modules\Admin\Services;

use Modules\Admin\Entities\Administrator;
use Modules\Admin\Entities\Role;
use Modules\Admin\Repositories\RoleRepository;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use \Modules\Core\Services\BaseService;

class RoleService extends BaseService
{
    protected RoleRepository $roleRepository;

    /**
     * RoleService constructor.
     *
     * @param RoleRepository $roleRepository
     */
    public function __construct(
        RoleRepository $roleRepository
    ) {
        $this->roleRepository = $roleRepository;

        parent::__construct();
    }

    /**
     * @param int $priority
     *
     * @return \Modules\Admin\Entities\Role[]
     */
    public function getAll(int $priority = Role::PRIORITY_MAX)
    {
        return $this->roleRepository->getAll($priority);
    }

    /**
     * @param array $data
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function create(array $data)
    {
        return $this->roleRepository->create($data);
    }

    /**
     * @param int $roleId
     * @param array $data
     * @param Administrator $administrator
     *
     * @return mixed
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function edit(int $roleId, array $data, Administrator $administrator)
    {
        $role = $this->getRoleById($roleId);
        $this->canManageRole($administrator, $role);

        return $this->roleRepository->edit($role, $data);
    }

    /**
     * @param int $roleId
     * @param Administrator $administrator
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function enable(int $roleId, Administrator $administrator)
    {
        $role = $this->getRoleById($roleId);
        $this->canManageRole($administrator, $role);

        if ($role->isActive()) {
            throw new ProblemException(__('roleCrud.roleEnable'));
        }

        $this->roleRepository->enable($role);

        return true;
    }

    /**
     * @param int $roleId
     * @param Administrator $administrator
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function disable(int $roleId, Administrator $administrator)
    {
        $role = $this->getRoleById($roleId);
        $this->canManageRole($administrator, $role);
        if (!$role->isActive()) {
            throw new ProblemException(__('roleCrud.roleDisable'));
        }

        $this->roleRepository->disable($role);

        return true;
    }

    /**
     * @param int $roleId
     * @param Administrator $administrator
     *
     * @return bool
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function delete(int $roleId, Administrator $administrator)
    {
        $role = $this->getRoleById($roleId);
        $this->canManageRole($administrator, $role);

        $this->roleRepository->delete($role);

        return true;
    }

    /**
     * @param int $roleId
     *
     * @return Role
     *
     * @throws NotFoundException
     */
    public function getRoleById(int $roleId)
    {
        $role = $this->roleRepository->getRoleById($roleId);
        if (!$role || $role->isDeleted()) {
            throw new NotFoundException(__('roleCrud.roleNotFound'));
        }

        return $role;
    }

    /**
     * @param Administrator $administrator
     * @param Role $role
     * @param bool $throwException
     *
     * @return bool
     *
     * @throws ProblemException
     */
    public function canManageRole(
        Administrator $administrator,
        Role $role,
        bool $throwException = true
    ) {
        $result = ($administrator->getMaxPriority() >= $role->priority);

        if (!$result && $throwException) {
            throw new ProblemException('Access denied');
        }

        return $result;
    }

    /**
     * @param Administrator $administrator
     * @param array $roleIds
     * @param bool $throwException
     *
     * @return bool
     *
     * @throws ProblemException
     */
    public function canManageRoles(
        Administrator $administrator,
        array $roleIds,
        bool $throwException = true
    ) {
        $result = ($administrator->getMaxPriority() >=
            $this->roleRepository->getBiggestPriorityRole($roleIds));

        if (!$result && $throwException) {
            throw new ProblemException('Access denied');
        }

        return $result;
    }

    /**
     * @param int $limit
     * @param array $data
     *
     * @return mixed
     */
    public function getByFilters(int $limit, array $data)
    {
        $whereConditions = $this->getWhereConditions($data);

        return $this->roleRepository->getAll(
            $limit,
            $whereConditions
        );
    }


    /**
     * @param array $roles
     * @return mixed
     */
    public function getPermissionsByRole($roles)
    {
        foreach ($roles as $role){
           foreach ($role->permissions as $permission){
              $groups[$role->name][$permission->controller][] = $permission;
           }
        }

        return $groups;
    }
}

