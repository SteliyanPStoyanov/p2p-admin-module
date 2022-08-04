<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateTableRepaidInstallment extends Migration
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
            'repaid_installment',
            function ($table) {
                $table->bigIncrements('repaid_installment_id');
                $table->integer('lender_id')->index();
                $table->integer('lender_installment_id')->index();
                $table->tinyInteger('handled')->default(0);
                $table->tableCreateFields(false, true);
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
        Schema::dropIfExists('repaid_installment');
    }
}
