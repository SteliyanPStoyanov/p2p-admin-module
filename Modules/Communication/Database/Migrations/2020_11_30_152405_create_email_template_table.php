<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Communication\Entities\Email;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateEmailTemplateTable extends Migration
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
            'email_template',
            function ($table) {
                $table->bigIncrements('email_template_id');
                $table->string('key');
                $table->string('description');
                $table->json('variables')->nullable();
                $table->string('title');
                $table->string('body');
                $table->text('text');
                $table->enum('gender', config('communication.gender'))->nullable();
                $table->enum('type', Email::getEmailTypes())->nullable();
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
        Schema::dropIfExists('email_template');
    }
}
