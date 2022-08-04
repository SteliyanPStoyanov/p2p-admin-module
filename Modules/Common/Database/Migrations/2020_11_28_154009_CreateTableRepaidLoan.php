<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Common\Entities\RepaidLoan;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateTableRepaidLoan extends Migration
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
            'repaid_loan',
            function ($table) {
                $table->bigIncrements('repaid_loan_id');
                $table->integer('lender_id')->index();
                $table->enum('repayment_type', RepaidLoan::getTypes());
                $table->tinyInteger('handled')->default(0);
                $table->tableCreateFields(false, true);
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
        Schema::dropIfExists('repaid_loan');
    }
}
