<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;

class UnitDefaultUserSeeder extends Seeder
{

    public function run()
    {
        DB::table('administrator')->insert(
            [
                'administrator_id' => Administrator::DEFAULT_UNIT_TEST_USER_ID,
                'first_name' => 'Unit',
                'middle_name' => 'Test',
                'last_name' => 'User',
                'phone' => '',
                'email' => 'unit_test_user@stick.bg',
                'username' => 'unit_test_user',
                'password' => '',
                'avatar' => '',
                'created_at' => now(),
            ]
        );
    }
}
