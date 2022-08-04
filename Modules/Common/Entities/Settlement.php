<?php

namespace Modules\Common\Entities;

use Modules\Core\Interfaces\LoggerInterface;
use Modules\Core\Models\BaseModel;

class Settlement extends BaseModel implements LoggerInterface
{
    protected $table = 'settlement';
    protected $primaryKey = 'settlement_id';

    protected $fillable = [
        'date',
        'originator_id',
        'currency_id',
        'total_invested_amount',
        'net_invested_amount',
        'avg_investment',
        'investments_count',
        'rebuy_principal',
        'rebuy_interest',
        'rebuy_late_interest',
        'repaid_principal',
        'repaid_interest',
        'repaid_late_interest',
        'net_settlement',
        'open_balance',
        'close_balance',
    ];
}
