<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Communication\Entities\Email;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateEmailTable extends Migration
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
            'email',
            function ($table) {
                $table->bigIncrements('email_id');
                $table->integer('email_template_id')->unsigned()->index();
                $table->integer('email_campaign_id')->unsigned()->nullable();
                $table->integer('investor_id')->unsigned()->index();
                $table->string('identifier')->nullable();
                $table->string('sender_from')->nullable();
                $table->string('sender_to')->nullable();
                $table->string('sender_reply')->nullable();
                $table->string('title')->nullable();
                $table->string('body')->nullable();
                $table->text('text')->nullable();
                $table->string('response')->nullable();
                $table->string('queue')->nullable();
                $table->timestamp('queued_at')->nullable();
                $table->integer('tries')->default(0)->unsigned();
                $table->timestamp('send_at')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamp('opened_at')->nullable();
                $table->smallInteger('has_files')->nullable();
                $table->foreign('investor_id')->references('investor_id')->on('investor');
                $table->foreign('email_template_id')->references('email_template_id')->on('email_template');
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
            'email',
            function (Blueprint $table) {
                $table->dropForeign('email_email_template_id_foreign');
                $table->dropForeign('email_investor_id_foreign');
            }
        );
        Schema::dropIfExists('email');
    }
}
