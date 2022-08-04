<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\ImportedPayment;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateImportedPaymentTable extends Migration
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
            'imported_payment',
            function ($table) {
                $table->bigIncrements('imported_payment_id');
                $table->string('bank_transaction_id')->index();
                $table->datetime('bank_transaction_date');
                $table->text('basis');
                $table->bigInteger('currency_id')->unsigned();
                $table->decimal('amount', 11, 2);
                $table->enum('type', ImportedPayment::getTypes());
                $table->enum('status', ImportedPayment::getStatuses());

                $table->string('iban')->nullable();
                $table->string('bic')->nullable();

                $table->bigInteger('transaction_id')->unsigned()->nullable();
                $table->bigInteger('investor_id')->unsigned()->nullable();
                $table->bigInteger('wallet_id')->unsigned()->nullable();

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
        Schema::dropIfExists('imported_payment');
    }
}
