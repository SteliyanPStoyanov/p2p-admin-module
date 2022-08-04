<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateLoanContractTable extends Migration
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
            'loan_contract',
            function ($table) {
                $table->bigIncrements('loan_contract_id');
                $table->bigInteger('loan_id')->unsigned();
                $table->bigInteger('investor_id')->unsigned();
                $table->bigInteger('contract_template_id')->unsigned();
                $table->bigInteger('file_id')->unsigned();
                $table->json('data');

                $table->foreign('loan_id')->references('loan_id')->on('loan')->onDelete('cascade');
                $table->foreign('investor_id')->references('investor_id')->on('investor')->onDelete('cascade');
                $table->foreign('contract_template_id')->references('contract_template_id')->on('contract_template')->onDelete('cascade');
                $table->foreign('file_id')->references('file_id')->on('file')->onDelete('cascade');

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
            'loan_contract',
            function (Blueprint $table) {
                $table->dropForeign('loan_contract_investor_id_foreign');
                $table->dropForeign('loan_contract_contract_template_id_foreign');
                $table->dropForeign('loan_contract_file_id_foreign');
                $table->dropForeign('loan_contract_loan_id_foreign');
            }
        );

        Schema::dropIfExists('loan_contract');
    }
}
