<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestorQualityRangeTable extends Migration
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
            'investor_quality_range',
            function ($table) {
                $table->bigIncrements('investor_quality_range_id');
                $table->bigInteger('investor_id')->unsigned()->index();
                $table->bigInteger('loan_id')->unsigned()->index();
                $table->integer('range')->unsigned();

                $table->tableCrudFields();

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
            'investor_quality_range',
            function (Blueprint $table) {
                $table->dropForeign('investor_quality_range_investor_id_foreign');
                $table->dropForeign('investor_quality_range_loan_id_foreign');
            }
        );

        Schema::dropIfExists('investor_quality_range');
    }
}
