<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Entities\Permission;
use Modules\Admin\Entities\Role;

class AdministratorSeeder extends Seeder
{
    public function run()
    {
        DB::table('administrator')->insert(
            [
                'administrator_id' => Administrator::DEFAULT_ADMINISTRATOR_ID,
                'first_name' => 'Super',
                'middle_name' => 'Mega',
                'last_name' => 'Admin',
                'phone' => '0888888888',
                'email' => 'admin@stikcredit.bg',
                'username' => 'admin',
                'password' => '$2y$10$iLX8SP0ljoM8QUyPvxXFA.Oa6OKUsQWkWys6mGR0NCQJA7ys9xnIK',
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ]
        );
    }

    public static function assignAllPermissions(Role $role)
    {
        $administrator = Administrator::find(Administrator::DEFAULT_ADMINISTRATOR_ID);
        $administrator->assignRole($role);
        $administrator->permissions()->sync(Permission::all());
    }
}
