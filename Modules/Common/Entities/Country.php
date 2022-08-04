<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Country extends BaseModel implements LoggerInterface
{
    public const ID_BG = '38';

    /**
     * @var string
     */
    protected $table = 'country';

    /**
     * @var string
     */
    protected $primaryKey = 'country_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'country_id',
        'active',
        'deleted',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'enabled_at',
        'enabled_by',
        'disabled_at',
        'disabled_by',
    ];
}
