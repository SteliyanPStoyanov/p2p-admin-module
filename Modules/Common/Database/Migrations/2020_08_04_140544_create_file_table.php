<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateFileTable extends Migration
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
            'file',
            function ($table) {
                $table->bigIncrements('file_id');
                $table->integer('file_storage_id')->unsigned()->index()->nullable();
                $table->integer('file_type_id')->unsigned()->index()->nullable();
                $table->string('hash');
                $table->string('file_path');
                $table->string('file_size');
                $table->string('file_type'); // this is a type of file: jpg, pdf, etc
                $table->string('file_name');
                $table->foreign('file_storage_id')->references('file_storage_id')->on('file_storage');
                $table->foreign('file_type_id')->references('file_type_id')->on('file_type');
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
        Schema::table(
            'file',
            function (Blueprint $table) {
                $table->dropForeign('file_file_storage_id_foreign');
                $table->dropForeign('file_file_type_id_foreign');
            }
        );

        Schema::dropIfExists('file');
    }
}
