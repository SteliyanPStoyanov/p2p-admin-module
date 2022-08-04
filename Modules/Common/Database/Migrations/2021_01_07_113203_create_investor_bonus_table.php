<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestorBonusTable extends Migration
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
            'investor_bonus',
            function ($table) {
                $table->bigIncrements('investor_bonus_id');
                $table->bigInteger('investor_id')->unsigned()->index();
                $table->bigInteger('from_investor_id')->unsigned();
                $table->decimal('amount', 11, 2)->nullable();
                $table->tinyInteger('handled')->default(0)->nullable();
                $table->date('date')->nullable();
                $table->timestamp('handled_at')->useCurrent();

                $table->foreign('investor_id')->references('investor_id')->on('investor')->onDelete('cascade');
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
        Schema::dropIfExists('investor_bonus');
    }
}
