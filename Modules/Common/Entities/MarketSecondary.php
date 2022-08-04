<?php
declare(strict_types=1);

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketSecondary extends BaseModel
{
    use SoftDeletes;

    protected $table = 'market_secondary';

    protected $primaryKey = 'market_secondary_id';

    protected $fillable = [
        'investor_id',
        'secondary_loan_on_sale',
        'loan_id',
        'investment_id',
        'originator_id',
        'principal_for_sale',
        'premium',
        'price',
        'percent_sold',
        'active',
    ];

    public function investor(): HasOne
    {
        return $this->hasOne(
            Investor::class,
            'investor_id',
            'investor_id'
        );
    }

    public function loan(): HasOne
    {
        return $this->hasOne(
            Loan::class,
            'loan_id',
            'loan_id'
        );
    }

    public function investment(): HasOne
    {
        return $this->hasOne(
            Investment::class,
            'investment_id',
            'investment_id'
        );
    }

    /**
     * @return HasOne
     */
    public function originator(): HasOne
    {
        return $this->hasOne(
            Originator::class,
            'originator_id',
            'originator_id'
        );
    }

    /**
     * @return HasOne
     */
    public function cartLoan(): HasOne
    {
        return $this->hasOne(
            CartSecondaryLoans::class,
            'secondary_market_id',
            'market_secondary_id'
        );
    }

    public function loanOnSale(): HasOne
    {
        return $this->hasOne(
            CartSecondaryLoans::class,
            'cart_loan_id',
            'secondary_loan_on_sale'
        );
    }

}
