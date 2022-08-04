<?php

namespace Modules\Common\Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Portfolio;

class PortfolioSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $portfolio = [];
        for ($i = 0; $i < 2; $i++) {
            $portfolio[] = [
                'investor_id' => Investor::INVESTOR_ID,
                'currency_id' => Currency::ID_EUR,
                'type' => $faker->unique()->randomElement(Portfolio::getPortfolioTypes()),
                'date' => now()
            ];
        }

        DB::table('portfolio')->insert($portfolio);
    }

}
