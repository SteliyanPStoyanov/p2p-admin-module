<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateAutoRebuyLoanTable extends Migration
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
            'auto_rebuy_loan',
            function ($table) {
                $table->bigIncrements('auto_rebuy_loan_id');
                $table->bigInteger('loan_id')->unsigned()->index();
                $table->decimal('remaining_principal', 11, 2);
                $table->integer('overdue_days')->unsigned();

                $table->tableCreateFields();

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
            'auto_rebuy_loan',
            function (Blueprint $table) {
                $table->dropForeign('auto_rebuy_loan_loan_id_foreign');
            }
        );

        Schema::dropIfExists('auto_rebuy_loan');
    }
}
