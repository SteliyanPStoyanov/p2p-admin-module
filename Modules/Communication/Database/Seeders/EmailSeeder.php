<?php

namespace Modules\Communication\Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Investor;
use Modules\Communication\Entities\Email;

class EmailSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 1) as $index) {
            DB::table('email')->insert(
                [
                    'email_template_id' => $index,
                    'email_campaign_id' => $index,
                    'investor_id' => Investor::INVESTOR_ID,
                    'identifier' => $faker->word,
                    'sender_from' => $faker->word,
                    'sender_to' => $faker->word,
                    'sender_reply' => $faker->word,
                    'title' => $faker->paragraph($nbSentences = 1, $variableNbSentences = true),
                    'body' => $faker->paragraph($nbSentences = 1, $variableNbSentences = true),
                    'text' => $faker->paragraph($nbSentences = 1, $variableNbSentences = true),
                    'response' => $faker->word,
                    'queue' => $faker->word,
                    'queued_at' => now(),
                    'tries' => $faker->randomDigit,
                    'send_at' => now(),
                    'received_at' => now(),
                    'opened_at' => now(),
                    'has_files' => 1
                ]
            );
        }
    }
}

