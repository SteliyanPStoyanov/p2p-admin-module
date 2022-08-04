<?php

namespace Modules\Common\Entities;

use Modules\Common\Interfaces\HistoryInterface;
use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class BankAccount extends BaseModel implements HistoryInterface, LoggerInterface
{
    /**
     * @var string
     */
    protected $table = 'bank_account';

    /**
     * @var string
     */
    protected $primaryKey = 'bank_account_id';

    protected $historyClass = ChangeLog::class;

    /**
     * @var string[]
     */
    protected $fillable = [
        'investor_id',
        'iban',
        'default',
    ];

     /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function investor()
    {
        return $this->belongsTo(
            Investor::class,
            'investor_id',
            'investor_id'
        )->orderByDesc('bank_account_id');
    }
}
