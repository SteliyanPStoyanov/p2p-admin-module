<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateAffiliateStatsTable extends Migration
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
            'affiliate_stats',
            function ($table) {
                $table->bigIncrements('affiliate_stats_id');
                $table->bigInteger('investor_id')->unsigned()->nullable();
                $table->bigInteger('affiliate_id')->unsigned();
                $table->json('send_data')->nullable();
                $table->string('api_address')->nullable();
                $table->json('response')->nullable();
                $table->timestamp('send_at')->nullable();
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
        Schema::dropIfExists('affiliate_stats');
    }
}
