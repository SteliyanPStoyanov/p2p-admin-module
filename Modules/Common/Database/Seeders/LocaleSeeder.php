<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locale')->insert(
            [
                'name' => 'English',
                'code' => 'en',
                'created_at' => Carbon::now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ]
        );
    }
}
