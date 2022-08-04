<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateWalletRollbackHistoryTable extends Migration
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
            'wallet_rollback_history',
            function ($table) {
                $table->bigIncrements('wallet_rollback_history_id');
                $table->bigInteger('wallet_id');
                $table->bigInteger('investor_id');
                $table->bigInteger('currency_id');
                $table->date('date')->nullable();
                $table->decimal('total_amount', 11, 2)->unsigned()->nullable();
                $table->decimal('invested', 11, 2)->unsigned()->nullable();
                $table->decimal('uninvested', 11, 2)->unsigned()->nullable();
                $table->decimal('deposit', 11, 2)->unsigned()->nullable();
                $table->decimal('withdraw', 11, 2)->unsigned()->nullable();
                $table->decimal('income', 11, 2)->unsigned()->nullable();
                $table->decimal('interest', 11, 2)->unsigned()->nullable();
                $table->decimal('late_interest', 11, 2)->unsigned()->nullable();
                $table->decimal('bonus', 11, 2)->unsigned()->nullable();
                $table->string('type')->unsigned()->nullable();
                $table->tableCrudFields();
                $table->tableArchiveFields();
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
        Schema::dropIfExists('wallet_rollback_history');
    }
}
