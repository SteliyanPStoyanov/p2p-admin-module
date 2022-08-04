<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\DocumentType;

class AddDocumentTypeCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('document_type')->insert(
            [
                [
                    'document_type_id' => DocumentType::DOCUMENT_TYPE_ID_COMPANY,
                    'name' => DocumentType::DOCUMENT_TYPE_NAME_COMPANY,
                    'description' => DocumentType::DOCUMENT_TYPE_NAME_COMPANY,
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ],
            ]
        );
    }

}
