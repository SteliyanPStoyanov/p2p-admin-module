<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCrudFieldsToLoginAttemptHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        public function up()
    {
        Schema::table(
            'login_attempt_history',
            function (Blueprint $table) {
                $table->smallInteger('last')->default('1')->index();
                $table->tinyInteger('active')->default('1')->index();
                $table->tinyInteger('deleted')->default('0');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->bigInteger('created_by')
                    ->unsigned()
                    ->nullable()
                    ->references('administrator_id')
                    ->on('administrator');
                $table->timestamp('updated_at')->nullable();
                $table->bigInteger('updated_by')
                    ->unsigned()
                    ->nullable()
                    ->references('administrator_id')
                    ->on('administrator');
                $table->timestamp('deleted_at')->nullable();
                $table->bigInteger('deleted_by')
                    ->unsigned()
                    ->nullable()
                    ->references('administrator_id')
                    ->on('administrator');

                $table->timestamp('enabled_at')->nullable();
                $table->bigInteger('enabled_by')
                    ->unsigned()
                    ->nullable()
                    ->references('administrator_id')
                    ->on('administrator');

                $table->timestamp('disabled_at')->nullable();
                $table->bigInteger('disabled_by')
                    ->unsigned()
                    ->nullable()
                    ->references('administrator_id')
                    ->on('administrator');
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
            'login_attempt_history',
            function (Blueprint $table) {
                $table->dropColumn('last');
                $table->dropColumn('active');
                $table->dropColumn('deleted');
                $table->dropColumn('created_at');
                $table->dropColumn('updated_at');
                $table->dropColumn('deleted_at');
                $table->dropColumn('enabled_at');
                $table->dropColumn('disabled_at');

                $table->dropColumn('created_by');
                $table->dropColumn('updated_by');
                $table->dropColumn('deleted_by');
                $table->dropColumn('disabled_by');
            }
        );
    }
}
