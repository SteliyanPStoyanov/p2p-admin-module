<?php

namespace Modules\Profile\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\SecondaryMarket\Cart\CartClient;
use Modules\Common\Entities\SecondaryMarket\Cart\CartLoanBuilder;
use Modules\Common\Entities\SecondaryMarket\Cart\CartLoanHelper;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartInterface;
use Modules\Common\Entities\SecondaryMarket\CartToMarketConverter;
use Modules\Common\Entities\SecondaryMarket\Market\SecondaryMarketClient;
use Modules\Common\Services\InvestmentService;
use Modules\Core\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\BaseException;
use Modules\Core\Exceptions\JsonException;
use Modules\Profile\Http\Requests\CartSecondaryBuyAllRequest;
use Modules\Profile\Http\Requests\CartSecondarySellAllRequest;
use Throwable;

class SecondaryMarketSellController extends BaseController
{

    /**
     * @param Request $request
     * @param CartClient $cartClient
     * @param SecondaryMarketClient $secondaryMarketClient
     * @return array
     * @throws JsonException
     */
    public function addToCartSingle(
        Request $request,
        CartClient $cartClient,
        SecondaryMarketClient $secondaryMarketClient
    ) {
        try {
            $investorId = $this->getInvestorId();

            // get card or create it, if its first time sell
            $cart = $this->getCart($investorId, $cartClient);

            $loanBuilder = new CartLoanBuilder();
            if ($cartClient->isInvestmentInCart($request->investmentId)) {
                $loan = $loanBuilder->createSingleFromArray(
                    [
                        'cart_secondary_id' => $cart->getCartId(),
                        'cart_loan_id' => $request->cartLoanId,
                        'loanId' => $request->loanId,
                        'investmentId' => $request->investmentId,
                        'originatorId' => $request->originatorId,
                        'principal_for_sale' => $request->amount,
                        'price' => $request->amount,
                        'premium' => 0,
                        'filters' => json_encode($request->filters),
                        'status' => CartSecondaryLoans::LOAN_STATUS_OKAY,
                        'reason' => ''
                    ]
                );
            } else {
                $loan = $loanBuilder->buildNewSingle(
                    [
                        'cartId' => $cart->getCartId(),
                        'loanId' => $request->loanId,
                        'investmentId' => $request->investmentId,
                        'originatorId' => $request->originatorId,
                        'principalForSale' => $request->amount,
                        'premium' => 0,
                        'filters' => json_encode($request->filters),
                        'status' => CartSecondaryLoans::LOAN_STATUS_OKAY,
                        'reason' => ''
                    ]
                );
            }

            $cartClient->saveLoan($loan);

            if ($request->ajax() == true) {
                return [
                    'success' => true,
                    'data' => [
                        'message' => __('common.SellIsSuccess', ['amount' => $request->amount]),
                        'amount' => $request->amount,
                    ]
                ];
            }
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param Request $request
     * @param CartClient $cartClient
     * @return bool[]|false[]
     * @throws JsonException
     */
    public function addToCartMultiple(
        Request $request,
        CartClient $cartClient
    ) {
        try {
            $investorId = $this->getInvestorId();

            if (empty($request['dataArray'])) {
                return [
                    'success' => false
                ];
            }
            // get card or create it, if its first time sell
            $cart = $this->getCart($investorId, $cartClient);

            // If $investment['cart_loan_id'] then we should update it - because it's already in the cart or on the market
            $loanBuilder = new CartLoanBuilder();
            foreach ($request['dataArray'] as $investment) {
                if (isset($investment['cart_loan_id']) && $investment['cart_loan_id']) {
                    $loan = $loanBuilder->createSingleFromArray(
                        [
                            'cart_secondary_id' => $cart->getCartId(),
                            'cart_loan_id' => $investment['cart_loan_id'],
                            'loanId' => $investment['loan_id'],
                            'investmentId' => $investment['investment_id'],
                            'originatorId' => $investment['originator_id'],
                            'principal_for_sale' => $investment['amount'],
                            'price' => $investment['amount'],
                            'premium' => 0,
                            'filters' => json_encode($request->filters),
                            'status' => CartSecondaryLoans::LOAN_STATUS_OKAY,
                            'reason' => ''
                        ]
                    );
                } else {
                    $loan = $loanBuilder->buildNewSingle(
                        [
                            'cartId' => $cart->getCartId(),
                            'loanId' => $investment['loan_id'],
                            'investmentId' => $investment['investment_id'],
                            'originatorId' => $investment['originator_id'],
                            'principalForSale' => $investment['amount'],
                            'premium' => 0,
                            'filters' => json_encode($request->filters),
                            'status' => CartSecondaryLoans::LOAN_STATUS_OKAY,
                            'reason' => ''
                        ]
                    );
                }

                $cartClient->saveLoan($loan);
            }

            return [
                'success' => true
            ];
        } catch (Throwable $e) {
            echo $e->getMessage() . " " . $e->getFile() . " " . $e->getLine();
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function cart()
    {
        try {
            return view('profile::secondary-cart.cart');
        } catch (Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param CartClient $cartClient
     * @return Application|Factory|View
     * @throws JsonException
     */
    public function list(CartClient $cartClient)
    {
        try {
            $investorId = $this->getInvestorId();

            // get card or create it, if its first time sell
            $cart = $this->getCart($investorId, $cartClient);

            return view(
                'profile::secondary-cart.list',
                [
                    'cart' => $cart,
                    'loans' => $cart->getLoans()->get(),
                    'cacheKey' => $this->cacheKey,
                ]
            );
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param CartClient $cartClient
     * @return Application|Factory|View
     * @throws JsonException
     */
    public function listBuy(CartClient $cartClient)
    {
        try {
            $investorId = $this->getInvestorId();

            if (
                !$cartClient->isInvestorHasCart(
                    $investorId,
                    CartSecondary::TYPE_BUYER
                )
            ) {
                $cartClient->createCart($investorId, CartSecondary::TYPE_BUYER);
            }

            $cart = $cartClient->getByInvestorId(
                $investorId,
                CartSecondary::TYPE_BUYER,
                CartSecondaryLoans::LOAN_STATUS_OKAY
            );

            return view(
                'profile::secondary-cart.list-buy',
                [
                    'cart' => $cart,
                    'loans' => $cart->getLoans()->get(),
                    'cacheKey' => $this->cacheKey,
                ]
            );
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param Request $request
     * @param CartClient $cartClient
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|string
     *
     * Format:
     *     #total: 50
     *     #lastPage: 5
     *     #items: Illuminate\Support\Collection
     *     #perPage: 10
     *     #currentPage: 1
     *     #path: "http://localhost:7000/profile/cart-secondary/list"
     *
     * @throws JsonException
     */
    public function refresh(Request $request, CartClient $cartClient)
    {
        try {
            $table = key($request->order);
            $field = key($request->order[key($request->order)]);
            $order = $request->order[key($request->order)][key($request->order[key($request->order)])];
            $investorId = $this->getInvestorId();

            $loans = $cartClient->getLoansOrderedBy(
                $table,
                $field,
                $order,
                (int)$investorId,
                CartSecondary::TYPE_SELLER,
                CartSecondaryLoans::LOAN_STATUS_OKAY
            );

            return view(
                'profile::secondary-cart.list-table',
                ['loans' => $loans->get()]
            )->render();
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param Request $request
     * @param CartClient $cartClient
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|string
     *
     * Format:
     *     #total: 50
     *     #lastPage: 5
     *     #items: Illuminate\Support\Collection
     *     #perPage: 10
     *     #currentPage: 1
     *     #path: "http://localhost:7000/profile/cart-secondary/list"
     *
     * @throws JsonException
     */
    public function refreshBuy(Request $request, CartClient $cartClient)
    {
        try {
            $investorId = $this->getInvestorId();

            $loans = $cartClient->getByInvestorId(
                $investorId,
                CartSecondary::TYPE_BUYER,
                CartSecondaryLoans::LOAN_STATUS_OKAY
            )->getLoans();

            return view(
                'profile::secondary-cart.list-table-buy',
                ['loans' => $loans->get()]
            )->render();
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param int $cartLoanId
     * @param CartClient $cartClient
     * @param SecondaryMarketClient $secondaryMarketClient
     * @throws JsonException
     */
    public function deleteLoan(
        int $cartLoanId,
        CartClient $cartClient,
        SecondaryMarketClient $secondaryMarketClient
    ) {
        try {
            $investorId = $this->getInvestorId();

            if (
                $cartClient->isInvestorOwnInvestment(
                    $investorId,
                    $cartLoanId,
                    CartSecondary::TYPE_SELLER,
                    CartSecondaryLoans::LOAN_STATUS_OKAY
                ) ||
                $cartClient->isInvestorOwnInvestment(
                    $investorId,
                    $cartLoanId,
                    CartSecondary::TYPE_SELLER,
                    CartSecondaryLoans::LOAN_STATUS_ON_SELL
                )
            ) {
                $marketSecondaryItems = $secondaryMarketClient->getBySecondaryLoanOnSale($cartLoanId);
                $marketSecondaryIds = [];
                foreach ($marketSecondaryItems as $item) {
                    $marketSecondaryIds[] = $item->market_secondary_id;
                }

                $cartClient->deleteLoan($cartLoanId);

                $secondaryMarketClient->deleteByCartLoanId($cartLoanId);

                if ($marketSecondaryIds) {
                    // Seller remove their own investments from cart and secondary market.
                    // Delete investments from buyers carts (in case have have any).
                    $cartClient->deleteManyBySecondaryMarketIds($marketSecondaryIds);
                }
            }
            elseif(
                $cartClient->isInvestorOwnInvestment(
                    $investorId,
                    $cartLoanId,
                    CartSecondary::TYPE_SELLER,
                    CartSecondaryLoans::LOAN_STATUS_SOLD
                )
            ) {
                throw new BaseException("Someone just bought your investment.");
            }
            else {
                throw new BaseException("Loan ID: ".$cartLoanId." not found.");
            }
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param int $cartLoanId
     * @param CartClient $cartClient
     * @param SecondaryMarketClient $secondaryMarketClient
     * @throws JsonException
     * @return string
     */
    public function deleteLoanBuyer(
        int $cartLoanId,
        CartClient $cartClient
    ) {
        try {
            $investorId = $this->getInvestorId();

            if (
                $cartClient->isInvestorOwnInvestment(
                    $investorId,
                    $cartLoanId,
                    CartSecondary::TYPE_BUYER,
                    CartSecondaryLoans::LOAN_STATUS_OKAY
                )
            ) {
                $cartClient->deleteLoan($cartLoanId);

                return response()->json([
                    'result' => 'success',
                ]);
            }

            throw new BaseException("Investment ".$cartLoanId." not found");

        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param int $cart_id
     * @param CartClient $cartClient
     * @param SecondaryMarketClient $secondaryMarketClient
     * @param CartSecondarySellAllRequest $request
     * @throws JsonException
     */
    public function sellAll(
        int $cart_id,
        CartClient $cartClient,
        SecondaryMarketClient $secondaryMarketClient,
        CartSecondarySellAllRequest $request
    ) {
        try {
            $cart = $cartClient->getByCartId($cart_id, CartSecondaryLoans::LOAN_STATUS_OKAY);

            foreach ($cart->getLoans()->get() as $loan) {
                foreach ($request->cart as $item) {
                    if ($loan->getCartLoanId() == $item['cartLoanId']) {
                        $loan->setPremium((float)$item['discount']);
                        $loan->setPrincipalForSale((float)$item['principal']);
                        $loan->setPrice((float)$item['salePrice']);

                        $loan->setPercentOnSell(
                            CartLoanHelper::calculatePercentOnSell(
                                $item['principal'],
                                $loan->getInvestment()->amount
                            )
                        );

                        $cartClient->saveLoan($loan);
                    }
                }
            }

            $cart = $cartClient->getByCartId($cart_id);

            $secondaryInvestment = CartToMarketConverter::run(
                $cart,
                $request->cart
            );// TODO: need a class to convert a cart object into secondary market investment object
            // like here: https://stackoverflow.com/questions/11832076/design-pattern-to-convert-a-class-to-another
            $secondaryMarketClient->pushing($secondaryInvestment);
//            $cartClient->deleteCart($cart_id);
            $cartClient->markCartOnSell($cart_id);
        } catch (Throwable $e) {
            echo $e->getMessage() . " " . $e->getFile() . " " . $e->getLine();
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param int $cart_id
     * @param CartClient $cartClient
     * @param SecondaryMarketClient $secondaryMarketClient
     * @return RedirectResponse
     * @throws JsonException
     */
    public function deleteAll(
        int $cart_id,
        CartClient $cartClient,
        SecondaryMarketClient $secondaryMarketClient
    ): RedirectResponse {
        try {
            $secondaryMarketClient->deleteByCartId($cart_id);
            $cartClient->deleteCart($cart_id);

            return redirect()->route('profile.myInvest');
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param CartClient $cartClient
     * @param InvestmentService $investmentService
     * @param CartSecondaryBuyAllRequest $request
     * @return JsonResponse
     * @throws JsonException
     */
    public function buyAll(
        CartClient $cartClient,
        InvestmentService $investmentService,
        CartSecondaryBuyAllRequest $request
    ) {
        $validated = $request->validated();

        try {
            $cart = $cartClient->getByCartId($validated['cartId']);
            $cartLoans = $cart->getLoans();

            foreach ($validated['cart'] as $item) {
                if ($cartLoan = $cartLoans->getLoanById($item['cartLoanId'])) {
                    $cartLoan->setPrincipalForSale($item['principal']);
                    $cartLoan->recalculatePercentBought();

                    $cartClient->updateLoan($cartLoan);
                }
            }

            // We may allow it to run even in case seller removed investments from market
            /// See Problem #1 in documentation
            if ($investmentService->massInvestSecondaryMarket($cart, false)) {
                return response()->json(
                    ['url' => route('profile.cart-secondary.buySuccess')],
                    200
                );
            }
        } catch (Exception $e) {
            echo $e->getMessage()." ".$e->getFile()." ".$e->getLine().PHP_EOL;
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function buyAllSuccess(CartClient $cartClient)
    {
        try {
            $investorId = $this->getInvestorId();

            $cart = $cartClient->getByInvestorId(
                $investorId,
                CartSecondary::TYPE_BUYER,
                CartSecondaryLoans::LOAN_STATUS_OKAY
            );

            return view(
                'profile::secondary-cart.buy-success',
                [
                    'countInvested' => $cartClient->countInvested($cart->getCartId()),
                    'amountInvested' => $cartClient->amountInvested($cart->getCartId()),
                    'countProblematic' => $cartClient->countProblematicInvestments($cart->getCartId()),
                ]
            );
        } catch (Throwable $e) {
            echo $e->getMessage() . " " . $e->getFile() . " " . $e->getLine();
            return view('errors.generic');
        }
    }

    /**
     * @param CartClient $cartClient
     * @return array|string
     * @throws Throwable
     */
    public function buyAllSuccessRefresh(CartClient $cartClient)
    {
        $investorId = $this->getInvestorId();

        $cart = $cartClient->getByInvestorId(
            $investorId,
            CartSecondary::TYPE_BUYER,
            CartSecondaryLoans::LOAN_STATUS_OKAY
        );

        return view(
            'profile::secondary-cart.buy-box',
            [
                'countInvested' => $cartClient->countInvested($cart->getCartId()),
                'amountInvested' => $cartClient->amountInvested($cart->getCartId()),
                'countProblematic' => $cartClient->countProblematicInvestments($cart->getCartId()),
            ]
        )->render();
    }

    /**
     * @param int $investorId
     * @param CartClient $cartClient
     * @param int $status
     * @return CartInterface
     */
    private
    function getCart(
        int $investorId,
        CartClient $cartClient,
        int $status = CartSecondaryLoans::LOAN_STATUS_OKAY
    ) {
        if (
            $cartClient->isInvestorHasCart(
                $investorId,
                CartSecondary::TYPE_SELLER
            )
        ) {
            return $cartClient->getByInvestorId(
                $investorId,
                CartSecondary::TYPE_SELLER,
                $status
            );
        }

        return $cartClient->createCart(
            $investorId,
            CartSecondary::TYPE_SELLER
        );
    }
}
