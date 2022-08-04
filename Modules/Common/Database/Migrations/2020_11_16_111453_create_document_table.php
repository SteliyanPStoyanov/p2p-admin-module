<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateDocumentTable extends Migration
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
            'document',
            function ($table) {
                $table->bigIncrements('document_id');
                $table->integer('document_type_id')->unsigned()->index();
                $table->integer('investor_id')->unsigned()->index();
                $table->integer('file_id')->unsigned()->index();
                $table->string('name');
                $table->string('description');
                $table->tableCrudFields();
                $table->foreign('document_type_id')->references('document_type_id')->on('document_type');
                $table->foreign('investor_id')->references('investor_id')->on('investor');
                $table->foreign('file_id')->references('file_id')->on('file');
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
            'document',
            function (Blueprint $table) {
                $table->dropForeign('document_document_type_id_foreign');
                $table->dropForeign('document_investor_id_foreign');
                $table->dropForeign('document_file_id_foreign');
            }
        );

        Schema::dropIfExists('document');
    }
}

