<?php

namespace Modules\Communication\Database\Seeders;

use Illuminate\Database\Seeder;

class CommunicationDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            [
                EmailSourceSeeder::class,
                EmailTemplateSeeder::class,
                EmailCampaignSeeder::class
            ]
        );
    }
}
