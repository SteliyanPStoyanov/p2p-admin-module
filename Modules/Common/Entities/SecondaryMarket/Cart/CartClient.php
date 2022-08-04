<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart;

use Illuminate\Support\Collection;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\Cart;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartInterface;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoan;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoanBuyer;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoanInterface;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoansCollectionInterface;
use Modules\Common\Services\CartSecondaryLoansService;
use Modules\Common\Services\CartSecondaryService;

class CartClient
{
    private CartSecondaryService $cartService;

    private CartSecondaryLoansService $cartLoansService;

    public function __construct(CartSecondaryService $cartSecondaryService, CartSecondaryLoansService $cartSecondaryLoansService)
    {
        $this->cartService = $cartSecondaryService;
        $this->cartLoansService = $cartSecondaryLoansService;
    }

    /**
     * @param int $id
     * @param int $status
     * @return CartInterface
     */
    public function getByCartId(int $id, int $status = CartSecondaryLoans::LOAN_STATUS_OKAY): CartInterface
    {
        $cart = $this->cartService->get($id);

        return $this->get($cart, $cart->type, $status);
    }

    /**
     * @param int $investorId
     * @param string $type
     * @param int $status
     * @return CartInterface
     */
    public function getByInvestorId(int $investorId, string $type, int $status = CartSecondaryLoans::LOAN_STATUS_OKAY): CartInterface
    {
        $cart = $this->cartService->getByInvestorId($investorId, $type, $status);

        return $this->get($cart, $type, $status);
    }

    /**
     * @param CartSecondary $cart
     * @param string $type
     * @param int $status
     * @return CartInterface
     */
    public function get(CartSecondary $cart, $type = CartSecondary::TYPE_SELLER, int $status): CartInterface
    {
        $cartLoans = $cart->loansForInvestment;
        if($status) {
            $cartLoans = $cartLoans->where('status', $status);
        }

        if ($type == CartSecondary::TYPE_SELLER) {
            $cartLoanBuilder = new CartLoanBuilder();
        }
        else {
            $cartLoanBuilder = new CartLoanBuyerBuilder();
        }
        $cartLoansCollection = $cartLoanBuilder->buildCollection($cartLoans);

        $cartBuilder = new CartBuilder();

        return $cartBuilder->create($cart, $cartLoansCollection);
    }

    public function getCartCollection(): Collection
    {

    }

    public function isInvestorHasCart(int $investor_id, string $type): bool
    {
        return $this->cartService->isInvestorHasCart($investor_id, $type);
    }

    /**
     * Insert new cart into DB and build and return cart object
     *
     * @param int $investor_id
     * @param string $type
     * @return CartInterface
     */
    public function createCart(int $investor_id, string $type): CartInterface
    {
        $cart = $this->cartService->create([
            'investor_id' => $investor_id,
            'type' => $type
        ]);

        $cartBuilder = new CartBuilder();

        return $cartBuilder->createBlank($cart);
    }

    /**
     * @param Cart $cart
     */
    public function updateCart(Cart $cart): void
    {
        $this->cartService->update([
            'cart_id' => $cart->getCartId(),
            'investor_id' => $cart->getInvestor()->investor_id,
            'type' => $cart->getType()
        ]);
    }

    /**
     * @param Cart $cart
     */
    public function saveCart(Cart $cart): void
    {

        if ($cart->getCartId() && $cart->getCartId() != 0) {
            $this->updateCart($cart);

        }
        else {
            $cart = $this->createCart(
                $cart->getInvestor()->investor_id,
                $cart->getType()
            );
        }

        foreach ($cart->getLoans() as $loan) {
            $this->saveLoan($loan);
        }
    }

    /**
     * @param CartLoanInterface $loan
     * @return int
     */
    public function createLoan(CartLoanInterface $loan): int
    {
        return $this->cartLoansService->create(
            [
                'cart_secondary_id' => $loan->getCartId(),
                'loan_id' => $loan->getLoan()->loan_id,
                'investment_id' => $loan->getInvestment()->investment_id,
                'originator_id' => $loan->getOriginator()->originator_id,
                'principal_for_sale' => $loan->getPrincipalForSale(),
                'premium' => $loan->getPremium(),
                'price' => $loan->getPrice(),
                'percent_on_sell' => $loan->getPercentOnSell(),
                'percent_bought' => $loan->getPercentBought(),
                'filters' => $loan->getFilters(),
                'status' => $loan->isStatus(),
                'reason' => $loan->getReason(),
            ]
        )->cart_loan_id;
    }

    /**
     * @param CartLoanInterface $loan
     */
    public function updateLoan(CartLoanInterface $loan): void
    {
        $this->cartLoansService->update(
            [
                'cart_loan_id' => $loan->getCartLoanId(),
                'cart_secondary_id' => $loan->getCartId(),
                'loan_id' => $loan->getLoan()->loan_id,
                'investment_id' => $loan->getInvestment()->investment_id,
                'originator_id' => $loan->getOriginator()->originator_id,
                'principal_for_sale' => $loan->getPrincipalForSale(),
                'premium' => $loan->getPremium(),
                'price' => $loan->getPrice(),
                'percent_on_sell' => $loan->getPercentOnSell(),
                'percent_bought' => $loan->getPercentBought(),
                'filters' => $loan->getFilters(),
                'status' => $loan->isStatus(),
                'reason' => $loan->getReason()
            ]
        );
    }

