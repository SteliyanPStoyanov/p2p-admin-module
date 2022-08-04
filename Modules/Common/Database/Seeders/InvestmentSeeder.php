<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Wallet;

class InvestmentSeeder extends Seeder
{
    public function run()
    {
        $investments = [];
        for ($i = 1; $i < 20; $i++) {
            $investments[] = [
                'investor_id' => Investor::INVESTOR_ID,
                'wallet_id' => Wallet::INVESTOR_WALLET_ID,
                'loan_id' => $i,
                'amount' => 30,
                'percent' => 10,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ];
        }

        DB::table('investment')->insert($investments);
    }
}
