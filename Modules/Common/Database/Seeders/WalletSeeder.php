<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;
use Faker\Factory as Faker;

class WalletSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        DB::table('wallet')->insert(
            [
                'investor_id' => Investor::INVESTOR_ID,
                'currency_id' => Currency::ID_EUR,
                'date' => now(),
                'total_amount' => $faker->numberBetween(2000, 3000),
                'invested' => $faker->numberBetween(1000, 1500),
                'uninvested' => $faker->numberBetween(855, 3000),
                'deposit' => $faker->numberBetween(1000, 2000),
                'withdraw' => $faker->numberBetween(200, 600),
                'income' => $faker->numberBetween(200, 600),
                'interest' => $faker->numberBetween(200, 600),
                'late_interest' => $faker->numberBetween(200, 600),
                'bonus' => $faker->numberBetween(200, 600),
                'created_at' => now(),
                'created_by' => Investor::INVESTOR_ID,
            ]
        );

          for ($i = 2; $i < 1000; $i++) {
               $wallet[] = [
                'investor_id' => $i,
                'currency_id' => Currency::ID_EUR,
                'date' => now(),
                'total_amount' => $faker->numberBetween(2000, 3000),
                'invested' => $faker->numberBetween(1000, 1500),
                'uninvested' => $faker->numberBetween(1000, 1500),
                'deposit' => $faker->numberBetween(1000, 2000),
                'withdraw' => $faker->numberBetween(200, 600),
                'income' => $faker->numberBetween(200, 600),
                'interest' => $faker->numberBetween(200, 600),
                'late_interest' => $faker->numberBetween(200, 600),
                'bonus' => $faker->numberBetween(200, 600),
                'created_at' => Carbon::now()->subDays(round($i/5)),
                'created_by' => Investor::INVESTOR_ID,
            ];
          }
           DB::table('wallet')->insert($wallet);
    }
}
