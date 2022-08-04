<?php

namespace Modules\Admin\Database\Seeders;

use Artisan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Entities\Guard;
use Modules\Admin\Entities\Permission;
use Modules\Admin\Entities\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // insert role
        $id = DB::table('role')->insertGetId(
            [
                'name' => 'Super Admin',
                'guard_name' => Guard::DEFAULT_GUARD_NAME,
                'priority' => 100,
                'active' => 1,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ]
        );

        // add permissions
        Artisan::call('script:permissions:register');

        // add all permissions to the role
        $role = Role::findById($id);
        $role->permissions()->sync(Permission::all());
        AdministratorSeeder::assignAllPermissions($role);
    }
}
