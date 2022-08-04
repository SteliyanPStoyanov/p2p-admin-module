<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\ContractTemplate;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateSettlementTable extends Migration
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
            'settlement',
            function ($table) {
                $table->bigIncrements('settlement_id');
                $table->date('date');
                $table->integer('originator_id')->unsigned()->nullable();
                $table->integer('currency_id')->unsigned()->nullable();

                $table->decimal('total_invested_amount', 11, 2)->nullable();
                $table->decimal('net_invested_amount', 11, 2)->nullable();
                $table->decimal('avg_investment', 11, 2)->nullable();
                $table->integer('investments_count')->unsigned()->nullable();

                $table->decimal('rebuy_principal', 11, 2)->nullable();
                $table->decimal('rebuy_interest', 11, 2)->nullable();
                $table->decimal('rebuy_late_interest', 11, 2)->nullable();

                $table->decimal('repaid_principal', 11, 2)->nullable();
                $table->decimal('repaid_interest', 11, 2)->nullable();
                $table->decimal('repaid_late_interest', 11, 2)->nullable();

                $table->decimal('net_settlement', 11, 2)->nullable();
                $table->decimal('open_balance', 11, 2)->nullable();
                $table->decimal('close_balance', 11, 2)->nullable();
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
        Schema::dropIfExists('settlement');
    }
}
