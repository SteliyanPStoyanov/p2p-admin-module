<?php

namespace Modules\Common\Database\Seeders;

use Illuminate\Database\Seeder;

class CommonSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            DocumentTypeSeeder::class,
            OriginatorSeeder::class,
            FileStorageSeeder::class,
            FileTypeSeeder::class,
            AgreementSeeder::class,
            AddFileTypeContract::class,
            AddFileTypeSelfieSeeder::class,
            DocumentTypeAddSelfieSeeder::class,
            StartedIdsSeeder::class,
            NewDeviceAgreementSeeder::class,
            AddFileTypeImportedPaymentSeeder::class,
            AddFileTypeCompanySeeder::class,
            AddDocumentTypeCompanySeeder::class
        ]);
    }
}
