<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\FileType;

class AddFileTypeSelfieSeeder extends Seeder
{
    public function run()
    {
        $fileTypes = [
            [
                'file_type_id' => FileType::SELFIE_ID,
                'name' => FileType::SELFIE_NAME,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
        ];

        DB::table('file_type')->insert($fileTypes);
    }

}
