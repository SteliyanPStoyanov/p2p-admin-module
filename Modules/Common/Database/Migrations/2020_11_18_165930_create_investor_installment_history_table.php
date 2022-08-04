<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestorInstallmentHistoryTable extends Migration
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
            'investor_installment_history',
            function ($table) {
                $table->bigIncrements('history_id');
                $table->integer('investor_installment_id')->unsigned()->index();
                $table->integer('loan_id')->unsigned()->index();
                $table->integer('investment_id')->unsigned()->index();
                $table->integer('investor_id')->unsigned()->index();
                $table->integer('installment_id')->unsigned()->index();
                $table->integer('currency_id')->unsigned()->nullable();
                $table->integer('days')->unsigned()->nullable();
                $table->decimal('remaining_principal', 11, 2);
                $table->decimal('principal', 11, 2);
                $table->decimal('accrued_interest', 11, 2)->default(0);
                $table->decimal('interest', 11, 2);
                $table->decimal('late_interest', 11, 2)->nullable();
                $table->double('interest_percent', 11, 12);
                $table->decimal('total', 11, 2);
                $table->tinyInteger('paid')->nullable()->default(0);
                $table->timestamp('paid_at', 0)->nullable();

                $table->tableCrudFields();
                $table->tableArchiveFields();

                $table->foreign('investor_id')->references('investor_id')->on('investor')->onDelete('cascade');
                $table->foreign('loan_id')->references('loan_id')->on('loan')->onDelete('cascade');
                $table->foreign('installment_id')->references('installment_id')->on('installment')->onDelete('cascade');
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
        Schema::dropIfExists('investor_installment_history');
    }
}
