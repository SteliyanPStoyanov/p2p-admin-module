<?php

namespace Modules\Core\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;

class MigrationBlueprint extends Blueprint
{
    /**
     * Add basic columns to table for CRUD operations.
     *
     * active
     * active
     * created_at
     * created_administrator_id
     * updated_at
     * updated_administrator_id
     * deleted_at
     * deleted_administrator_id
     *
     * @param bool $addLast
     * @param int $precision
     *
     * @return void
     */
    public function tableCrudFields($addLast = false, int $precision = 0)
    {
        if ($addLast) {
            $this->smallInteger('last')->default('1')->index();
        }
        $this->tinyInteger('active')->default('1')->index();
        $this->tinyInteger('deleted')->default('0');
        $this->timestamp('created_at', $precision)->nullable()->useCurrent();
        $this->bigInteger('created_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');
        $this->timestamp('updated_at', $precision)->nullable();
        $this->bigInteger('updated_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');
        $this->timestamp('deleted_at', $precision)->nullable();
        $this->bigInteger('deleted_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');

        $this->timestamp('enabled_at', $precision)->nullable();
        $this->bigInteger('enabled_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');

        $this->timestamp('disabled_at', $precision)->nullable();
        $this->bigInteger('disabled_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');
    }

    public function tableCreateFields(
        $addLast = false,
        $addUpdatedFields = false,
        int $precision = 0
    ) {
        if ($addLast) {
            $this->smallInteger('last');
        }

        $this->timestamp('created_at', $precision)->nullable()->useCurrent();
        $this->bigInteger('created_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');

        if ($addUpdatedFields) {
            $this->timestamp('updated_at', $precision)->nullable();
            $this->bigInteger('updated_by')
                ->unsigned()
                ->nullable()
                ->references('administrator_id')
                ->on('administrator');
        }
    }

    public function tableArchiveFields()
    {
        $this->timestamp('archived_at')->nullable();
        $this->bigInteger('archived_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');
    }
}
