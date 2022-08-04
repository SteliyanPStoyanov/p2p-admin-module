<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;

class TaskSeeder extends Seeder
{

    public function run()
    {

        $task = [
            [
                'task_type' => 'withdraw',
                'investor_id' => Investor::INVESTOR_ID,
                'wallet_id' => 1,
                'currency_id' => Currency::ID_EUR,
                'amount' => 500,
                'status' => 'new'
            ],
            [
                'task_type' => 'verification',
                'investor_id' => Investor::INVESTOR_ID,
                'wallet_id' => 1,
                'currency_id' => Currency::ID_EUR,
                'amount' => 500,
                'status' => 'new'
            ],
            [
                'task_type' => 'bonus_payment',
                'investor_id' => Investor::INVESTOR_ID,
                'wallet_id' => 1,
                'currency_id' => Currency::ID_EUR,
                'amount' => 500,
                'status' => 'new'
            ]
        ];


        DB::table('task')->insert($task);
    }

}
