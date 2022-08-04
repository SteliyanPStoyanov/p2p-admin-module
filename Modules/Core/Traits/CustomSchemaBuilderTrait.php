<?php

namespace Modules\Core\Traits;

use Modules\Core\Database\Migrations\MigrationBlueprint;
use Modules\Core\Database\Migrations\MigrationPivotBlueprint;

trait CustomSchemaBuilderTrait
{
    /**
     * Add custom blueprint to schema builder
     * Which give us adding defaults behaviour to all migrations
     *
     * @param $schema
     * @return Illuminate\Database\Schema\[Mysql|Postgres Builder]
     */
    public function getCustomSchemaBuilder($schema)
    {
        $schema->blueprintResolver(function ($table, $callback) {
            return new MigrationBlueprint($table, $callback);
        });

        return $schema;
    }

    /**
     * @param $schema
     * @return mixed
     */
    public function getCustomPivotSchemaBuilder($schema)
    {
        $schema->blueprintResolver(function ($table, $callback) {
            return new MigrationPivotBlueprint($table, $callback);
        });

        return $schema;
    }
}
