<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestStrategyTable extends Migration
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
            'invest_strategy',
            function ($table) {
                $table->bigIncrements('invest_strategy_id');
                $table->bigInteger('investor_id')->unsigned();
                $table->bigInteger('wallet_id')->unsigned();
                $table->string('name');
                $table->integer('priority');
                $table->decimal('min_amount', 11, 2)->nullable();
                $table->decimal('max_amount', 11, 2)->nullable();
                $table->decimal('min_interest_rate', 11, 2)->nullable();
                $table->decimal('max_interest_rate', 11, 2)->nullable();
                $table->integer('min_loan_period')->nullable();
                $table->integer('max_loan_period')->nullable();
                $table->json('loan_type');
                $table->json('loan_payment_status');
                $table->decimal('portfolio_size', 11, 2)->nullable();
                $table->decimal('max_portfolio_size', 11, 2)->nullable();
                $table->decimal('total_invested', 11, 2)->nullable();
                $table->decimal('total_received', 11, 2)->nullable();
                $table->tinyInteger('reinvest')->default(1);
                $table->tinyInteger('include_invested')->default(1);
                $table->tinyInteger('agreed')->enum('agreed',[0,1]);
                $table->foreign('investor_id')->references('investor_id')->on('investor')->onDelete('cascade');
                $table->foreign('wallet_id')->references('wallet_id')->on('wallet')->onDelete('cascade');
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
        Schema::table(
            'invest_strategy',
            function (Blueprint $table) {
                $table->dropForeign('invest_strategy_investor_id_foreign');
                $table->dropForeign('invest_strategy_wallet_id_foreign');
            }
        );

        Schema::dropIfExists('investor_contract');
    }
}
