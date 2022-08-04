<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\Task;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateTaskTable extends Migration
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
            'task',
            function ($table) {
                $table->bigIncrements('task_id');
                $table->enum('task_type', [Task::TASK_TYPE_VERIFICATION, Task::TASK_TYPE_WITHDRAW, Task::TASK_TYPE_BONUS_PAYMENT]);
                $table->integer('investor_id')->unsigned()->index();
                $table->integer('wallet_id')->unsigned()->nullable();
                $table->integer('currency_id')->unsigned()->nullable();
                $table->integer('bank_account_id')->unsigned()->nullable();
                $table->decimal('amount', 11, 2)->nullable();
                $table->enum('status', [Task::TASK_STATUS_NEW, Task::TASK_STATUS_PROCESSING, Task::TASK_STATUS_DONE ,Task::TASK_STATUS_CANCEL]);
                $table->timestamp('processing_at')->nullable();
                $table->integer('processing_by')->nullable();
                $table->timestamp('done_at')->nullable();
                $table->integer('done_by')->nullable();
                $table->integer('time_spent')->nullable();
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
        Schema::dropIfExists('task');
    }
}
