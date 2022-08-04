<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\ImportedPayment;
use Modules\Common\Entities\LoanAmountAvailable;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateLoanAmountAvailableTable extends Migration
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
            'loan_amount_available',
            function ($table) {
                $table->bigIncrements('loan_amount_available_id');
                $table->bigInteger('loan_id')->unsigned();
                $table->decimal('amount_before', 11, 2);
                $table->decimal('amount_after', 11, 2);
                $table->enum('type', LoanAmountAvailable::getTypes());
                $table->bigInteger('investment_id')->unsigned()->nullable();
                $table->bigInteger('installment_id')->unsigned()->nullable();
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
        Schema::dropIfExists('loan_amount_available');
    }
}
