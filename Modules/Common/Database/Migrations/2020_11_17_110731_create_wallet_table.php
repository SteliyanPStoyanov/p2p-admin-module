<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateWalletTable extends Migration
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
            'wallet',
            function ($table) {
                $table->bigIncrements('wallet_id');
                $table->bigInteger('investor_id');
                $table->bigInteger('currency_id');
                $table->date('date')->nullable();
                $table->decimal('total_amount', 11, 2)->unsigned()->default(0.00);
                $table->decimal('invested', 11, 2)->unsigned()->default(0.00);
                $table->decimal('uninvested', 11, 2)->unsigned()->default(0.00);
                $table->decimal('deposit', 11, 2)->unsigned()->default(0.00);
                $table->decimal('withdraw', 11, 2)->unsigned()->default(0.00);
                $table->decimal('income', 11, 2)->unsigned()->default(0.00);
                $table->decimal('interest', 11, 2)->unsigned()->default(0.00);
                $table->decimal('late_interest', 11, 2)->unsigned()->default(0.00);
                $table->decimal('bonus', 11, 2)->unsigned()->default(0.00);

                $table->unique(['investor_id', 'currency_id']);
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
        Schema::dropIfExists('wallet');
    }
}
