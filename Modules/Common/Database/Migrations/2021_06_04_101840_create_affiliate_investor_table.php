<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateAffiliateInvestorTable extends Migration
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
            'affiliate_investor',
            function ($table) {
                $table->bigIncrements('affiliate_investor_id');
                $table->bigInteger('investor_id')->unsigned()->nullable();
                $table->bigInteger('affiliate_id')->unsigned();
                $table->string('client_id')->unsigned();
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
        Schema::dropIfExists('affiliate_investor');
    }
}
