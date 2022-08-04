<?php

namespace Modules\Admin\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Traits\BaseModelTrait;
use \Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole implements LoggerInterface
{
    use BaseModelTrait;

    public const PRIORITY_MIN = 0;
    public const PRIORITY_MAX = 100;

    /**
     * @var string[]
     */
    protected $with = ['permissions', 'creator', 'updater'];

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'guard_name',
        'priority'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->casts = $this->traitCasts;
    }
}
