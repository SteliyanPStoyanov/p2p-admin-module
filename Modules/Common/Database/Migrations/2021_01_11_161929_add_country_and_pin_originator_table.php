<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\Country;

class AddCountryAndPinOriginatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('originator', function (Blueprint $table) {
            $table->bigInteger('country_id')->nullable();
            $table->string('pin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('originator', function (Blueprint $table) {
            $table->dropColumn('country_id');
            $table->dropColumn('pin');
        });
    }
}
