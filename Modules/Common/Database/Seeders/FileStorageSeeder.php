<?php


namespace Modules\Common\Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\FileStorage;

class FileStorageSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        DB::table('file_storage')->insert(
            [
                'file_storage_id' => FileStorage::FILE_STORAGE_HARD_DISC_ONE_ID,
                'name' => FileStorage::FILE_STORAGE_HARD_DISC_ONE_NAME,
                'disk_total' => $faker->randomNumber(9),
                'disk_usage' => $faker->randomNumber(9),
                'disk_space' => $faker->randomNumber(9),
                'last_file_id' => 1,
                'last_file_update_date' => now(),
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ]
        );
    }

}
