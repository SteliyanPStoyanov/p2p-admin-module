<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StartedIdsSeeder extends Seeder
{
    public function run()
    {
    	DB::update("ALTER SEQUENCE investor_investor_id_seq RESTART WITH 103578");
    	DB::update("ALTER SEQUENCE loan_loan_id_seq RESTART WITH 234120");
    	DB::update("ALTER SEQUENCE wallet_wallet_id_seq RESTART WITH 10000");
    	DB::update("ALTER SEQUENCE transaction_transaction_id_seq RESTART WITH 10000");

        DB::statement("SELECT setval('loan_loan_id_seq', (SELECT MAX(loan_id) FROM loan));");
        DB::statement("SELECT setval('investor_investor_id_seq', (SELECT MAX(investor_id) FROM investor));");
        DB::statement("SELECT setval('wallet_wallet_id_seq', (SELECT MAX(wallet_id) FROM wallet));");
        DB::statement("SELECT setval('transaction_transaction_id_seq', (SELECT MAX(transaction_id) FROM transaction));");
    }
}
