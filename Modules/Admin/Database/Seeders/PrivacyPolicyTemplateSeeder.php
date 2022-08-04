<?php

namespace Modules\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\ContractTemplate;

class PrivacyPolicyTemplateSeeder extends Seeder
{
    public function run()
    {
        DB::table('contract_template')->insert(
            [
                'type' => ContractTemplate::TYPE_COOKIE_PRIVACY,
                'name' => 'Privacy policy and cookie policy',
                'version' => '1.0',
                'text' => file_get_contents(__DIR__ . '/UserAgreementTemplates/privacy-and-cookie-template.html'),
                'variables' => json_encode([]),
                'start_date' => Carbon::now()->toDateString(),
            ]
        );
    }
}
