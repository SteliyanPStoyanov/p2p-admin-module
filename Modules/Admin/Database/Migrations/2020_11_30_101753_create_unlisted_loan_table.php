<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\UnlistedLoan;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateUnlistedLoanTable extends Migration
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
            'unlisted_loan',
            function ($table) {
                $table->bigIncrements('unlisted_loan_id');
                $table->integer('lender_id')->index();
                $table->tinyInteger('handled')->default(0);
                $table->enum('status', UnlistedLoan::getStatuses())->default('default');

                $table->tableCreateFields(false, true);
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
        Schema::dropIfExists('unlisted_loan');
    }
}
