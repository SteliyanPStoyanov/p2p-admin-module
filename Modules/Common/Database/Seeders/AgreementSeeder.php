<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Agreement;


class AgreementSeeder extends Seeder
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
                    'type' => Agreement::TYPE_REGISTRATION,
                    'name' => 'User agreement',
                    'description' => 'User agreement',
                    'active' => 1,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ],[
                    'type' => Agreement::TYPE_REGISTRATION,
                    'name' => 'Receiving marketing communication',
                    'description' => 'Receiving marketing communication',
                    'active' => 1,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ],[
                    'type' => Agreement::TYPE_NOTIFICATION,
                    'name' => 'Notification for funds',
                    'description' => 'Notification for funds',
                    'active' => 1,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ],[
                    'type' => Agreement::TYPE_NOTIFICATION,
                    'name' => 'Withdraw request notification',
                    'description' => 'Withdraw request notification',
                    'active' => 1,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ],
            ]
        );
    }

}
