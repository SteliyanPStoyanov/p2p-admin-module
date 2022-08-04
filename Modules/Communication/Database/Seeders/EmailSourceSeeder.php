<?php

namespace Modules\Communication\Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailSourceSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        DB::table('email_source')->insert([
            'email_source_id' => 1,
            'name' => $faker->word,
            'type' => $faker->randomElement(['callback', 'list']),
            'source' => $faker->randomElement(['client', 'admin']),
            'details' => $faker->word,
            'created_at' => now(),
            'created_by' => 1,
        ]);
    }
}

