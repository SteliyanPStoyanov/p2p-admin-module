<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCounteragentInTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Need this field for secondary_market purpose
     * @return void
     */
    public function up()
    {
        Schema::table(
            'transaction',
            function (Blueprint $table) {
                $table->integer('counteragent')->unsigned()->nullable()->default(null);
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
        //
    }
}
