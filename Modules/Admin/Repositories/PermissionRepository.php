<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Entities\Permission;
use \Modules\Core\Repositories\BaseRepository;

class PermissionRepository extends BaseRepository
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection|Permission[]
     */
    public function getAll()
    {
        $excludedMethods = config('permission.exclude_methods');
        $permissions = Permission::where(['deleted' => 0, 'active' => 1])
            ->whereNotIn('action', array_keys($excludedMethods))
            ->get();

        $permissionByGroups = [];
        foreach ($permissions as $permission) {
            $permissionByGroups[$permission->module][$permission->controller][] = $permission;
        }

        return $permissionByGroups;
    }
}
