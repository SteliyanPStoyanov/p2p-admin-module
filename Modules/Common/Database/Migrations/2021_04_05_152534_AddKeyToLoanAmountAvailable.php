<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKeyToLoanAmountAvailable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'loan_amount_available',
            function (Blueprint $table) {
                $table->string('key', 255)->nullable()->index();

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
            'loan_amount_available',
            function (Blueprint $table) {
                $table->dropColumn('key');
            }
        );
    }
}
