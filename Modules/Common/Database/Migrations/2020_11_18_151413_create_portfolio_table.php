<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;
use Modules\Common\Entities\Portfolio;

class CreatePortfolioTable extends Migration
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
            'portfolio',
            function ($table) {
                $table->bigIncrements('portfolio_id');
                $table->integer('investor_id')->unsigned()->index();
                $table->integer('currency_id')->unsigned()->nullable();
                $table->enum('type', Portfolio::getPortfolioTypes());
                $table->date('date');
                $table->integer('range1')->default(0);
                $table->integer('range2')->default(0);
                $table->integer('range3')->default(0);
                $table->integer('range4')->default(0);
                $table->integer('range5')->default(0);
                $table->timestamp('ranges_updated_at')->nullable()->index();

                $table->tableCrudFields();
                $table->foreign('investor_id')->references('investor_id')->on('investor')->onDelete('cascade');
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
            'portfolio',
            function (Blueprint $table) {
                $table->dropForeign('portfolio_investor_id_foreign');
            }
        );

        Schema::dropIfExists('portfolio');
    }
}
