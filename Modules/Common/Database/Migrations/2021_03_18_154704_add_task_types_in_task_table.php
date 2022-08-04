<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Common\Entities\Task;

class AddTaskTypesInTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE task DROP CONSTRAINT task_task_type_check");
        $types = Task::getTypes();
        $result = join( ', ', array_map(function( $value ){ return sprintf("'%s'::character varying", $value); }, $types) );
        DB::statement("ALTER TABLE task add CONSTRAINT task_task_type_check CHECK (task_type::text = ANY (ARRAY[$result]::text[]))");

        Schema::table('task', function (Blueprint $table) {
            $table->integer('investor_id')->unsigned()->nullable()->change();
            $table->integer('imported_payment_id')->unsigned()->nullable();
        });
    }
}
