<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestorCompanyTable extends Migration
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
            'investor_company',
            function ($table) {
                $table->bigIncrements('investor_company_id');
                $table->bigInteger('investor_id')->unsigned();
                $table->string('name')->nullable();
                $table->string('number')->nullable();
                $table->string('address')->nullable();
                $table->bigInteger('country_id')->unsigned()->nullable();
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
            'investor_company',
            function (Blueprint $table) {
                $table->dropForeign('investor_company_investor_id_foreign');
            }
        );
        Schema::dropIfExists('investor_company');
    }
}
