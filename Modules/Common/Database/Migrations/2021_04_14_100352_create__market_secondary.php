<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateMarketSecondary extends Migration
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
            'market_secondary',
            function ($table) {
                $table->bigIncrements('market_secondary_id');
                $table->integer('secondary_loan_on_sale')->unsigned();
                $table->bigInteger('investor_id');
                $table->integer('loan_id');
                $table->integer('investment_id')->unsigned();
                $table->integer('originator_id')->unsigned();
                $table->integer('secondary_market_id')->unsigned()->default('0');
                $table->decimal('principal_for_sale', 11, 2);
                $table->decimal('premium', 3, 1)->nullable();
                $table->decimal('price', 11, 2)->nullable();
                $table->decimal('percent_sold', 3, 2)->nullable();
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
        Schema::dropIfExists('market_secondary_loans');
    }
}
