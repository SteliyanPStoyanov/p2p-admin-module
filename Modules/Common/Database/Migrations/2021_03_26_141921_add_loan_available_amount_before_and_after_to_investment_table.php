<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoanAvailableAmountBeforeAndAfterToInvestmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'investment',
            function (Blueprint $table) {
                $table->decimal('loan_available_amount_before', 11, 2)->unsigned()
                    ->nullable()->after('amount');
                $table->decimal('loan_available_amount_after', 11, 2)->unsigned()
                    ->nullable()->after('amount');
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
            'investment',
            function (Blueprint $table) {
                $table->dropColumn('loan_available_amount_before');
                $table->dropColumn('loan_available_amount_after');
            }
        );
    }
}
