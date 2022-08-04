<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class AddTableInvestment extends Migration
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
            'investment',
            function ($table) {
                $table->bigIncrements('investment_id');
                $table->integer('investment_bunch_id')->nullable()->index();
                $table->integer('investor_id')->index();
                $table->integer('wallet_id')->index();
                $table->integer('loan_id')->index();
                $table->decimal('amount', 11, 2)->index();
                $table->double('percent', 11, 12);
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
        Schema::dropIfExists('investment');
    }
}
