<?php

use Illuminate\Database\Migrations\Migration;

class CreateMongoDbCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::connection('mongodb')->table('investor_log_collection', function ($collection) {
        //     $collection->index('created_at');
        //     $collection->index('table');
        //     $collection->index('investor_id');
        //     $collection->index('loan_id');
        //     $collection->index('action');
        // });
        // Schema::connection('mongodb')->table('pivot_log_collection', function ($collection) {
        //     $collection->index('created_at');
        //     $collection->index('table');
        //     $collection->index('investor_id');
        //     $collection->index('loan_id');
        //     $collection->index('action');
        // });
        // Schema::connection('mongodb')->table('default_log_collection', function ($collection) {
        //     $collection->index('created_at');
        //     $collection->index('table');
        //     $collection->index('investor_id');
        //     $collection->index('loan_id');
        //     $collection->index('action');
        // });
    }
}
