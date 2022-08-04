<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class RestoreHash extends BaseModel implements LoggerInterface
{
    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'restore_hash';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string[]
     */
    protected $fillable = [
        'hash',
        'investor_id',
        'valid_till',
        'used',
    ];
}
