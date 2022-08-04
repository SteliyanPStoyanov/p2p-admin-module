<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\Transaction;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateTransactionTable extends Migration
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
            'transaction',
            function ($table) {
                $table->bigIncrements('transaction_id');
                $table->bigInteger('task_id')->nullable()->index();
                $table->string('bank_transaction_id', 255)->nullable()->index();
                $table->bigInteger('loan_id')->nullable()->index();
                $table->bigInteger('originator_id')->nullable()->index();
                $table->bigInteger('investor_id')->nullable()->index();
                $table->bigInteger('wallet_id')->nullable()->index();
                $table->bigInteger('currency_id')->nullable()->index();
                $table->string('bank_account_id', 255)->nullable()->index();
                $table->string('details', 255)->nullable();
                $table->enum('direction', Transaction::getDirections())->nullable()->index();
                $table->enum('type', Transaction::getTypes())->nullable()->index();
                $table->decimal('amount', 11, 2)->default(0.00);
                $table->decimal('principal', 11, 2)->nullable()->default(0.00);
                $table->decimal('accrued_interest', 11, 2)->nullable()->default(0.00);
                $table->decimal('interest', 11, 2)->nullable()->default(0.00);
                $table->decimal('late_interest', 11, 2)->nullable()->default(0.00);
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
        Schema::dropIfExists('transaction');
    }
}
