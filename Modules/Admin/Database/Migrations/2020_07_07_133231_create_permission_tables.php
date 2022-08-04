<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Core\Database\Migrations\MigrationBlueprint;
use Modules\Core\Database\Migrations\MigrationPivotBlueprint;
use Modules\Core\Traits\CustomSchemaBuilderTrait;

class CreatePermissionTables extends Migration
{
    use CustomSchemaBuilderTrait;

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        if (empty($tableNames)) {
            throw new \Exception(
                'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.'
            );
        }

        //table permission
        $this->getCustomSchemaBuilder(DB::getSchemaBuilder())->create(
            $tableNames['permissions'],
            function (MigrationBlueprint $table) use ($columnNames) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->string('description');
                $table->string('module');
                $table->string('controller');
                $table->string('action');
                $table->tableCrudFields();
                $table->index('name');
            }
        );

        //table role
        $this->getCustomSchemaBuilder(DB::getSchemaBuilder())->create(
            $tableNames['roles'],
            function (MigrationBlueprint $table) use ($columnNames) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->integer('priority');
                $table->tableCrudFields();
            }
        );

        //table administrator_permission
        $this->getCustomPivotSchemaBuilder(DB::getSchemaBuilder())->create(
            $tableNames['model_has_permissions'],
            function (MigrationPivotBlueprint $table) use ($tableNames, $columnNames) {
                $table->unsignedBigInteger($columnNames['permission_id']);

                $table->string('model_type');
                $table->unsignedBigInteger($columnNames['model_morph_key']);
                $table->index(
                    [$columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_model_id_model_type_index'
                );

                $table->foreign($columnNames['permission_id'])
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade');

                $table->foreign($columnNames['model_morph_key'])
                    ->references($columnNames['model_morph_key'])
                    ->on($tableNames['administrator_table'])
                    ->onDelete('cascade');

                $table->primary(
                    [$columnNames['permission_id'], $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );

                $table->tableCrudFields();
            }
        );

        //table administrator_role
        $this->getCustomPivotSchemaBuilder(DB::getSchemaBuilder())->create(
            $tableNames['model_has_roles'],
            function (MigrationPivotBlueprint $table) use ($tableNames, $columnNames) {
                $table->unsignedBigInteger($columnNames['role_id']);

                $table->string('model_type');
                $table->unsignedBigInteger($columnNames['model_morph_key']);
                $table->index(
                    [$columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_model_id_model_type_index'
                );

                $table->foreign($columnNames['role_id'])
                    ->references('id')
                    ->on($tableNames['roles'])
                    ->onDelete('cascade');

                $table->foreign($columnNames['model_morph_key'])
                    ->references($columnNames['model_morph_key'])
                    ->on($tableNames['administrator_table'])
                    ->onDelete('cascade');

                $table->primary(
                    [$columnNames['role_id'], $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );

                $table->tableCrudFields();
            }
        );

        //Table permission_role
        $this->getCustomPivotSchemaBuilder(DB::getSchemaBuilder())->create(
            $tableNames['role_has_permissions'],
            function (MigrationPivotBlueprint $table) use ($tableNames, $columnNames) {
                $table->unsignedBigInteger($columnNames['permission_id']);
                $table->unsignedBigInteger($columnNames['role_id']);

                $table->foreign($columnNames['permission_id'])
                    ->references('id')
                    ->on($tableNames['permissions'])
                    ->onDelete('cascade');

                $table->foreign($columnNames['role_id'])
                    ->references('id')
                    ->on($tableNames['roles'])
                    ->onDelete('cascade');

                $table->primary([$columnNames['permission_id'], $columnNames['role_id']], 'role_has_permissions_permission_id_role_id_primary');
                $table->tableCrudFields();
            }
        );

        app('cache')
            ->store(config()->has('permission.cache.store') ? config('permission.cache.store') : null);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     *
     * @throws Exception
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception(
                'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.'
            );
        }

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
