<?php

namespace Modules\Communication\Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Communication\Entities\Email;

class EmailCampaignSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 1) as $index) {
            DB::table('email_campaign')->insert(
                [
                    'name' => $faker->word,
                    'email_template_id' => $index,
                    'email_source_id' => $index,
                    'type' => $faker->randomElement(Email::getEmailTypes()),
                    'sender_email' => $faker->email,
                    'sender_name' => $faker->name,
                    'reply_email' => $faker->email,
                    'reply_name' => $faker->name,
                    'start_at' => now(),
                    'end_at' => now(),
                    'period' => json_encode(["week","years","month"]),
                    'products' => json_encode(["1","2","3"]),
                    'last_send_date' => now(),
                    'created_at' => now(),
                    'created_by' => 1
                ]
            );
        }
    }
}

