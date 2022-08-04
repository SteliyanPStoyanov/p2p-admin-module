<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(file_get_contents(__DIR__ . '/SqlFiles/afranga_db_public_country.sql'));
        DB::statement("SELECT setval('country_country_id_seq', (SELECT MAX(country_id) FROM country));");
    }
}
