<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateOriginatorTable extends Migration
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
            'originator',
            function ($table) {
                $table->bigIncrements('originator_id');
                $table->string('name');
                $table->string('description');
                $table->string('phone');
                $table->string('email');
                $table->string('website');
                $table->string('iban');
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
        Schema::dropIfExists('originator');
    }
}
