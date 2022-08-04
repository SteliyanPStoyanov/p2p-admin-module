<?php

namespace Modules\Common\Jobs\InvestAll\HandlerTactics;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Entities\SecondaryMarket\Cart\CartLoanHelper;
use Modules\Common\Jobs\InvestAll\BeforeInvestingCheck;
use Modules\Common\Jobs\InvestAll\BeforeInvestingSecondaryCartCheck;
use Modules\Common\Jobs\InvestAll\HandlerTactics\HandlerTacticInterface;
use Modules\Common\Jobs\InvestAll\HandlerTactics\Tactic;
use Throwable;

class CartSecondaryTactic extends Tactic implements HandlerTacticInterface
{
    use SecondaryMarketHelper;

    private float $amountRequired;

    private array $reservedIds;

    private array $loandIds;

    private array $investmentIds;

    public function __construct(InvestmentBunch $bunch)
    {
        parent::__construct($bunch);
        $this->amountRequired = 0.00;
        $this->reservedIds = [];
        $this->loandIds = [];
        $this->setCart();
    }

    public function check(): bool
    {
        return true;
    }

    public function massInvest()
    {
        // https://stackoverflow.com/questions/34556511/laravel-lockforupdate-pessimistic-locking
        // https://laravel.com/docs/8.x/queries#chunking-results  chunkById
        try {
            $wallet = $this->getWallet();
            if (empty($wallet->wallet_id)) {
                dump('Failed to get wallet');
                $this->errors[] = 'Failed to get wallet';
                return false;
            }

            $amountToInvest = 0;
            foreach (
                $this->getCart()->loansForInvestment->where(
                    'status',
                    CartSecondaryLoans::LOAN_STATUS_OKAY
                ) as $loan
            ) {
                $this->summarizeAmountRequired($loan);
                $this->collectLoanIds($loan);
            }

            if (!$this->loandIds) {
                dump('Empty loanIds. Nothing to buy. Exit now.');
                return false;
            }

            $startTotal = microtime(true);


            $this->getCart()->loansForInvestment()
                ->where('status', CartSecondaryLoans::LOAN_STATUS_OKAY)
                ->chunkById(
                    20,
                    function ($loanIdsCollection) {
                        $loanIds = $this->getLoanIdsFromCollectionSecondaryMarket($loanIdsCollection);
                        dump('chunk count = ' . count($loanIds));

                        $investedLoansInChunk = [];
                        if (!empty($loanIds)) {
                            $investedLoansInChunk = $this->massInvestForReservedLoans(
                                $loanIds
                            );
                        }

                        // if nothing to buy -> stop chunking
                        if (empty($investedLoansInChunk)) {
                            return false;
                        }
                    },
                    'cart_loan_id'
                );
        } catch (\Throwable $e) {
            dump($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
        }

        $endTotal = microtime(true);
        $countInvested = count($this->totalInvestedInLoans);

        dump('totalInvestedInCartLoans:', $this->totalInvestedInLoans);
        dump('details:', $this->details);
        dump('Total exec.time: ' . round(($endTotal - $startTotal), 2) . ' sec(s), for ' . $countInvested . ' loan(s)');

        return ($countInvested > 0);
    }

    private function collecCarttLoanIds(CartSecondaryLoans $loan): array
    {
        $this->reservedIds[] = $loan->cart_loan_id;
    }

    private function collectLoanIds(CartSecondaryLoans $loan): void
    {
        $this->loandIds[] = $loan->loan_id;
    }

    private function collectInvestmentIds(CartSecondaryLoans $loan): array
    {
        $this->investmentIds[] = $loan->investment_id;
    }


    private function summarizeAmountRequired(CartSecondaryLoans $loan)
    {
        $this->amountRequired += $loan->price;
    }

    private function massInvestForReservedLoans(array $loanIds): array
    {
        $investedInLoans = [];

        DB::beginTransaction();

        try {
            $buyerWallet = $this->getBlockedWallet();

            $buyerWalletAvailableAmount = $buyerWallet->uninvested;

            $cart = $this->getBlockedCart();
            $cartLoans = $this->getBlockedCartLoansByIds($loanIds); // here we should go through loans from chunk
            $buyerPortfolioQuality = $this->getInvestorBlockedQuality($cart->investor); // From SecondaryMarketHelper
            $buyerPortfolioMaturity = $this->getInvestorBlockedMaturity($cart->investor); // Really return Portfolio object

            $insertsBuyer = [];
            $insertsSeller = [];

            $buyerLoansUpdate = [];

            $priceTotal = 0;
            $buyerWalletAmountAfter = 0;

            $oldSellerInvestmentsIds = [];
            $addUpdatesSeller = [];
            $sellersWallets = [];
            $buyersWallet = [];

            $buyerPortfolioQualityData = [];
            $buyerPortfolioMaturityData = [];

            $sellerPortfolioQualityData = [];
            $sellerPortfolioMaturityData = [];

            $sellerInstallments = [];

            foreach ($cartLoans as $loan) {
                try {
                    $cartLoanOnSale = $loan->marketSecondary->loanOnSale;
                    $buyer = $cart->investor; // locked
                    $seller = $loan->marketSecondary->investor;
                    $sellerWallet = $seller->wallet();

                    if ($loan->marketSecondary->percent_sold >= 100) {
                        $loan->update([
                            'status' => 0,
                            'reason' => 'Rejected. 100% of seller\'s investment already sold.'
                        ]);

                        Log::channel('invest_service')->error(
                            'Error! Rejected. 100% of seller\'s investment already sold.'
                            . 'Seller #' . $seller->investor_id . ', '
                            . 'Buyer #' . $buyer->investor_id . ', '
                            . 'principal on sell = ' . $cartLoanOnSale->principal_for_sale . ', '
                            . 'principal to buy = ' . $loan->principal_for_sale . ', '
                            . 'seller cart loan id: ' . $cartLoanOnSale->cart_loan_id . ', '
                            . 'buyer cart loan id: ' . $loan->cart_loan_id . ', '
                            . 'loanIds: ' . implode(', ', $loanIds) . ', '
                            . 'InvestedInLoans: ' . implode(', ', $investedInLoans)
                        );

                        continue;
                    }

                    // mandatory checks on buyer side
                    $buyerCheck = new BeforeInvestingSecondaryCartCheck(
                        $buyerWallet,
                        $loan,
                        $this->totalInvestedInLoans,
                        count($this->loandIds),
                        $loan->price,
                        $this->getPortfolioService(),
                        $cartLoanOnSale,
                        $buyerWalletAvailableAmount
                    );

                    if (!$buyerCheck->isOk()) {
                        $this->details[$buyerCheck->getKey()] = sprintf(
                            'Msg: %s | Action: %s',
                            $buyerCheck->getMsg(),
                            $buyerCheck->getAction()
                        );
                        dump('getAction = ' . $buyerCheck->getAction() . ' | Msg: ' . $buyerCheck->getMsg());

                        $loan->update(
                            [
                                'status' => CartSecondaryLoans::LOAN_STATUS_ERROR,
                                'reason' => $buyerCheck->getMsg()
                            ]
                        );

                        if ('break' == $buyerCheck->getAction()) {
                            break;
                        }

                        if ('continue' == $buyerCheck->getAction()) {
                            continue;
                        }
                    }

                    // get portfolio ranges
                    $buyerQualityRange = $buyerCheck->getqualityRange();
                    $buyerMaturityRange = $buyerCheck->getmaturityRange();
                    $sellerQualityRange = $buyerCheck->getqualityRange();
                    $sellerMaturityRange = $buyerCheck->getmaturityRange();

                    // get locked objects
                    $sellerPortfolioQuality = $this->getInvestorBlockedQuality($seller);
                    $sellerPortfolioMaturity = $this->getInvestorBlockedMaturity($seller);

                    $sellerInstallments[] = [
                        'investor_id' => $seller->investor_id,
                        'investment_id' => $cartLoanOnSale->investment_id,
                    ];

                    $principalToSell = $cartLoanOnSale->principal_for_sale;
                    $premium = $cartLoanOnSale->premium;
                    $principalToBuy = $loan->principal_for_sale;

                    $amountDiff = $principalToSell - $principalToBuy;
                    if ($amountDiff < 0) {
                        Log::channel('invest_service')->error(
                            'Error! Buyer can not buy more than seller put on sell'
                            . 'Seller #' . $seller->investor_id . ', '
                            . 'Buyer #' . $buyer->investor_id . ', '
                            . 'principal on sell = ' . $cartLoanOnSale->principal_for_sale . ', '
                            . 'principal to buy = ' . $loan->principal_for_sale . ', '
                            . 'seller cart loan id: ' . $cartLoanOnSale->cart_loan_id . ', '
                            . 'buyer cart loan id: ' . $loan->cart_loan_id . ', '
                            . 'loanIds: ' . implode(', ', $loanIds) . ', '
                            . 'InvestedInLoans: ' . implode(', ', $investedInLoans)
                        );

                        // Update buyers cart. Change status to 0 (error) and fill in the reason
                        $loan->update(
                            [
                                'status' => CartSecondaryLoans::LOAN_STATUS_ERROR,
                                'reason' => $buyerCheck->getMsg()
                            ]
                        );

                        continue;
                    }

                    $investedInLoans[] = $loan->marketSecondary->market_secondary_id;

                    // prepare data for mass inserts
                    // We have to have different keys for buyer and seller to allow InvestAllRelationJob do the job
                    $uniqueKeyBuyer = $this->createUniqueKey(
                        $buyerWallet->investor_id,
                        $loan->getId(),
                        $principalToBuy
                    );

                    $uniqueKeySeller = $this->createUniqueKey(
                        $sellerWallet->investor_id,
                        $loan->getId(),
                        $principalToSell
                    );

                    $insertsBuyer = $this->fillInsertsSecondaryMarketBuyer(
                        $insertsBuyer,
                        $uniqueKeyBuyer,
                        $buyerWallet,
                        $loan->marketSecondary,
                        $loan->premium,
                        $loan->price,
                        $buyerQualityRange,
                        $principalToBuy,
                        $sellerWallet->investor_id
                    );

                    if (empty($insertsBuyer['buyer']['investments'])) {
                        Log::channel('invest_service')->error(
                            'Error! Could not prepare data for investing'
                            . 'investor #' . $this->bunch->investor_id . ', '
                            . 'amount = ' . $loan->principal_for_investment . ', '
                            . 'secondary market id: ' . implode(', ', $loanIds) . ', '
                            . 'InvestedInSecondaryLoans: ' . implode(', ', $investedInLoans)
                        );

                        $loan->update(
                            [
                                'status' => CartSecondaryLoans::LOAN_STATUS_ERROR,
                                'reason' => 'Error! Could not prepare data for investing'
                            ]
                        );

                        throw new Exception("Empty array with investments, rollback transaction");
                    }

                    $amountInvestment = $loan->marketSecondary->loanOnSale->investment->amount - $loan->principal_for_sale;
                    $amountLeft = $loan->marketSecondary->principal_for_sale - $loan->principal_for_sale;

                    $insertsSeller = $this->fillInsertsSecondaryMarketSeller(
                        $insertsSeller,
                        $uniqueKeySeller,
                        $seller->wallet(),
                        $loan->marketSecondary,
                        $loan->premium,
                        $loan->price,
                        $sellerQualityRange,
                        $principalToSell, // delete investment if sold in full
                        $amountInvestment,  // what buyer bought. Use this to update sellers original investment
                        $amountLeft,
                        $buyer->investor_id
                    );

                    $buyerPortfolioQualityData[$buyerPortfolioQuality->portfolio_id] = [
                        $buyerQualityRange => $buyerPortfolioQuality->$buyerQualityRange + 1,
                    ];

                    $buyerPortfolioMaturityData[$buyerPortfolioMaturity->portfolio_id] = [
                        $buyerMaturityRange => $buyerPortfolioQuality->$buyerMaturityRange + 1,
                    ];


                    $sellersCartLoanPrincipalBefore = $principalToSell;
                    $sellersCartLoanPrincipalAfter = $principalToSell - $principalToBuy;
                    // Buy whole investment, we should reduce seller portfolios
                    if ($amountDiff == 0) {
                        $sellerPortfolioQualityData[$sellerPortfolioQuality->portfolio_id] = [
                            $sellerQualityRange => $sellerPortfolioQuality->$sellerQualityRange - 1,
                        ];

                        $sellerPortfolioMaturityData[$sellerPortfolioMaturity->portfolio_id] = [
                            $sellerMaturityRange => $sellerPortfolioMaturity->$sellerMaturityRange - 1,
                        ];
                    }

                    $buyerWalletAmountBefore = $buyerWallet->uninvested;

                    // Calculate and prepare sellers wallet uninvested amount to use it later
                    $priceTotal += $loan->price;


                    $sellersWallets = $this->populateWalletsArray(
                        $seller->wallet()->wallet_id,
                        $sellersWallets,
                        $priceTotal,
                        $loan->principal_for_sale,
                        $loan->premium
                    );

                    $buyersWallet = $this->populateWalletsArray(
                        $buyerWallet->wallet_id,
                        $buyersWallet,
                        $priceTotal,
                        $loan->principal_for_sale,
                        $loan->premium
                    );

                    $buyerWalletAmountAfter = $buyerWalletAmountBefore - $priceTotal;

                    $buyerWalletAvailableAmount -= $loan->price;

                    // invest stats
                    $this->totalInvestedInLoans[$loan->getId()] = sprintf(
                        'secondary market #' . $loan->marketSecondary->getId() . ', '
                        . 'price = ' . $loan->price . ', '
                        . 'principal to sell  = ' . $cartLoanOnSale->principal_for_sale . ', '
                        . 'principal to buy  = ' . $loan->principal_for_sale . ', '
                        . 'premium = ' . $premium . ', '
                        . 'uniqueKeyBuyer =' . $uniqueKeyBuyer . ', '
                        . 'uniqueKeySeller =' . $uniqueKeySeller . ', '

                        . 'Principal on sell b. = ' . $sellersCartLoanPrincipalBefore . ', '
                        . 'Principal on sell a. = ' . $sellersCartLoanPrincipalAfter . ', '

                        . 'seller\'s wallet amount b. = ' . $seller->wallet()->uninvested . ', '
                        . 'seller\'s wallet amount a. = ' . $priceTotal . ', '

                        . 'buyer\'s wallet amount b. = ' . $buyerWalletAmountBefore . ', '
                        . 'buyer\'s wallet amount a. = ' . $buyerWalletAmountAfter
                    );

                    // Update buyers cart -> what part of market_secondary.principal_for_sale has been bought
                    $buyerLoansUpdate[$loan->cart_loan_id] = [
                        'percent_bought' => $this->calculateCartBought($loan->marketSecondary, $loan),
                        'status' => CartSecondaryLoans::LOAN_STATUS_BOUGHT
                    ];

                    $percentSold = $this->calculateMarketSold($loan->marketSecondary, $loan);

                    $newPrice = CartLoanHelper::calculatePrice($cartLoanOnSale->premium, $sellersCartLoanPrincipalAfter);

                    // Collect seller's original investment ids to delete them later all at once
                    $oldSellerInvestmentsIds[] = $cartLoanOnSale->investment->investment_id;

                    $cartLoanOnSaleStatus = $cartLoanOnSale->status;
                    if ($percentSold >= 100) {
                        $cartLoanOnSaleStatus = CartSecondaryLoans::LOAN_STATUS_SOLD;
                    }

                    $marketSecondaryActive = $loan->marketSecondary->active;
                    if ($sellersCartLoanPrincipalAfter <= 0) {
                        $marketSecondaryActive = 0;
                    }

                    $addUpdatesSeller[$loan->marketSecondary->market_secondary_id] = [
                        'cart_loan_on_sale' => $cartLoanOnSale->cart_loan_id,
                        'principal_for_sale' => $sellersCartLoanPrincipalAfter,
                        'price' => $newPrice,
                        'status' => $cartLoanOnSaleStatus,
                        'investment_id' => $loan->marketSecondary->investment_id,
                        'active' => $marketSecondaryActive,
                        'percent_sold' => $percentSold,
                    ];
                } catch (Throwable $e) {
                    echo $e->getMessage() . " " . $e->getFile() . " " . $e->getLine() . PHP_EOL;
                    Log::channel('invest_service')->error($e->getTraceAsString());
                }
            }

            echo "Inserts :".PHP_EOL;
            print_r($insertsSeller['seller']);
            print_r($insertsBuyer['buyer']);

            echo "Updates :".PHP_EOL;
            print_r($addUpdatesSeller);
            print_r($buyerLoansUpdate);

            echo "Wallets :".PHP_EOL;
            print_r($sellersWallets);
            print_r($buyersWallet);

            echo "Buyer portfolio :".PHP_EOL;
            print_r($buyerPortfolioQualityData);
            print_r($buyerPortfolioMaturityData);

            // multiple updates/inserts
            if (
                isset($insertsSeller['seller']) &&
                $insertsSeller['seller'] &&
                isset($insertsBuyer['buyer']) &&
                $insertsBuyer['buyer'] &&
                $addUpdatesSeller &&
                $buyerLoansUpdate &&
                $sellersWallets &&
                $buyersWallet &&
                $buyerPortfolioQualityData &&
                $buyerPortfolioMaturityData
            ) {
                // Need to delete installments for outdated investments
                // Then generate new ones for actual investments
                if ($sellerInstallments) {
                    $this->deleteOldInstallments($sellerInstallments);
                }

                $this->addInsertsUpdatesSecondaryMarketSellers($insertsSeller['seller'], $addUpdatesSeller);
                $this->addInsertsSecondaryMarket($insertsBuyer['buyer']);

                $this->updateCartLoansBuyer($buyerLoansUpdate);

                $this->bunch->addCount(count($investedInLoans));

                if ($oldSellerInvestmentsIds) {
                    Investment::destroy($oldSellerInvestmentsIds);
                }

                $this->saveSellerWallet($sellersWallets);
                $this->saveBuyerWallet($buyersWallet);

                // Update portfolio. It's done in a little bit stupid way
                // Probably need to refactor it later
                $this->updatePortfolio($buyerPortfolioQualityData);
                $this->updatePortfolio($buyerPortfolioMaturityData);

                if ($sellerPortfolioQualityData) {
                    $this->updatePortfolio($sellerPortfolioQualityData);
                }

                if ($sellerPortfolioMaturityData) {
                    $this->updatePortfolio($sellerPortfolioMaturityData);
                }
            }

            // Even when we have no deal we still have to save reason.
            // That's why we need to commit transaction
            DB::commit();
        } catch (Throwable $e) {
            DB::rollback();

            $msg = 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine() . PHP_EOL;

            $this->details[time()] = $msg;
            Log::channel('invest_service')->error(
                'Error! '
                . 'investor #' . $this->bunch->investor_id . ', '
                . 'amount = ' . $this->amountRequired . ', '
                . $msg
            );
        }

        return $investedInLoans;
    }

    private function calculateCartBought(MarketSecondary $marketSecondary, CartSecondaryLoans $buyersLoan): float
    {
        return (float)$buyersLoan->principal_for_sale * 100 / $marketSecondary->principal_for_sale;
    }

    private function calculateMarketSold(MarketSecondary $marketSecondary, CartSecondaryLoans $buyersLoan): float
    {
        return (float)$buyersLoan->principal_for_sale * 100 / $marketSecondary->principal_for_sale;
    }

    public function populateWalletsArray(
        int $walletId,
        array $walletArray,
        float $priceTotal,
        float $principalForSale,
        float $premium
    ): array
    {
        $realPremium = CartLoanHelper::calculateRealPremium($premium, $principalForSale);
        if(isset($walletArray[$walletId]) && $walletArray[$walletId]) {
            $walletArray[$walletId] = [
                'priceTotal' => $priceTotal,
                'principal' => $walletArray[$walletId]['principal'] + $principalForSale,
                'premium' => $walletArray[$walletId]['premium'] + $realPremium,
            ];

            return $walletArray;
        }

        $walletArray[$walletId] = [
            'priceTotal' => $priceTotal,
            'principal' => $principalForSale,
            'premium' => $realPremium,
        ];

        return $walletArray;
    }
}