    /**
     * TODO: test it to details !
     * TODO: should it return id or loan object ?
     * @param CartLoanInterface $loan
     */
    public function saveLoan(CartLoanInterface $loan): void
    {
        // If loan is already added into cart and have an id
        if ($this->isLoanAlreadyInCart($loan)) {
            $this->updateLoan($loan);

            return;
        }

        // insert loan and update entity's id
        $loan->setCartLoanId(
            $this->createLoan($loan)
        );
    }

    /**
     * @param CartLoanInterface $loan
     * @return bool
     */
    public function isLoanAlreadyInCart(CartLoanInterface $loan): bool
    {
        return $this->cartLoansService->isAlreadyInCart($loan);
    }

    /**
     * @param string $table
     * @param string $field
     * @param string $order
     * @param int $investorId
     * @param string $type
     * @return CartLoansCollectionInterface
     */
    public function getLoansOrderedBy(
        string $table,
        string $field,
        string $order,
        int $investorId,
        string $type = CartSecondary::TYPE_SELLER,
        int $status = CartSecondaryLoans::LOAN_STATUS_ERROR
    ): CartLoansCollectionInterface {

        $loans = $this->cartLoansService->getOrdered(
            $table,
            $field,
            $order,
            $investorId,
            $type,
            $status
        );

        if ($type == CartSecondary::TYPE_BUYER) {
            $cartLoanBuilder = new CartLoanBuyerBuilder();
        } else {
            $cartLoanBuilder = new CartLoanBuilder();
        }

        return $cartLoanBuilder->buildCollection($loans);
    }

    /**
     * We use soft delete
     *
     * @param int $id
     */
    public function deleteLoan(int $id): void
    {
        $this->cartLoansService->delete($id);
    }

    public function deleteLoanBySecondaryMarketId(int $secondaryMarketId): void
    {
        $this->cartLoansService->deleteBySecondaryMarketId($secondaryMarketId);
    }

    public function deleteManyBySecondaryMarketIds(array $secondaryMarketsId): void
    {
        $this->cartLoansService->deleteManyBySecondaryMarketIds($secondaryMarketsId);
    }

    /**
     * Really it doesn't remove the cart. It soft delete all loans in cart
     *
     * @param int $cartId
     */
    public function deleteCart(int $cartId): void
    {
        $this->cartLoansService->deleteCart($cartId);
    }

    /**
     * @param int $cartId
     */
    public function markCartOnSell(int $cartId): void
    {
        $this->cartLoansService->changeCartStatus(
            $cartId,
            CartSecondaryLoans::LOAN_STATUS_ON_SELL
        );
    }

    /**
     * @param int $investmentId
     * @return bool
     */
    public function isInvestmentInCart(int $investmentId): bool
    {
        return $this->cartLoansService->isInvestmentInCart($investmentId);
    }

    /**
     * @param CartLoanBuyer $cartLoanBuyer
     * @return int
     */
    public function saveLoanBuyer(CartLoanBuyer $cartLoanBuyer): int
    {

        if ($this->isLoanAlreadyInCart($cartLoanBuyer)) {
            $this->updateLoan($cartLoanBuyer);

            return $cartLoanBuyer->getCartLoanId();
        }

        $cartLoanBuyer->setCartLoanId(
            $this->createBuyerLoan($cartLoanBuyer)
        );

        return $cartLoanBuyer->getCartLoanId();
    }

    /**
     * @param CartLoanBuyer $loan
     * @return int
     */
    public function createBuyerLoan(CartLoanBuyer $loan): int
    {
         return $this->cartLoansService->create(
            [
                'cart_secondary_id' => $loan->getCartId(),
                'loan_id' => $loan->getLoan()->loan_id,
                'investment_id' => $loan->getInvestment()->investment_id,
                'originator_id' => $loan->getOriginator()->originator_id,
                'secondary_market_id' => $loan->getMarketSecondary()->market_secondary_id,
                'principal_for_sale' => $loan->getPrincipalForSale(),
                'premium' => $loan->getPremium(),
                'price' => $loan->getPrice(),
                'percent_bought' => $loan->getPercentBought(),
                'filters' => $loan->getFilters(),
                'status' => $loan->isStatus(),
                'reason' => $loan->getReason(),
            ]
        )->cart_loan_id;
    }

    public function countInvested(int $cartId): int
    {
        return $this->cartService->countInvested($cartId);
    }

    public function countProblematicInvestments(int $cartId): int
    {
        return $this->cartService->countProblematicInvestments($cartId);
    }

    public function amountInvested(int $cartId): float
    {
        return $this->cartService->amountInvested($cartId);
    }

    public function isInvestorOwnInvestment(
        int $investorId,
        int $cartLoanId,
        string $type = CartSecondary::TYPE_SELLER,
        int $status = CartSecondaryLoans::LOAN_STATUS_OKAY
    ): bool
    {
        $cart = $this->getByInvestorId($investorId, $type, $status);

        $loans = $cart->getLoans();

        if ($loans->getLoanById($cartLoanId)) {
            return true;
        }

        return false;
    }
}
