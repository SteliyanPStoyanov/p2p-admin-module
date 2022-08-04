<?php

namespace Modules\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\ContractTemplate;

class RefferAFriendTemplateSeeder extends Seeder
{
    public function run()
    {
        DB::table('contract_template')->insert(
            [
                'type' => ContractTemplate::TYPE_REFER_A_FRIEND,
                'name' => 'Refer a friend T&C',
                'version' => '1.0',
                'text' => file_get_contents(__DIR__ . '/UserAgreementTemplates/refer-a-friend-terms-template.html'),
                'variables' => json_encode([]),
                'start_date' => Carbon::now()->toDateString(),
            ]
        );
    }
}
