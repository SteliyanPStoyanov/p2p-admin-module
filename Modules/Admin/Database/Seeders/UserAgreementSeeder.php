<?php

namespace Modules\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\ContractTemplate;

class UserAgreementSeeder extends Seeder
{
    public function run()
    {
        DB::table('contract_template')->insert(
            [
                'type' => 'investor',
                'name' => 'User agreement',
                'version' => '1.0',
                'text' => file_get_contents(__DIR__ . '/UserAgreementTemplates/user-agreement-template.html'),
                'variables' => json_encode(
                    ContractTemplate::USER_AGREEMENT_VARS,
                ),
                'start_date' => Carbon::now()->toDateString(),
            ]
        );

        $userAgreementService = \App::make(UserAgreementService::class);
        $userAgreementService->updateUserAgreementTemplate();
    }
}
