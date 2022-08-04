<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Modules\Core\Traits\CustomSchemaBuilderTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBlogPageTable extends Migration
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
            'blog_page',
            function ($table) {
                $table->bigIncrements('blog_page_id');
                $table->integer('administrator_id')->unsigned();
                $table->string('title');
                $table->date('date');
                $table->json('tags')->nullable();
                $table->text('content');
                $table->tableCrudFields();

                $table->foreign('administrator_id')->references('administrator_id')->on('administrator');
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
            'blog_page',
            function (Blueprint $table) {
                $table->dropForeign('blog_page_administrator_id_foreign');
            }
        );

        Schema::dropIfExists('blog_page');
    }
}
