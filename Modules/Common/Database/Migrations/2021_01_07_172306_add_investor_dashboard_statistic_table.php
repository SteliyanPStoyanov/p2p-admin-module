<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestorDashboardStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'investor',
            function (Blueprint $table) {
                $table->integer('statistic_days')->unsigned()
                    ->default(\Modules\Common\Entities\Investor::INVESTOR_STATISTIC_DAYS[0])
                    ->nullable()->after('referral_id');
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
            'investor',
            function (Blueprint $table) {
                $table->dropColumn('statistic_days');
            }
        );
    }
}
