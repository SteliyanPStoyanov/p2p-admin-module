<?php

namespace Modules\Common\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Administrator;
use Modules\Common\Entities\Document;
use Modules\Common\Entities\DocumentType;

class DocumentTypeAddSelfieSeeder extends Seeder
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
                    'document_type_id' => DocumentType::DOCUMENT_TYPE_SELFIE_ID,
                    'name' => DocumentType::DOCUMENT_TYPE_SELFIE,
                    'description' => DocumentType::DOCUMENT_TYPE_SELFIE,
                    'created_at' => Carbon::now(),
                    'created_by' => Administrator::SYSTEM_ADMINISTRATOR_ID,
                ],
            ]
        );
    }

}
