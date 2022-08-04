<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\CartSecondary;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreateCartSecondaryTable extends Migration
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
            'cart_secondary',
            function ($table) {
                $table->bigIncrements('cart_secondary_id');
                $table->bigInteger('investor_id');
                $table->enum(
                    'type',
                    [
                        CartSecondary::TYPE_BUYER,
                        CartSecondary::TYPE_SELLER,
                    ]
                )->default(CartSecondary::TYPE_SELLER)->nullable();
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
        Schema::dropIfExists('cart_secondary');
    }

}
