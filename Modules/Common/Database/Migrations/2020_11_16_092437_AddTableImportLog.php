<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class AddTableImportLog extends Migration
{
    use CustomSchemaBuilderTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getCustomSchemaBuilder(DB::getSchemaBuilder())->create(
            'cron_log',
            function ($table) {
                $table->bigIncrements('cron_log_id');
                $table->string('command', 50)->index();
                $table->string('file', 150)->nullable();
                $table->string('message', 200)->nullable();
                $table->integer('total')->nullable();
                $table->integer('imported')->nullable();
                $table->integer('attempt')->nullable();
                $table->decimal('total_exec_time', 11, 2)->nullable();
                $table->decimal('last_exec_time', 11, 2)->nullable();
                $table->tableCreateFields(false, true);
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
        Schema::dropIfExists('cron_log');
    }
}
