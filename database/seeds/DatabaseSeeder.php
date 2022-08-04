<?php

use Illuminate\Database\Seeder;
use Modules\Admin\Database\Seeders\AdministratorAutoIncrementFixSeeder;
use Modules\Admin\Database\Seeders\AdministratorSeeder;
use Modules\Admin\Database\Seeders\AssignmentAgreementSeeder;
use Modules\Admin\Database\Seeders\FakeAdministratorSeeder;
use Modules\Admin\Database\Seeders\OriginatorAddCountryAndPinSeeder;
use Modules\Admin\Database\Seeders\PrivacyPolicyTemplateSeeder;
use Modules\Admin\Database\Seeders\RefferAFriendTemplateSeeder;
use Modules\Admin\Database\Seeders\RolePermissionSeeder;
use Modules\Admin\Database\Seeders\SettingsSeeder;
use Modules\Admin\Database\Seeders\SystemDefaultUserSeeder;
use Modules\Admin\Database\Seeders\UnitDefaultUserSeeder;
use Modules\Admin\Database\Seeders\UserAgreementSeeder;
use Modules\Common\Database\Seeders\CommonSeeder;
use Modules\Common\Database\Seeders\CountrySeeder;
use Modules\Common\Database\Seeders\CurrencySeeder;
use Modules\Common\Database\Seeders\LocaleSeeder;
use Modules\Communication\Database\Seeders\CommunicationDatabaseSeeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(
            [
                // FakeAdministratorSeeder::class,
                AdministratorAutoIncrementFixSeeder::class,
                AdministratorSeeder::class,
                AssignmentAgreementSeeder::class,
                CommonSeeder::class,
                CommunicationDatabaseSeeder::class,
                CountrySeeder::class,
                CurrencySeeder::class,
                LocaleSeeder::class,
                OriginatorAddCountryAndPinSeeder::class,
                RolePermissionSeeder::class,
                SettingsSeeder::class,
                SystemDefaultUserSeeder::class,
                UnitDefaultUserSeeder::class,
                UserAgreementSeeder::class,
                AssignmentAgreementSeeder::class,
                PrivacyPolicyTemplateSeeder::class,
                RefferAFriendTemplateSeeder::class,

            ]
        );
    }
}
