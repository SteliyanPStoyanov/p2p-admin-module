<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateBlockedIpHistoryTable extends Migration
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
            'blocked_ip_history',
            function ($table) {
                $table->bigIncrements('history_id');
                $table->bigInteger('id');
                $table->string('ip');
                $table->timestamp('blocked_till');

                $table->tableCreateFields();
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
        Schema::dropIfExists('blocked_ip_history');
    }
}
