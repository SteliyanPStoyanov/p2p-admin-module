<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Investor;
use Faker\Factory as Faker;

class BankAccountSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $bankAccounts = [];
        for ($i = 1; $i <= 11; $i++) {
            $default = 1;

            $bankAccounts[] = [
                'investor_id' => $i,
                'iban' => $faker->iban(),
                'default' => $default,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ];
        }

        DB::table('bank_account')->insert($bankAccounts);
    }

}
