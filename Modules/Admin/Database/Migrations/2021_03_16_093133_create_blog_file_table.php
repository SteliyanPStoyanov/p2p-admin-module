<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateBlogFileTable extends Migration
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
            'blog_file',
            function ($table) {
                $table->integer('blog_page_id')->unsigned()->index();
                $table->foreign('blog_page_id')->references('blog_page_id')->on('blog_page')->onDelete('cascade');

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
        Schema::dropIfExists('blog_file');
    }
}
