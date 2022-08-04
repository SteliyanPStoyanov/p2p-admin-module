<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Agreement;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorAgreement;


class AddNewDeviceAgreementToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $investorIds = Investor::all()->pluck('investor_id');

        foreach ($investorIds as $investorId) {
            $investorAgreement = new InvestorAgreement();
            $investorAgreement->fill(
                [
                    'investor_id' => $investorId,
                    'agreement_id' => Agreement::NEW_DEVICE_NOTIFICATION,
                    'value' => 1,
                ]
            );
            $investorAgreement->save();
        }
    }

}
