<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstallmentSeeder extends Seeder
{
    public function run()
    {
        DB::unprepared(file_get_contents(__DIR__ . '/SqlFiles/installment_202011191130.sql'));
        DB::statement("SELECT setval('installment_installment_id_seq', (SELECT MAX(installment_id) FROM installment));");
    }

}
