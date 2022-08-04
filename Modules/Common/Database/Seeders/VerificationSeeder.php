<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Faker\Factory as Faker;

class VerificationSeeder extends Seeder
{
    public function run()
    {
        //More fake data so we can list them and test. TODO: remove
        $faker = Faker::create();
        $investors = [];
        for ($i = 1; $i < 1000; $i++) {
            $investors[] = [
                'investor_id' => $i,
                'comment' => 1,
                'birth_date' => 1,
                'name' => 1,
                'address' => 1,
                'citizenship' => 1,
                'created_at' => Carbon::now()->subDays(round($i / 5)),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ];
        }

        DB::table('verification')->insert($investors);
    }

}
