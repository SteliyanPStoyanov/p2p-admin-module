<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFieldsToTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'task',
            function (Blueprint $table) {
                $table->string('details')->unsigned()
                    ->nullable()->after('bank_account_id');

                $table->string('comment')->unsigned()
                    ->nullable()->after('bank_account_id');

                $table->integer('investor_bonus_id')->unsigned()
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
            'task',
            function (Blueprint $table) {
                $table->dropColumn('details');
                $table->dropColumn('comment');
                $table->dropColumn('investor_bonus_id');
            }
        );
    }
}
