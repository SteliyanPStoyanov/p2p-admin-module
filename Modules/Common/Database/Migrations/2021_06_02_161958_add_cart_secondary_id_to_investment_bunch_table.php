<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCartSecondaryIdToInvestmentBunchTable extends Migration
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
                $table->integer('cart_secondary_id')->unsigned()
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
                $table->dropColumn('cart_secondary_id');
            }
        );
    }
}
