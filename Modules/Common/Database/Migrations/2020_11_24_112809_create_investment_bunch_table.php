<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestmentBunchTable extends Migration
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
            'investment_bunch',
            function ($table) {
                $table->bigIncrements('investment_bunch_id');
                $table->integer('count')->unsigned()->nullable()->default(0);
                $table->integer('investor_id')->unsigned();
                $table->decimal('amount', 11, 2)->nullable()->index();
                $table->json('filters');
                $table->string('finished')->nullable()->default(0);
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
        Schema::dropIfExists('investment_bunch');
    }
}
