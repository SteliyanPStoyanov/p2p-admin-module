<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\Models\BaseModel;

class InvestStrategyHistory extends BaseModel
{
    protected $table = 'invest_strategy_history';

    protected $primaryKey = 'invest_strategy_history_id';

    protected $fillable = [
        "invest_strategy_id",
        "investor_id",
        "wallet_id",
        "name",
        "priority",
        "min_amount",
        "max_amount",
        "min_interest_rate",
        "max_interest_rate",
        "min_loan_period",
        "max_loan_period",
        "loan_type",
        "loan_payment_status",
        "portfolio_size",
        "max_portfolio_size",
        "reinvest",
        "include_invested",
        "agreed",
        "active",
        "deleted",
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
        "archived_at",
        "archived_by"
    ];

     /**
     * @return BelongsTo
     */
    public function investor()
    {
        return $this->belongsTo(
            Investor::class,
            'investor_id',
            'investor_id'
        );
    }
}
