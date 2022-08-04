<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\ContractTemplate;

class AddCookieTypeContractTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE contract_template DROP CONSTRAINT contract_template_type_check");
        $types = ContractTemplate::getTypes();
        $result = join( ', ', array_map(function( $value ){ return sprintf("'%s'::character varying", $value); }, $types) );
        DB::statement("ALTER TABLE contract_template add CONSTRAINT contract_template_type_check CHECK (type::text = ANY (ARRAY[$result]::text[]))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
