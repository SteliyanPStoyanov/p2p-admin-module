<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateDocumentDownloadLogTable extends Migration
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
            'document_download_log',
            function ($table) {
                $table->bigIncrements('document_download_log_id');
                $table->integer('document_id')->unsigned()->index();
                $table->integer('downloaded_by')->unsigned();
                $table->timestamp('downloaded_at');
                $table->foreign('document_id')->references('document_id')->on('document');
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
        Schema::dropIfExists('document_download_log');
    }
}
