<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\FileType;

class AddFileTypeContract extends Seeder
{
    public function run()
    {
        DB::table('file_type')->insert(
            [
                'file_type_id' => FileType::INVESTOR_CONTRACT_ID,
                'name' => FileType::INVESTOR_CONTRACT_NAME,
                'created_at' => now(),
                'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
            ]
        );
    }
}
