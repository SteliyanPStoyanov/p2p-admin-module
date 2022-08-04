<?php

namespace Modules\Core\Models;

use Jenssegers\Mongodb\Eloquent\Model;

abstract class BaseLoggerModel extends Model
{
    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * @var string[]
     */
    protected $fillable = [
        'model',
        'action',
        'object_prev_state',
        'object_cur_state',
        'changes',
        'administrator_id',
        'table',
    ];

    /**
     * @var string[]
     */
    protected $dates = ['created_at'];

    public function log(array $log): BaseLoggerModel
    {
        $this->fill($log);
        $this->save();

        return $this;
    }
}
