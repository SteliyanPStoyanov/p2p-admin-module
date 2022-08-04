<?php

namespace Modules\Core\Models;

class PivotLogger extends BaseLoggerModel
{
    /**
     * @var string
     */
    protected $collection = 'pivot_log_collection';

    /**
     * @var string[]
     */
    protected $fillable = [
        'table',
        'model',
        'model_id',
        'relation',
        'attached',
        'detached',
        'updated',
        'administrator_id',
    ];
}
