<?php

namespace Modules\Profile\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Entities\SecondaryMarket\Cart\CartClient;
use Modules\Common\Entities\SecondaryMarket\Cart\CartLoanBuyerBuilder;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartInterface;
use Modules\Common\Entities\SecondaryMarket\Market\SecondaryMarketClient;
use Modules\Common\Http\Requests\MarketSecondaryInvestMultipleRequest;
use Modules\Common\Http\Requests\MarketSecondaryInvestSingleRequest;
use Modules\Common\Http\Requests\MarketSecondarySearchRequest;
use Modules\Common\Services\CartSecondaryLoansService;
use \Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\BaseException;
use Modules\Core\Exceptions\JsonException;
use ReflectionException;

class SecondaryMarketController extends BaseController
{
    protected CartSecondaryLoansService $cartSecondaryLoansService;

    /**
     * SecondaryMarketController constructor.
     * @param CartSecondaryLoansService $cartSecondaryLoansService
     * @throws ReflectionException
     */
    public function __construct(
        CartSecondaryLoansService $cartSecondaryLoansService
    ) {
        $this->cartSecondaryLoansService = $cartSecondaryLoansService;

        parent::__construct();
    }

    /**
     * @param SecondaryMarketClient $secondaryMarketClient
     * @param CartClient $cartClient
     * @return Application|Factory|View
     */
    public function list(SecondaryMarketClient $secondaryMarketClient, CartClient $cartClient)
    {
        $investorId = $this->getInvestorId();

        try {
            // TODO: we can have SecondaryInvestmentCollection and still use LengthAwarePaginator
            // https://laracasts.com/discuss/channels/laravel/how-to-use-lengthawarepaginator-manual-paginator
            $items = $secondaryMarketClient->pull(
                BaseController::DEFAULT_TABLE_ROWS_COUNT,
                $investorId,
                []
            );

            $this->getSessionService()->add($this->cacheKey, ['market' => 'secondary']);

            $this->getOrCreateCart($investorId, $cartClient);

            $loansInCartRaw = $this->cartSecondaryLoansService->getLoansByInvestorId(
                $investorId,
                CartSecondary::TYPE_BUYER,
                CartSecondaryLoans::LOAN_STATUS_OKAY
            );

            $loansInCart = collect([]);
            foreach ($loansInCartRaw as $loan) {
                $loansInCart->put(
                    $loan->secondary_market_id,
                    $loan
                );
            }

            return view(
                'profile::secondary-market.list',
                [
                    'cacheKey' => $this->cacheKey,
                    'userId' => $investorId,
                    'items' => $items,
                    'loansInSecondaryCart' => $loansInCart,
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    public function listUnsuccessful(SecondaryMarketClient $secondaryMarketClient, CartClient $cartClient)
    {
        try {
            $investorId = $this->getInvestorId();

            $cart = $cartClient->getByInvestorId(
                $investorId,
                CartSecondary::TYPE_BUYER,
                CartSecondaryLoans::LOAN_STATUS_ERROR
            );

            $marketSecondaryCollection = collect([]);
            $loansInCart = collect([]);
            foreach ($cart->getLoans()->get() as $loan) {
                $marketSecondaryCollection->put(
                    $loan->getMarketSecondary()->market_secondary_id . PHP_EOL,
                    $loan->getMarketSecondary()
                );

                $loansInCart->put(
                    $loan->getMarketSecondary()->market_secondary_id,
                    $loan
                );
            }

            $items = new LengthAwarePaginator(
                $marketSecondaryCollection,
                $marketSecondaryCollection->count(),
                BaseController::DEFAULT_TABLE_ROWS_COUNT
            );

            $loansInCartRaw = $this->cartSecondaryLoansService->getLoansByInvestorId(
                $investorId,
                CartSecondary::TYPE_BUYER,
                CartSecondaryLoans::LOAN_STATUS_ERROR
            );

            $loansInCart = collect([]);
            foreach ($loansInCartRaw as $loan) {
                $loansInCart->put(
                    $loan->secondary_market_id,
                    $loan
                );
            }

            return view(
                'profile::secondary-market.list',
                [
                    'cacheKey' => $this->cacheKey,
                    'userId' => $investorId,
                    'items' => $items,
                    'loansInSecondaryCart' => $loansInCart,
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }


    /**
     * @param MarketSecondarySearchRequest $request
     * @param SecondaryMarketClient $secondaryMarketClient
     * @return array|string
     * @throws JsonException
     */
    public function refresh(
        MarketSecondarySearchRequest $request,
        SecondaryMarketClient $secondaryMarketClient
    ) {
        try {
            parent::setFiltersFromRequest($request);

            $investorId = $this->getInvestorId();

            // TODO: we can have SecondaryInvestmentCollection and still use LengthAwarePaginator
            // https://laracasts.com/discuss/channels/laravel/how-to-use-lengthawarepaginator-manual-paginator

            $validated = $request->validated();

            $failedLoansInCart = $this->cartSecondaryLoansService->getLoansByInvestorId(
                $investorId,
                CartSecondary::TYPE_BUYER,
                CartSecondaryLoans::LOAN_STATUS_ERROR
            );

            // when we show failed purchases we have to force filter to work with them only
            $marketSecondaryIdsInCart = [];
            $status = CartSecondaryLoans::LOAN_STATUS_OKAY;
            if (
                isset($validated['secondaryMarketFailed']) &&
                $validated['secondaryMarketFailed']
            ) {
                $status = CartSecondaryLoans::LOAN_STATUS_ERROR;

                $marketSecondaryIdsInCart = [];
                foreach ($failedLoansInCart as $loan) {
                    $marketSecondaryIdsInCart[] = $loan->secondary_market_id;
                }
            }

            $items = $secondaryMarketClient->pull(
                $validated['limit'] ?? BaseController::DEFAULT_TABLE_ROWS_COUNT,
                $investorId,
                session($this->cacheKey, []),
                $marketSecondaryIdsInCart
            );

            $loansInCartRaw = $this->cartSecondaryLoansService->getLoansByInvestorId(
                $investorId,
                CartSecondary::TYPE_BUYER,
                $status
            );

            $loansInCart = collect([]);
            foreach ($loansInCartRaw as $loan) {
                $loansInCart->put(
                    $loan->secondary_market_id,
                    $loan
                );
            }

            return view(
                'profile::secondary-market.list-table',
                [
                    'cacheKey' => $this->cacheKey,
                    'items' => $items,
                    'loansInSecondaryCart' => $loansInCart,
                    'userId' => $investorId,
                ]
            )->render();
        } catch (\Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param MarketSecondaryInvestSingleRequest $request
     * @param CartClient $cartClient
     * @return bool[]
     * @throws JsonException
     */
    public function investSingle(
        MarketSecondaryInvestSingleRequest $request,
        CartClient $cartClient,
        CartLoanBuyerBuilder $loanBuilder
    ): array
    {
        $validated = $request->validated();
        $filters = session($this->cacheKey, []);

        try {
            $investorId = $this->getInvestorId();

            $cart = $this->getOrCreateCart($investorId, $cartClient);


            $cartLoanId = $cartClient->saveLoanBuyer(
                $this->storeArrayBuild($validated, $loanBuilder, $cart, $filters)
            );

            return [
                'success' => true,
                'data' => [
                    'message' => __('common.SellIsSuccess', ['amount' => $validated['amount']]),
                    'amount' => $validated['amount'],
                    'cart_loan_id' => $cartLoanId
                ]
            ];

        } catch (\Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param MarketSecondaryInvestMultipleRequest $request
     * @param CartClient $cartClient
     * @return bool[]|false[]
     * @throws JsonException
     */
    public function addToCartMultiple(
        MarketSecondaryInvestMultipleRequest $request,
        CartClient $cartClient,
        SecondaryMarketClient $secondaryMarketClient
    )
    {
        $validated = $request->validated();
        $filters = session($this->cacheKey, []);

        try {
            $investorId = $this->getInvestorId();

            $cart = $this->getOrCreateCart($investorId, $cartClient);

            $loanBuilder = new CartLoanBuyerBuilder();

            if (empty($validated['cart'])) {
                return [
                    'success' => false
                ];
            }

            foreach ($validated['cart'] as $investment) {
                $cartClient->saveLoanBuyer(
                    $this->storeArrayBuild($investment, $loanBuilder, $cart, $filters)
                );
            }

            return [
                    'success' => true
            ];
        } catch (\Throwable $e) {
            echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();
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
    public function delete(int $cartLoanId, CartClient $cartClient, SecondaryMarketClient $secondaryMarketClient)
    {
        try {
            $cartClient->deleteLoan($cartLoanId);
            $secondaryMarketClient->deleteByCartLoanId($cartLoanId);

        } catch (\Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param $data
     * @param $loanBuilder
     * @param $cart
     * @param $filters
     * @return mixed
     */
    public function storeArrayBuild($data, $loanBuilder, $cart, $filters)
    {
        $marketSecondary = MarketSecondary::find($data['market_secondary_id']);

        if ($marketSecondary->principal_for_sale < $data['amount']) {
            $data['amount'] = $marketSecondary->principal_for_sale;
        }

        $commonArray = [
            'loanId' => $data['loan_id'],
            'investmentId' => $data['investment_id'],
            'originatorId' => $data['originator_id'],
            'marketSecondaryId' => $data['market_secondary_id'],
            'principalForSale' => (float)$data['amount'],
            'premium' => $marketSecondary->premium,
            'filters' => json_encode($filters),
            'status' => CartSecondaryLoans::LOAN_STATUS_OKAY,
            'reason' => ''
        ];

        if (!empty($data['cart_loan_id'])) {
            $loan = $loanBuilder->createSingleFromArray(
                array_merge(
                    [
                        'cart_secondary_id' => $cart->getCartId(),
                        'cart_loan_id' => $data['cart_loan_id'],
                    ],
                    $commonArray
                )

            );
        } else {
            $loan = $loanBuilder->buildNewSingle(
                array_merge(
                    [
                        'cartId' => $cart->getCartId(),
                    ],
                    $commonArray
                )
            );
        }
        return $loan;
    }

    /**
     * @param $investorId
     * @param $cartClient
     * @return CartInterface
     */
    public function getOrCreateCart($investorId, $cartClient): CartInterface
    {
        if (
            $cartClient->isInvestorHasCart(
                $investorId,
                CartSecondary::TYPE_BUYER
            )
        ) {
            $cart = $cartClient->getByInvestorId(
                $investorId,
                CartSecondary::TYPE_BUYER
            );
        } else {
            $cart = $cartClient->createCart(
                $investorId,
                CartSecondary::TYPE_BUYER
            );
        }

        return $cart;
    }
}
