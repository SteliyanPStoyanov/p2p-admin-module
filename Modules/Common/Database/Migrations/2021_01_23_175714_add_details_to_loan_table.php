<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailsToLoanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'loan',
            function (Blueprint $table) {
                $table->string('details')->unsigned()
                    ->nullable()->after('interest_updated_at');
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
            'loan',
            function (Blueprint $table) {
                $table->dropColumn('details');
            }
        );
    }
}
