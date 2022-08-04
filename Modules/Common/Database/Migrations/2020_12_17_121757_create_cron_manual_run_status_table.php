<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\CronStatus;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateCronManualRunStatusTable extends Migration
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
            'cron_manual_run_status',
            function ($table) {
                $table->bigIncrements('cron_manual_run_status_id');
                $table->string('command')->index();
                $table->enum('status', CronStatus::getTypes());

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
        Schema::dropIfExists('cron_manual_run_status');
    }
}
