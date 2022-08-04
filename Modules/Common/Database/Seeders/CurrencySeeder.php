<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $currencies = [
            [
                'name'  => 'Euro',
                'code'  => 'EUR',
                'rate'  => 0,
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                'created_at' => Carbon::now(),
            ],
            [
                'name'  => 'USD',
                'code'  => 'USD',
                'rate'  => 0,
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                'created_at' => Carbon::now(),
            ],
            [
                'name'  => 'BGN',
                'code'  => 'BGN',
                'rate'  => 0,
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                'created_at' => Carbon::now(),
            ],
        ];

        DB::table('currency')->insert($currencies);
    }
}
