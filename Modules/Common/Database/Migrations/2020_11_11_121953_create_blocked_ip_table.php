<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateBlockedIpTable extends Migration
{
    use CustomSchemaBuilderTrait;

    public function up()
    {
        $this->getCustomSchemaBuilder(DB::getSchemaBuilder())->create(
            'blocked_ip',
            function ($table) {
                $table->bigIncrements('id');
                $table->string('ip');
                $table->timestamp('blocked_till');

                $table->tableCreateFields();
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
        Schema::dropIfExists('blocked_ip');
    }
}
