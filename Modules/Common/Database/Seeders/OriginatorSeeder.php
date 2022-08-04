<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Originator;

class OriginatorSeeder extends Seeder
{
    public function run()
    {
        DB::table('originator')->insert(
            [
                'originator_id' => Originator::ID_ORIG_STIKCREDIT,
                'name' => Originator::NAME_ORIG_STIKCREDIT,
                'description' => Originator::NAME_ORIG_STIKCREDIT,
                'phone' => Originator::PHONE_ORIG_STIKCREDIT,
                'email' => Originator::EMAIL_ORIG_STIKCREDIT,
                'website' => Originator::WEBSITE_ORIG_STIKCREDIT,
                'iban' => Originator::IBAN_ORIG_STIKCREDIT,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ]
        );
    }
}
