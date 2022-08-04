<?php

namespace Modules\Core\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;

class MigrationPivotBlueprint extends Blueprint
{
    /**
     * @param bool $addLast
     * @param int $precision
     *
     * @return void
     */
    public function tableCrudFields(
        $addLast = false,
        $addTimestamps = false,
        int $precision = 0
    )
    {
        if ($addLast) {
            $this->smallInteger('last')->index();
        }

        if ($addTimestamps) {
            $this->tinyInteger('deleted')->default('0');
            $this->timestamp('created_at', $precision)->nullable();
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
        }
    }

    public function tableCreateFields(
        $addLast = false,
        int $precision = 0
    )
    {
        if ($addLast) {
            $this->smallInteger('last');
        }

        $this->timestamp('created_at', $precision)->nullable();
        $this->bigInteger('created_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');
    }

    public function tableCreateFieldsHistory(
        $addLast = false,
        int $precision = 0
    )
    {
        if ($addLast) {
            $this->smallInteger('last');
        }

        $this->timestamp('created_at', $precision)->nullable();
        $this->bigInteger('created_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');
        $this->timestamp('archived_at')->nullable();
        $this->bigInteger('archived_by')
            ->unsigned()
            ->nullable()
            ->references('administrator_id')
            ->on('administrator');
    }
}
