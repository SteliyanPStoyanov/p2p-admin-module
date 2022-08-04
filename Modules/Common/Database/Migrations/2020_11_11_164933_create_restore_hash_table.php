<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateRestoreHashTable extends Migration
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
            'restore_hash',
            function ($table) {
                $table->bigIncrements('id');
                $table->string('hash');
                $table->integer('investor_id');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('valid_till');
                $table->tinyInteger('used')->default(1);
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
        Schema::dropIfExists('restore_hash');
    }
}
