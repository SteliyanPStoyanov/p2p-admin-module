<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Agreement;


class NewDeviceAgreementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('agreement')->insert(
            [
                [
                    'type' => Agreement::TYPE_NOTIFICATION,
                    'name' => 'Login from new IP/Device notification',
                    'description' => 'Login from new IP/Device notification',
                    'active' => 1,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ],
            ]
        );
    }

}
