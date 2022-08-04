<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Document;
use Modules\Common\Entities\DocumentType;

class DocumentTypeSeeder extends Seeder
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
                    'document_type_id' => DocumentType::DOCUMENT_TYPE_ID_IDCARD,
                    'name' => DocumentType::DOCUMENT_TYPE_NAME_IDCARD,
                    'description' => DocumentType::DOCUMENT_TYPE_NAME_IDCARD,
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ],
                [
                    'document_type_id' => DocumentType::DOCUMENT_TYPE_ID_PASSPORT,
                    'name' => DocumentType::DOCUMENT_TYPE_NAME_PASSPORT,
                    'description' => 'Passport',
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ],
                [
                    'document_type_id' => DocumentType::DOCUMENT_TYPE_FROM_ADMIN_ID,
                    'name' => DocumentType::DOCUMENT_TYPE_FROM_ADMIN,
                    'description' => DocumentType::DOCUMENT_TYPE_FROM_ADMIN,
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ]
            ]
        );
    }

}
