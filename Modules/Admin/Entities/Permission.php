<?php

namespace Modules\Admin\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Traits\BaseModelTrait;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission implements LoggerInterface
{
    use BaseModelTrait;

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'module',
        'controller',
        'action',
        'active',
        'created_by',
    ];
}
