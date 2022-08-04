<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateVerificationTable extends Migration
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
            'verification',
            function ($table) {
                $table->bigIncrements('verification_id');
                $table->bigInteger('investor_id');
                $table->string('comment')->nullable();
                $table->smallInteger('name')->nullable();
                $table->smallInteger('birth_date')->nullable();
                $table->smallInteger('address')->nullable();
                $table->smallInteger('citizenship')->nullable();
                $table->smallInteger('photo')->nullable();
                $table->foreign('investor_id')->references('investor_id')->on('investor');

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
        Schema::table(
            'verification',
            function (Blueprint $table) {
                $table->dropForeign('verification_investor_id_foreign');
            }
        );

        Schema::dropIfExists('verification');
    }
}
