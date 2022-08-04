<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateEmailFileTable extends Migration
{
    use CustomSchemaBuilderTrait;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getCustomPivotSchemaBuilder(DB::getSchemaBuilder())->create(
            'email_file',
            function ($table) {
                $table->integer('email_id')->unsigned()->index();
                $table->foreign('email_id')->references('email_id')->on('email')->onDelete('cascade');
                $table->integer('file_id')->unsigned()->index();
                $table->foreign('file_id')->references('file_id')->on('file')->onDelete('cascade');
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
        Schema::dropIfExists('email_file');
    }
}
