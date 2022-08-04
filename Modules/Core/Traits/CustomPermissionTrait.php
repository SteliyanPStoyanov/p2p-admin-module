<?php

namespace Modules\Core\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

trait CustomPermissionTrait
{
    use HasPermissions {
        HasPermissions::hasPermissionTo as parentHasPermission;
    }
    use HasRoles;

    /**
     * Override default behavior of the Spatie permission package.
     * Checking is the permission active and not soft deleted.
     *
     * @param string $permission
     * @param string|null $guardName
     *
     * @return bool
     */
    public function hasPermissionTo(string $permission, ?string $guardName = null): bool
    {
        $permissionClass = $this->getPermissionClass();
        $permission = $permissionClass->findByName(
            $permission,
            $guardName ?? $this->getDefaultGuardName()
        );
        if (!$permission->active || $permission->deleted) {
            return false;
        }

        return $this->hasDirectPermission($permission);
    }

    /**
     * @param Permission $permission
     * @param string|null $guard
     *
     * @return mixed
     */
    protected function hasDirectPermission(Permission $permission, ?string $guard = null)
    {
        return $this->permissions->contains('id', $permission->id);
    }

    /**
     * A model may have multiple roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            'role_id'
        )->select(['id', 'name', 'priority']);
    }
}
