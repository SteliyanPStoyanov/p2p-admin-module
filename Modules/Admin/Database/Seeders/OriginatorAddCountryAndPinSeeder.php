<?php

namespace Modules\Admin\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\ContractTemplate;
use Modules\Common\Entities\Country;
use Modules\Common\Entities\Originator;

class OriginatorAddCountryAndPinSeeder extends Seeder
{
    public function run()
    {
        DB::table('originator')->where('originator_id', Originator::ID_ORIG_STIKCREDIT)->update(
            [
                'country_id' => Country::ID_BG,
                'pin' => Originator::PIN_STIKCREDIT,
            ]
        );
    }
}
