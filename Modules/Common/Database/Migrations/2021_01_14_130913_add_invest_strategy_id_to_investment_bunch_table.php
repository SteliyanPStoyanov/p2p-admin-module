<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestStrategyIdToInvestmentBunchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'investment_bunch',
            function (Blueprint $table) {
                $table->integer('invest_strategy_id')->unsigned()
                    ->nullable()->after('filters');
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
            'investment_bunch',
            function (Blueprint $table) {
                $table->dropColumn('invest_strategy_id');
            }
        );
    }
}
