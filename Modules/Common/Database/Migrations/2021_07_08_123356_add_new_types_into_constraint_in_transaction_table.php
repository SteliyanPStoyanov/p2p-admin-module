<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewTypesIntoConstraintInTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Need this field for secondary_market purpose
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE transaction DROP CONSTRAINT transaction_type_check');
        DB::statement("
            ALTER TABLE
                    transaction
            ADD CONSTRAINT
                    transaction_type_check
            CHECK (
                    (
                        (type)::text = ANY
                        (
                            (
                                ARRAY[
                                    'bonus'::character varying,
                                    'deposit'::character varying,
                                    'early_repayment'::character varying,
                                    'installment_repayment'::character varying,
                                    'investment'::character varying,
                                    'repayment'::character varying,
                                    'withdraw'::character varying,
                                    'buyback_manual'::character varying,
                                    'buyback_overdue'::character varying,
                                    'sm_buy'::character varying,
                                    'sm_sell'::character varying,
                                    'sm_premium'::character varying
                                ]
                            )::text[]
                        )
                    )
            );
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
