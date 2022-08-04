<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvestmentIdToLoanContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'loan_contract',
            function (Blueprint $table) {
                $table->integer('investment_id')->unsigned()
                    ->nullable()->after('investor_id');
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
            'loan_contract',
            function (Blueprint $table) {
                $table->dropColumn('investment_id');
            }
        );
    }
}
