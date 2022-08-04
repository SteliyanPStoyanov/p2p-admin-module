<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Admin\Entities\Administrator;

class AddAdministratorDashboardStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'administrator',
            function (Blueprint $table) {
                $table->integer('statistic_days')->unsigned()
                    ->default(Administrator::ADMINISTRATOR_STATISTIC_DAYS[0])
                    ->nullable()->after('remember_token');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'administrator',
            function (Blueprint $table) {
                $table->dropColumn('statistic_days');
            }
        );
    }
}
