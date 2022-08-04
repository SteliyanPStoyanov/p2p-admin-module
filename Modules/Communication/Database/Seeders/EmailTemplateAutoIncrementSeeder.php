<?php

namespace Modules\Communication\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateAutoIncrementSeeder extends Seeder
{
    public function run()
    {
        DB::statement("SELECT setval('email_template_email_template_id_seq', (SELECT MAX(email_template_id) FROM email_template)+1);");
    }
}
