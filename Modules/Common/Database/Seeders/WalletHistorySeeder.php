<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;
use Faker\Factory as Faker;

class WalletHistorySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        for ($i = 1; $i < 1000; $i++) {
            $wallet_history[] = [
                'wallet_id' => 10009,
                'investor_id' => 103587,
                'currency_id' => Currency::ID_EUR,
                'date' => Carbon::now()->subDays($i),
                'total_amount' => $faker->numberBetween(2000, 3000),
                'invested' => $faker->randomElement(array(0, 1 ,55 ,100)),
                'uninvested' => $faker->numberBetween(200, 8000),
                'deposit' => $faker->numberBetween(200, 8000),
                'withdraw' => $faker->numberBetween(200, 8000),
                'income' => $faker->numberBetween(200, 8000),
                'interest' => $faker->numberBetween(200, 8000),
                'late_interest' => $faker->numberBetween(200, 8000),
                'bonus' => $faker->numberBetween(200, 8000),
                'created_at' => Carbon::now()->subDays($i),
                'created_by' => Investor::INVESTOR_ID,
            ];
        }
        DB::table('wallet_history')->insert($wallet_history);
    }
}
