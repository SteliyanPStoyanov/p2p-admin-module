<?php

namespace Modules\Common\Entities;

use Modules\Core\Models\BaseModel;

class PortfolioHistory extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'portfolio_history';

    /**
     * @var string
     */
    protected $primaryKey = 'portfolio_history_id';

    /**
     * @var string[]
     */
    protected $guarded = [
        'portfolio_history_id',
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
        'archived_at',
        'archived_by'
    ];

}
