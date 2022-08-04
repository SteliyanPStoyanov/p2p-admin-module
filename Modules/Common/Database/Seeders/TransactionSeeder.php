<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;
use Faker\Factory as Faker;
use Modules\Common\Entities\Transaction;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();


        for ($i = 1; $i < 1000; $i++) {
            $transaction[] = [
                'task_id' => $i,
                'wallet_id' => $i,
                'investor_id' => $i,
                'currency_id' => Currency::ID_EUR,
                'type' =>  $faker->randomElement(Transaction::getTypes()),
                'direction' =>  'out',
                'amount' => $faker->numberBetween(2000, 3000),
                'principal' => $faker->numberBetween(2000, 3000),
                'accrued_interest' => $faker->numberBetween(2000, 3000),
                'interest' => $faker->numberBetween(2000, 3000),
                'late_interest' => $faker->numberBetween(2000, 3000),
                'created_at' => Carbon::now()->subDays(round($i/5)),
                'created_by' => Investor::INVESTOR_ID,
            ];
        }
         for ($i = 1; $i < 1000; $i++) {
            $transaction[] = [
                'task_id' => $i,
                'wallet_id' => $i,
                'investor_id' => $i,
                'currency_id' => Currency::ID_EUR,
                'type' =>  $faker->randomElement(Transaction::getTypes()),
                'direction' =>  'in',
                'amount' => $faker->numberBetween(2000, 3000),
                'principal' => $faker->numberBetween(2000, 3000),
                'accrued_interest' => $faker->numberBetween(2000, 3000),
                'interest' => $faker->numberBetween(2000, 3000),
                'late_interest' => $faker->numberBetween(2000, 3000),
                'created_at' => Carbon::now()->subDays(round($i/5)),
                'created_by' => Investor::INVESTOR_ID,
            ];
        }
        DB::table('transaction')->insert($transaction);
    }
}

