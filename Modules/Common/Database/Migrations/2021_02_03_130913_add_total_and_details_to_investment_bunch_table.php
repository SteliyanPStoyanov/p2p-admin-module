<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalAndDetailsToInvestmentBunchTable extends Migration
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
                $table->integer('total')->unsigned()->nullable()->after('count');
                $table->text('details')->nullable();
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
                $table->dropColumn('total');
                $table->dropColumn('details');
            }
        );
    }
}
