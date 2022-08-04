<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\Investor;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateInvestorTable extends Migration
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
            'investor',
            function ($table) {
                $table->bigIncrements('investor_id');
                $table->string('email')->unique();
                $table->string('password')->nullable();
                $table->string('first_name')->nullable();
                $table->string('middle_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('phone')->nullable();
                $table->date('birth_date')->nullable();
                $table->integer('citizenship')->nullable();
                $table->integer('residence')->nullable();
                $table->string('city')->nullable();
                $table->string('postcode')->nullable();
                $table->string('address')->nullable();
                $table->string('comment')->nullable();
                $table->string('email_notification')->nullable();
                $table->enum(
                    'type',
                    [
                        Investor::TYPE_INDIVIDUAL,
                        Investor::TYPE_COMPANY
                    ]
                )->default(Investor::TYPE_INDIVIDUAL)->nullable();
                $table->tinyInteger('political')->nullable();
                $table->tinyInteger('locale_id')->nullable()->default(1);
                $table->enum(
                    'status',
                    [
                        Investor::INVESTOR_STATUS_UNREGISTERED,
                        Investor::INVESTOR_STATUS_REGISTERED,
                        Investor::INVESTOR_STATUS_AWAITING_VERIFICATION,
                        Investor::INVESTOR_STATUS_VERIFIED,
                        Investor::INVESTOR_STATUS_REJECTED_VERIFICATION,
                        Investor::INVESTOR_STATUS_AWAITING_DOCUMENTS
                    ]
                )->nullable();
                $table->json('verification_data')->nullable();
                $table->string('referral_hash')->nullable();
                $table->integer('referral_id')->nullable();
                $table->timestamp('unregistered_recall_at')->nullable()->index();
                $table->timestamp('registered_recall_at')->nullable()->index();
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
        Schema::dropIfExists('investor');
    }
}
