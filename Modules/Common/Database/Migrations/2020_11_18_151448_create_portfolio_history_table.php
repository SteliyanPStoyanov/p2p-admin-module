<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;
use Modules\Common\Entities\Portfolio;

class CreatePortfolioHistoryTable extends Migration
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
            'portfolio_history',
            function ($table) {
                $table->bigIncrements('portfolio_history_id');
                $table->integer('portfolio_id')->unsigned()->index();
                $table->integer('investor_id')->unsigned()->index();
                $table->integer('currency_id')->unsigned()->nullable();
                $table->enum('type', Portfolio::getPortfolioTypes());
                $table->date('date');
                $table->integer('range1');
                $table->integer('range2');
                $table->integer('range3');
                $table->integer('range4');
                $table->integer('range5');
                $table->timestamp('ranges_updated_at')->nullable()->index();

                $table->tableCrudFields();
                $table->tableArchiveFields();

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
            'portfolio_history',
            function (Blueprint $table) {
                $table->dropForeign('portfolio_history_investor_id_foreign');
            }
        );

        Schema::dropIfExists('portfolio_history');
    }
}
