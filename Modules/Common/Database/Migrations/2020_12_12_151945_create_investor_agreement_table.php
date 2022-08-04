<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestorAgreementTable extends Migration
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
            'investor_agreement',
            function ($table) {
                $table->bigIncrements('investor_agreement_id');
                $table->bigInteger('agreement_id');
                $table->bigInteger('investor_id');
                $table->tinyInteger('value');

                $table->foreign('investor_id')->references('investor_id')->on('investor')->onDelete('cascade');
                $table->foreign('agreement_id')->references('agreement_id')->on('agreement')->onDelete('cascade');

                $table->tableCreateFields();
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
            'investor_agreement',
            function (Blueprint $table) {
                $table->dropForeign('investor_agreement_investor_id_foreign');
                $table->dropForeign('investor_agreement_agreement_id_foreign');
            }
        );

        Schema::dropIfExists('investor_agreement');
    }
}
