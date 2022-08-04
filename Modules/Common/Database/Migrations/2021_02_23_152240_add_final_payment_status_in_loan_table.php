<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\Loan;

class AddFinalPaymentStatusInLoanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('loan', function (Blueprint $table) {
            $table->enum('final_payment_status', Loan::getFinalPaymentStatuses())->after('payment_status')->nullable();
        });
    }
}
