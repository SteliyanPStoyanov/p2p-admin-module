<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Common\Entities\Loan;

class AddParentIdToInvestmentDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // in order to track the sale of an investment
        Schema::table('investment', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned()->nullable();
        });
    }
}