<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class WalletRollbackHistory extends BaseModel implements LoggerInterface
{
    const TYPE_BEFORE_ROLLBACK = 'before_rollback';
    const TYPE_AFTER_ROLLBACK = 'after_rollback';

    /**
     * @var string
     */
    protected $table = 'wallet_rollback_history';

    /**
     * @var string
     */
    protected $primaryKey = 'wallet_rollback_history_id';

    /**
     * @var string[]
     */

    protected $guarded = [
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet()
    {
        return $this->belongsTo(
            Wallet::class,
            'wallet_id',
            'wallet_id'
        );
    }

}
