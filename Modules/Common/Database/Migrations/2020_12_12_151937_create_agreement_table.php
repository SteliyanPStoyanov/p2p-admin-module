<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\Agreement;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateAgreementTable extends Migration
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
            'agreement',
            function ($table) {
                $table->bigIncrements('agreement_id');
                $table->enum('type', Agreement::getTypes());
                $table->string('name');
                $table->string('description');

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
        Schema::dropIfExists('agreement');
    }
}
