<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\FileType;

class FileTypeSeeder extends Seeder
{
    public function run()
    {
        $fileTypes = [
            [
                'file_type_id' => FileType::ID_CARD_ID,
                'name' => FileType::ID_CARD_NAME,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'file_type_id' => FileType::PASSPORT_ID,
                'name' => FileType::PASSPORT_NAME,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'file_type_id' => FileType::NEW_LOANS_ID,
                'name' => FileType::NEW_LOANS_NAME,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'file_type_id' => FileType::UNLISTED_LOANS_ID,
                'name' => FileType::UNLISTED_LOANS_NAME,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
            [
                'file_type_id' => FileType::IMAGE_BLOG_ID,
                'name' => FileType::IMAGE_BLOG_NAME,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
        ];

        DB::table('file_type')->insert($fileTypes);
    }

}
