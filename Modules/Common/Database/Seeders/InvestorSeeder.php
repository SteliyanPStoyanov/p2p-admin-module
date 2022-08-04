<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Investor;
use Faker\Factory as Faker;

class InvestorSeeder extends Seeder
{
    public function run()
    {
        DB::table('investor')->insert(
            [
                'investor_id' => Investor::INVESTOR_ID,
                'email' => 'profile@stikcredit.bg',
                'password' => '$2y$10$iLX8SP0ljoM8QUyPvxXFA.Oa6OKUsQWkWys6mGR0NCQJA7ys9xnIK',
                'first_name' => 'Investor',
                'middle_name' => 'Mega',
                'last_name' => 'Invest',
                'phone' => '08888888888',
                'birth_date' => now(),
                'citizenship' => 1,
                'residence' => 1,
                'city' => 'Sofia',
                'postcode' => '1000',
                'address' => 'Alecsandyr malinov 91',
                'type' => 'individual',
                'political' => 1,
                'status' => 'verified',
                'referral_hash' => 'Bpwq0rzG21',
                'created_at' => now(),
                'created_by' => Investor::INVESTOR_ID,
            ]
        );

        $this->call(InvestorAutoIncrementFixSeeder::class);

        //More fake data so we can list them and test. TODO: remove
        $faker = Faker::create();
        $investors = [];
        for ($i = 0; $i < 1000; $i++) {
            $investors[] = [
                'email' => $faker->email,
                'password' => bcrypt($faker->password),
                'first_name' => $faker->firstName,
                'middle_name' => $faker->name,
                'last_name' => $faker->lastName,
                'phone' => $faker->phoneNumber,
                'birth_date' => $faker->date(),
                'citizenship' => $faker->numberBetween(1, 250),
                'residence' => $faker->numberBetween(1, 250),
                'city' => $faker->city,
                'postcode' => $faker->postcode,
                'address' => $faker->address,
                'type' => $faker->randomElement(['individual', 'company']),
                'political' => 1,
                'status' => $faker->randomElement(
                    [
                        Investor::INVESTOR_STATUS_UNREGISTERED,
                        Investor::INVESTOR_STATUS_REGISTERED,
                        Investor::INVESTOR_STATUS_AWAITING_DOCUMENTS,
                        Investor::INVESTOR_STATUS_VERIFIED,
                        Investor::INVESTOR_STATUS_REJECTED_VERIFICATION,
                        Investor::INVESTOR_STATUS_AWAITING_VERIFICATION
                    ]
                ),
                'created_at' => Carbon::now()->subDays(round($i / 5)),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ];
        }

        DB::table('investor')->insert($investors);
    }

}
