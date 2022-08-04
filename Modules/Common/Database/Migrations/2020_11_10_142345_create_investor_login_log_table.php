<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestorLoginLogTable extends Migration
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
            'investor_login_log',
            function ($table) {
                $table->bigIncrements('investor_login_log_id');
                $table->integer('investor_id')->unsigned()->index();
                $table->string('ip');
                $table->string('device');
                $table->tableCrudFields();
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
        Schema::dropIfExists('investor_login_log');
    }
}
