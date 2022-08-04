<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Common\Entities\Loan;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateLoanTable extends Migration
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
            'loan',
            function ($table) {
                $table->bigIncrements('loan_id');
                $table->integer('originator_id')->unsigned()->index();
                $table->integer('lender_id')->unsigned();
                $table->enum('type', ['payday', 'installments']);
                $table->tinyInteger('from_office')->default(0)->index();
                $table->integer('country_id')->unsigned();
                $table->date('lender_issue_date');
                $table->date('final_payment_date');
                $table->integer('prepaid_schedule_payments')->unsigned()->default(0)->index();
                $table->integer('period');

                $table->integer('original_currency_id')->unsigned()->nullable();
                $table->decimal('original_amount', 11, 2)->nullable();
                $table->decimal('original_amount_afranga', 11, 2)->nullable();
                $table->decimal('original_amount_available', 11, 2)->nullable();
                $table->decimal('original_remaining_principal', 11, 2)->nullable();

                $table->integer('currency_id')->unsigned();
                $table->decimal('amount', 11, 2)->nullable();
                $table->decimal('amount_afranga', 11, 2)->nullable();
                $table->decimal('amount_available', 11, 2)->nullable();
                $table->decimal('remaining_principal', 11, 2)->nullable();

                $table->decimal('interest_rate_percent', 11, 2)->nullable();
                $table->decimal('assigned_origination_fee_share', 11, 2)->nullable();

                $table->tinyInteger('buyback')->default(1);
                $table->integer('contract_tempate_id')->unsigned()->nullable();
                $table->integer('borrower_age')->unsigned()->nullable();
                $table->enum('borrower_gender', Loan::getGenders());
                $table->enum('status', Loan::getStatuses())->index();
                $table->enum('payment_status', Loan::getPaymentStatuses())->index();
                $table->tinyInteger('blocked')->default(0);
                $table->tinyInteger('unlisted')->default(0)->index();
                $table->timestamp('unlisted_at', 0)->nullable();
                $table->integer('overdue_days')->unsigned()->default(0)->index();
                $table->timestamp('payment_status_updated_at')->nullable()->index();
                $table->timestamp('interest_updated_at')->nullable()->index();

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
        Schema::dropIfExists('loan');
    }
}
