<?php
declare(strict_types=1);

namespace Modules\Common\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoanInterface;
use Modules\Core\Database\Collections\CustomEloquentCollection;
use Modules\Core\Repositories\BaseRepository;

class CartSecondaryLoansRepository extends BaseRepository
{
    /**
     * @param array $data
     * @return CartSecondaryLoans
     */
    public function create(array $data): CartSecondaryLoans
    {
        $cartSecondaryLoans = new CartSecondaryLoans();
        $cartSecondaryLoans->fill($data);
        $cartSecondaryLoans->save();

        return $cartSecondaryLoans;
    }

    /**
     * @param array $data
     * @return CartSecondaryLoans
     */
    public function update(array $data): CartSecondaryLoans
    {
        $cartSecondaryLoans = CartSecondaryLoans::find($data['cart_loan_id']);
        $cartSecondaryLoans->fill($data);
        $cartSecondaryLoans->save();

        return $cartSecondaryLoans;
    }

    /**
     * @param CartLoanInterface $cart
     * @return bool
     */
    public function isAlreadyInCart(CartLoanInterface $cart): bool
    {
        return (bool)CartSecondaryLoans::where(
            'cart_loan_id', $cart->getCartLoanId()
        )
        ->where('deleted', 0)
        ->count();
    }

    /**
     * @param CartLoanInterface $cart
     * @return int
     */
    public function findId(CartLoanInterface $cart): int
    {
        return (int)CartSecondaryLoans::where(
            'cart_secondary_id', $cart->getCartId()
        )
            ->where('investment_id', $cart->getInvestment()->investment_id)
            ->first()->cart_loan_id;
    }

    /**
     * @param string $table
     * @param string $field
     * @param string $order
     * @return Collection
     */
    public function getOrdered(string $table, string $field, string $order, int $investorId, string $type, int $status): Collection
    {
        $loans =  CartSecondaryLoans::join('loan', 'cart_secondary_loans.loan_id', '=', 'loan.loan_id')
            ->join('investment', 'cart_secondary_loans.investment_id', '=', 'investment.investment_id')
            ->join('originator', 'cart_secondary_loans.originator_id', '=', 'originator.originator_id')
            ->join('cart_secondary', 'cart_secondary_loans.cart_secondary_id', 'cart_secondary.cart_secondary_id')
            ->where('cart_secondary.type', $type)
            ->where('cart_secondary.investor_id', $investorId);

        if ($status) {
            $loans->where('cart_secondary_loans.status', $status);
        }

        return $loans->orderBy($table.'.'.$field, $order)->get();

    }

    public function changeCartStatus(int $cartId, int $status, string $reason = null): void
    {
        CartSecondaryLoans::where('cart_secondary_id', '=', $cartId)
            ->update([
                'status' => $status,
                'reason' => $reason
            ]);
    }

    public function changeStatus(int $cartLoanId, int $status, string $reason = null): void
    {
        $cartLoan = CartSecondaryLoans::find($cartLoanId);
        $cartLoan->status = $status;
        $cartLoan->reason = $reason;
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        CartSecondaryLoans::destroy($id);
    }

    public function deleteBySecondaryMarketId(int $secondaryMarketId): void
    {
        CartSecondaryLoans::where('secondary_market_id', '=', $secondaryMarketId)->delete();
    }

    public function deleteManyBySecondaryMarketIds(array $secondaryMarketIds): void
    {
        $ids = CartSecondaryLoans::whereIn('secondary_market_id', $secondaryMarketIds)->get();

        $msIds = [];
        foreach ($ids as $id) {
            $msIds[] = $id->cart_loan_id;
        }

        if ($msIds) {
            // This way it arises an event and fill in deleted field properly
            CartSecondaryLoans::destroy($msIds);
        }
    }

    public function deleteCart(int $cartId): void
    {
        $loans = CartSecondaryLoans::where('cart_secondary_id', '=', $cartId)->get();
        $loans->delete();
    }

    public function getManyByInvestmentIds(int $investorId, array $investmentIds, string $type = CartSecondary::TYPE_SELLER, int $status = CartSecondaryLoans::LOAN_STATUS_OKAY)
    {
        $cartSecondary = CartSecondary::where('investor_id', $investorId)
            ->where('type', $type)
            ->with(['loansForInvestment' => function($query) use ($status, $investmentIds){
                $query
                    ->where('status', $status)
                    ->where('deleted', '0')
                    ->where('active', '1')
                    ->whereIn('investment_id', $investmentIds);
            }])->first();

        if (
            isset($cartSecondary->loansForInvestment) &&
            $cartSecondary->loansForInvestment
        ){
            return $cartSecondary->loansForInvestment;
        }

        return new CustomEloquentCollection();
    }

    public function getManyByCartLoanIds(array $cartLoanIds, $status = 1)
    {
        return CartSecondaryLoans::whereIn('cart_loan_id', $cartLoanIds)
            ->where('deleted', 0)
            ->where('status', $status)
            ->get();
    }

    public function getManyBySecondaryMarketIds(array $secondaryMarketIds, $status = 1)
    {
        return CartSecondaryLoans::whereIn('secondary_market_id', $secondaryMarketIds)
            ->where('deleted', 0)
            ->where('status', $status)
            ->get();
    }

    public function isInvestmentInCart(int $investmentId): Collection
    {
        return CartSecondaryLoans::where('investment_id', $investmentId)
            ->where('deleted', 0)
            ->where('status', 1)
            ->get();
    }

    public function getLoansByInvestorId(int $investorId, $type = CartSecondary::TYPE_SELLER, int $status = CartSecondaryLoans::LOAN_STATUS_OKAY)
    {
        $cartSecondary = CartSecondary::where('investor_id', $investorId)
            ->where('type', $type)
            ->with(['loansForInvestment' => function($query) use ($status){
                $query->where('status', '=', $status);
            }])
            ->first();

        if (
            isset($cartSecondary->loansForInvestment) &&
            $cartSecondary->loansForInvestment
        ){
            return $cartSecondary->loansForInvestment;
        }

        return new CustomEloquentCollection();
    }
}
