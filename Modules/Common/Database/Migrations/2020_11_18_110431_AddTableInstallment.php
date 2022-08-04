<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Loan;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class AddTableInstallment extends Migration
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
            'installment',
            function ($table) {
                $table->bigIncrements('installment_id');
                $table->integer('loan_id')->index();
                $table->integer('lender_installment_id')->index();
                $table->integer('seq_num');
                $table->date('due_date')->index();
                $table->tinyInteger('paid')->default(0);
                $table->timestamp('paid_at', 0)->nullable();

                $table->integer('original_currency_id')->unsigned()->nullable();
                $table->decimal('original_remaining_principal', 11, 2)->nullable();
                $table->decimal('original_principal', 11, 2)->nullable();
                $table->decimal('original_interest', 11, 2)->nullable();

                $table->integer('currency_id')->unsigned()->nullable();
                $table->decimal('remaining_principal', 11, 2);
                $table->decimal('principal', 11, 2);
                $table->decimal('accrued_interest', 11, 2)->default(0);
                $table->decimal('interest', 11, 2);
                $table->decimal('late_interest', 11, 2)->nullable();
                $table->decimal('total', 11, 2);

                $table->enum('status', Loan::getPaymentStatuses())->index();
                $table->enum('payment_status', Installment::getPaymentStatuses())->index();
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
        Schema::dropIfExists('installment');
    }
}
