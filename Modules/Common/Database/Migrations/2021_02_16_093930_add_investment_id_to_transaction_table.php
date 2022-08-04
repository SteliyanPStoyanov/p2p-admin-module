<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestmentIdToTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'transaction',
            function (Blueprint $table) {
                $table->integer('investment_id')->unsigned()
                    ->nullable()->after('bank_account_id');
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
            'transaction',
            function (Blueprint $table) {
                $table->dropColumn('investment_id');
            }
         );
    }
}
