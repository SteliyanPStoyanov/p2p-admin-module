<?php

namespace Modules\Admin\Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class FakeAdministratorSeeder extends \Illuminate\Database\Seeder
{
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 50) as $index) {
            DB::table('administrator')->insert(
                [
                    'first_name' => $faker->firstName,
                    'middle_name' => $faker->name,
                    'last_name' => $faker->lastName,
                    'phone' => $faker->phoneNumber,
                    'email' => $faker->email,
                    'username' => $faker->userName,
                    'password' => $faker->password,
                    'created_at' => now(),
                    'created_by' => 1,
                ]
            );
        }
    }
}
