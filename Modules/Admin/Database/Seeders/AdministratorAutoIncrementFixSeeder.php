<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdministratorAutoIncrementFixSeeder extends Seeder
{
    public function run()
    {
        DB::statement("SELECT setval('administrator_administrator_id_seq', (SELECT MAX(administrator_id) FROM administrator));");
    }
}
