<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\FileType;

class AddFileTypeImportedPaymentSeeder extends Seeder
{
    public function run()
    {
        $fileTypes = [
            [
                'file_type_id' => FileType::IMPORTED_PAYMENT_ID,
                'name' => FileType::IMPORTED_PAYMENT_NAME,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ],
        ];

        DB::table('file_type')->insert($fileTypes);
    }

}
