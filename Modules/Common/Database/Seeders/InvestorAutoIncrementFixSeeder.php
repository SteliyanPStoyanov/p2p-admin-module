<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvestorAutoIncrementFixSeeder extends Seeder
{
    public function run()
    {
        DB::statement("SELECT setval('investor_investor_id_seq', (SELECT MAX(investor_id) FROM investor));");
    }
}
