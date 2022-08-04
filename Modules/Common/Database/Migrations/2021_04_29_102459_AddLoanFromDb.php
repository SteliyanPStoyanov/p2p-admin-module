<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Common\Entities\Loan;

class AddLoanFromDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan', function (Blueprint $table) {
            $table->string('from_db', 50)->default(Loan::DB_SITE);
        });
    }
}
