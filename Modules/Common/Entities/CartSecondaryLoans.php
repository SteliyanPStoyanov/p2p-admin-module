<?php
declare(strict_types=1);

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Modules\Core\Models\BaseModel;

class CartSecondaryLoans extends BaseModel
{
    use SoftDeletes;

    const LOAN_STATUS_ERROR = 0;
    const LOAN_STATUS_OKAY = 1;
    const LOAN_STATUS_ON_SELL = 2;
    const LOAN_STATUS_SOLD = 3;
    const LOAN_STATUS_BOUGHT = 4;

    protected $table = 'cart_secondary_loans';

    protected $primaryKey = 'cart_loan_id';

    protected $fillable = [
        'cart_secondary_id',
        'loan_id',
        'investment_id',
        'originator_id',
        'secondary_market_id',
        'principal_for_sale',
        'premium',
        'price',
        'percent_on_sell',
        'percent_bought',
        'status', // 0 - error (reason is mandatory), 1 - okay, 2 - on sell, 3 - sold, 4 - bought
        'reason',
    ];

    /**
     * @return CartSecondary
     */
    public function cart(): CartSecondary
    {
        return $this->belongsTo(
            CartSecondary::class,
            'cart_secondary_id',
            'cart_secondary_id'
        )->getRelated();
    }

    /**
     * @return HasOne
     */
    public function cartForInvestments(): HasOne
    {
        return $this->hasOne(
            CartSecondary::class,
            'cart_secondary_id',
            'cart_secondary_id'
        );
    }

    /**
     * @return HasOne
     */
    public function loan(): HasOne
    {
        return $this->hasOne(
            Loan::class,
            'loan_id',
            'loan_id'
        );
    }


    /**
     * @return HasOne
     */
    public function originator(): HasOne
    {
        return $this->hasOne(Originator::class, 'originator_id', 'originator_id');
    }


    /**
     * @return HasOne
     */
    public function investment(): HasOne
    {
        return $this->hasOne(
            Investment::class,
            'investment_id',
            'investment_id'
        );
    }

    public function marketSecondary(): HasOne
    {
        return $this->hasOne(
            MarketSecondary::class,
            'market_secondary_id',
            'secondary_market_id'
        );
    }

}
