<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;

class SystemDefaultUserSeeder extends Seeder
{

    public function run()
    {
        DB::table('administrator')->insert(
            [
                'administrator_id' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                'first_name' => 'Daemon',
                'middle_name' => '',
                'last_name' => 'Quiet',
                'phone' => '',
                'email' => '',
                'username' => 'quiet_daemon',
                'password' => '',
                'avatar' => '',
                'created_at' => now(),
            ]
        );
    }
}
