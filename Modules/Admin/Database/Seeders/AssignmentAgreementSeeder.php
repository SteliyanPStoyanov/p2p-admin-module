<?php

namespace Modules\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\ContractTemplate;

class AssignmentAgreementSeeder extends Seeder
{
    public function run()
    {
        DB::table('contract_template')->insert(
            [
                'type' => ContractTemplate::TYPE_LOAN,
                'name' => 'Assignment agreement',
                'version' => '1.0',
                'text' => file_get_contents(__DIR__ . '/UserAgreementTemplates/loan-assignment-template.html'),
                'variables' => json_encode(
                    ContractTemplate::ASSIGNMENT_AGREEMENT_VARS,
                ),
                'start_date' => Carbon::now()->toDateString(),
            ]
        );
    }
}
