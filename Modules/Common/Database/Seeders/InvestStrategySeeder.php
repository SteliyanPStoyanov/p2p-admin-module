<?php

namespace Modules\Common\Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvestStrategySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $investStrategy = [];
        for ($i = 1; $i < 50; $i++) {
            $investStrategy[] = [
                'investor_id' => 103606,
                'wallet_id' => 29,
                'name' => $faker->word().$i,
                'priority' => $i,
                'min_amount' => $faker->numberBetween(1,15500),
                'max_amount' => $faker->numberBetween(1,10550),
                'min_interest_rate' => $faker->numberBetween(1,30),
                'max_interest_rate' => $faker->numberBetween(30,100),
                'min_loan_period' => $faker->numberBetween(5,15),
                'max_loan_period' => $faker->numberBetween(15,40),
                'loan_type' => json_encode(['type' => ['payday']]),
                'loan_payment_status' => json_encode(['payment_status' =>['current']]),
                'agreed' => 1,
                'portfolio_size' => $faker->numberBetween(30,100),
            ];
        }

        DB::table('invest_strategy')->insert($investStrategy);
    }

}
