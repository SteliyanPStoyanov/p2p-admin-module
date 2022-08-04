<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBlockedAmountInWalletTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallet', function (Blueprint $table) {
            $table->decimal('blocked_amount', 11, 2)->default(0);
        });
        Schema::table('wallet_history', function (Blueprint $table) {
            $table->decimal('blocked_amount', 11, 2)->default(0);
        });
    }
}
