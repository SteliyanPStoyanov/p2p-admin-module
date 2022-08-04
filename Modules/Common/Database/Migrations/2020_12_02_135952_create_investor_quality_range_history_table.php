<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestorQualityRangeHistoryTable extends Migration
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
            'investor_quality_range_history',
            function ($table) {
                $table->bigIncrements('history_id');
                $table->bigInteger('investor_quality_range_id');
                $table->bigInteger('investor_id')->unsigned()->index();
                $table->bigInteger('loan_id')->unsigned()->index();
                $table->integer('range')->unsigned();

                $table->tableCrudFields();
                $table->tableArchiveFields();

                $table->foreign('investor_id')->references('investor_id')->on('investor')->onDelete('cascade');
                $table->foreign('loan_id')->references('loan_id')->on('loan')->onDelete('cascade');
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
            'investor_quality_range_history',
            function (Blueprint $table) {
                $table->dropForeign('investor_quality_range_history_investor_id_foreign');
                $table->dropForeign('investor_quality_range_history_loan_id_foreign');
            }
        );

        Schema::dropIfExists('investor_quality_range_history');
    }
}
