<?php

namespace Modules\Communication\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Communication\Entities\EmailTemplate;
use Illuminate\Support\Arr;

class EmailTemplateSeeder extends Seeder
{
    public function run()
    {
        DB::unprepared(file_get_contents(__DIR__ . '/EmailTemplatesFiles/email-template.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/EmailTemplatesFiles/email-verification-rejected.sql'));
        DB::unprepared(file_get_contents(__DIR__ . '/EmailTemplatesFiles/withdrawal-processed-template.sql'));
        DB::statement(
            "SELECT setval('email_template_email_template_id_seq', (SELECT MAX(email_template_id) FROM email_template));"
        );
    }
}

