<?php

use Modules\Core\Services\StorageService;
use Modules\Core\Traits\CustomSchemaBuilderTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateAdministratorTable extends Migration
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
            'administrator',
            function ($table) {
                $table->bigIncrements('administrator_id');
                $table->string('first_name');
                $table->string('middle_name');
                $table->string('last_name');
                $table->string('phone');
                $table->string('email')->unique();
                $table->string('username')->unique();
                $table->string('password');
                $table->string('avatar', 150)->default(StorageService::DEFAULT_AVATAR_PATH);
                $table->rememberToken();
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
        Schema::dropIfExists('administrator');
    }
}
