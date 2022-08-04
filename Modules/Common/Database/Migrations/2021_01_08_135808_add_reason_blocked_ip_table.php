<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReasonBlockedIpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'blocked_ip',
            function (Blueprint $table) {
                $table->string('reason')->unsigned()
                    ->nullable()->after('blocked_till');
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
            'blocked_ip',
            function (Blueprint $table) {
                $table->dropColumn('reason');
            }
        );
    }
}
