<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\BlockedAmountHistory;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateBlockedAmountHistoryTable extends Migration
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
            'blocked_amount_history',
            function ($table) {
                $table->bigIncrements('blocked_amount_history_id');
                $table->bigInteger('investor_id');
                $table->bigInteger('wallet_id');
                $table->bigInteger('task_id');
                $table->decimal('amount', 11, 2);
                $table->enum('status', BlockedAmountHistory::getStatuses());

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
        Schema::dropIfExists('blocked_amount_history');
    }
}
