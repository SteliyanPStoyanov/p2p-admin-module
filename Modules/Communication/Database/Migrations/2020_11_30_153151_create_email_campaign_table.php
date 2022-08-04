<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Communication\Entities\Email;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateEmailCampaignTable extends Migration
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
            'email_campaign',
            function ($table) {
                $table->bigIncrements('email_campaign_id');
                $table->string('name');
                $table->integer('email_template_id')->unsigned();
                $table->integer('email_source_id')->unsigned();
                $table->enum('type', Email::getEmailTypes())->nullable();
                $table->string('sender_email');
                $table->string('sender_name');
                $table->string('reply_email');
                $table->string('reply_name');
                $table->datetime('start_at');
                $table->datetime('end_at');
                $table->json('period');
                $table->json('products');
                $table->timestamp('last_send_date');
                $table->foreign('email_template_id')->references('email_template_id')->on('email_template');
                $table->foreign('email_source_id')->references('email_source_id')->on('email_source');
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
            'email_campaign',
            function (Blueprint $table) {
                $table->dropForeign('email_campaign_email_template_id_foreign');
                $table->dropForeign('email_campaign_email_source_id_foreign');
            }
        );
        Schema::dropIfExists('email_campaign');
    }
}
