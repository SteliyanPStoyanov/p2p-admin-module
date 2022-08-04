<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateChangeLogTable extends Migration
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
            'change_log',
            function ($table) {
                $table->bigIncrements('log_id');
                $table->bigInteger('investor_id');
                $table->string('key');
                $table->string('old_value')->nullable();
                $table->string('new_value')->nullable();
                $table->enum('user_type', ['investor', 'administrator']);

                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->bigInteger('created_by')
                    ->unsigned()
                    ->nullable();
                $table->enum('created_by_type', ['investor', 'administrator']);
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
        Schema::dropIfExists('change_log');
    }
}
