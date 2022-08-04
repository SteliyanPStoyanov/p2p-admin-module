<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanSeeder extends Seeder
{
    public function run()
    {
        DB::unprepared(file_get_contents(__DIR__ . '/SqlFiles/loan_202011191131.sql'));
        DB::statement("SELECT setval('loan_loan_id_seq', (SELECT MAX(loan_id) FROM loan));");
    }

}
