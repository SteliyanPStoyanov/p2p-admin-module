<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\ContractTemplate;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateContractTemplateTable extends Migration
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
            'contract_template',
            function ($table) {
                $table->bigIncrements('contract_template_id');
                $table->enum('type', ContractTemplate::getTypes());
                $table->string('name');
                $table->string('version');
                $table->text('text');
                $table->json('variables');
                $table->date('start_date');

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
        Schema::dropIfExists('contract_template');
    }
}
