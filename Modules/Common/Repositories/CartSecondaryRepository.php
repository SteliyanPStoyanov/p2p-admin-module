<?php
declare(strict_types=1);

namespace Modules\Common\Repositories;

use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Core\Repositories\BaseRepository;

class CartSecondaryRepository extends BaseRepository
{
    /**
     * @param  array  $data
     *
     * @return CartSecondary
     */
    public function create(array $data): CartSecondary
    {
        $cartSecondary = new CartSecondary();
        $cartSecondary->fill($data);
        $cartSecondary->save();

        return $cartSecondary;
    }

    /**
     * @param  array  $data
     */
    public function update(array $data): void
    {
        $cartSecondary = CartSecondary::find($data['cart_id']);
        $cartSecondary->fill($data);
        $cartSecondary->save();
    }

    /**
     * @param int $id
     * @return CartSecondary
     */
    public function get(int $id): CartSecondary
    {;
        return CartSecondary::find($id);
    }

    /**
     * @param int $investor_id
     * @param string $type
     * @return bool
     */
    public function isInvestorHasCart(int $investor_id, string $type): bool
    {
        return (bool) CartSecondary::where('investor_id', $investor_id)->where('type', $type)->count();
    }

    /**
     * @param int $id
     * @param string $type
     * @return CartSecondary
     */
    public function getByInvestorId(int $id, string $type, int $status = -1): CartSecondary
    {
        $query = CartSecondary::with('investor')->where('investor_id', $id)->where('type', $type);

        if ($status >= 0) {
            $query->with(['loansForInvestment' => function($q) use ($status) {
                $q->where('status', '=', $status);
            }]);
        }

        return $query->first();
    }

    public function countInvested(int $cartId): int
    {
        return InvestmentBunch::where('cart_secondary_id', $cartId)
            ->orderBy('investment_bunch_id', 'desc')
            ->first()
            ->count; // investment_bunch.count field not a method
    }

    public function countProblematicInvestments(int $cartId): int
    {
        return CartSecondaryLoans::where('cart_secondary_id', $cartId)
            ->where('status', 0)
            ->count(); // method
    }

    public function amountInvested(int $cartId): float
    {
        $bunch = InvestmentBunch::where('cart_secondary_id', $cartId)
            ->orderBy('investment_bunch_id', 'desc')
            ->first();

        return floatval(Investment::where('investor_id', $bunch->investor_id)
            ->where('investment_bunch_id', $bunch->investment_bunch_id)
            ->sum('amount'));

    }
}
