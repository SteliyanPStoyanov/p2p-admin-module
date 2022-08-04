<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateCartSecondaryLoansTable extends Migration
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
            'cart_secondary_loans',
            function ($table) {
                $table->bigIncrements('cart_loan_id');
                $table->integer('cart_secondary_id');
                $table->integer('secondary_market_id')->unsigned()->default(0);
                $table->integer('loan_id');
                $table->integer('investment_id')->unsigned();
                $table->integer('originator_id')->unsigned();
                $table->decimal('principal_for_sale', 11, 2);
                $table->decimal('premium', 11, 1)->nullable();
                $table->decimal('price', 11, 2)->nullable();
                $table->decimal('percent_on_sell', 11, 2)->nullable();
                $table->decimal('percent_bought', 11, 2)->nullable();
                $table->json('filters')->nullable();
                $table->tinyInteger('status')->default(0); // 0 - error (reason is mandatory), 1 - okay, 2 - on sell, 3 - sold, 4 - bought
                $table->text('reason')->nullable();
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
        Schema::dropIfExists('cart_secondary_loans');
    }
}
