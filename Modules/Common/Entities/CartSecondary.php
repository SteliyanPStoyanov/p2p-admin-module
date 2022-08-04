<?php

namespace Modules\Common\Entities;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Modules\Core\Models\BaseModel;
use \Illuminate\Database\Eloquent\Relations\HasOne;

class CartSecondary extends BaseModel
{
    public const TYPE_SELLER = 'sell';
    public const TYPE_BUYER = 'buy';

    protected $table = 'cart_secondary';

    protected $primaryKey = 'cart_secondary_id';

    protected $fillable = [
        'investor_id',
        'type',
    ];

    /**
     * @return string[]
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_SELLER,
            self::TYPE_BUYER,
        ];
    }

    /**
     * @return Collection
     */
    public function loans(): Collection
    {
        return $this->hasMany(
            CartSecondaryLoans::class,
            'cart_secondary_id',
            'cart_secondary_id'
        )->get();
    }

    /**
     * @return HasMany
     */
    public function loansForInvestment(): HasMany
    {
        return $this->hasMany(
            CartSecondaryLoans::class,
            'cart_secondary_id',
            'cart_secondary_id'
        );
    }

    /**
     * @return HasOne
     */
    public function investor(): HasOne
    {
        return $this->hasOne(
            Investor::class,
            'investor_id',
            'investor_id'
        );
    }

    /**
     * @return CartSecondary
     */
    public function getBlockedCart(): CartSecondary
    {
        return CartSecondary::where(
            [
                'cart_secondary_id' => $this->cart_secondary_id,
            ]
        )->lockForUpdate()->first();
    }

    /**
     * @return mixed
     */
    public function getBlockedCartLoans()
    {
        return CartSecondaryLoans::where(
            [
                'cart_secondary_id' => $this->cart_secondary_id,
                'status' => 1,
                'active' => 1,
                'deleted' => 0,
                ['secondary_market_id', '>', 0],
            ]
        )->lockForUpdate()->get();
    }

    /**
     * @param array $loanIds
     * @return mixed
     */
    public function getBlockedCartLoansByIds(array $loanIds)
    {
        return CartSecondaryLoans::whereIn('cart_loan_id', $loanIds)
            ->lockForUpdate()
            ->get()
            ->all();
    }

    /**
     * @param int $loanId
     * @return mixed
     */
    public function getBlockedCartLoan(int $loanId)
    {
        return CartSecondaryLoans::where(
            [
                'cart_loan_id' => $loanId
            ]
        )->lockForUpdate()->get();
    }

}
