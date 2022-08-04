<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateRegistrationAttemptHistoryTable extends Migration
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
            'registration_attempt_history',
            function ($table) {
                $table->bigIncrements('history_id');
                $table->integer('id');
                $table->timestamp('datetime');
                $table->string('email');
                $table->string('ip');
                $table->string('device');

                $table->tableArchiveFields();
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
        Schema::dropIfExists('registration_attempt_history');
    }
}