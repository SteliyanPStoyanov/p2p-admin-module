<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailsToInvestmentTable extends Migration
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
            'investment',
            function (Blueprint $table) {
                $table->dropColumn('details');
            }
        );
    }
}
