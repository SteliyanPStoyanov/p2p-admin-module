<?php

namespace Modules\Common\Jobs\InvestAll\HandlerTactics;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\InvestorInstallmentHistory;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\LoanAmountAvailable;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\SecondaryMarket\Cart\CartLoanHelper;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Services\InvestService;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\PortfolioService;

class Tactic
{
    protected $portfolioService = null;
    protected $bankAccountId = null;
    protected $investService = null;
    protected $loanService = null;
    protected $strategy = null;
    protected $investor = null;
    protected $filters = null;
    protected $wallet = null;
    protected $bunch = null;
    protected $cart = null;

    protected int $maxCountToBuy = 0;
    protected int $chunkCount = 100;

    protected array $totalInvestedInLoans = [];
    protected array $details = [];
    protected array $errors = [];

    public function __construct(InvestmentBunch $bunch)
    {
        $this->bunch = $bunch;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function getInvestedLoans(): array
    {
        return $this->totalInvestedInLoans;
    }

    public function setStrategy(): void
    {
        $this->strategy = (
            !empty($this->bunch->invest_strategy_id)
            ? $this->bunch->investStrategy()
            : null
        );

        if (empty($this->strategy->invest_strategy_id)) {
            throw new Exception(
                'Failed to get strategy for bunch #'
                . $this->bunch->investment_bunch_id
            );
        }
    }

    public function setCart(): void
    {
        $this->cart = (!empty($this->bunch->cart_secondary_id)
            ? $this->bunch->cartSecondary
            : null
        );

        if (empty($this->cart->cart_secondary_id)) {
            throw new Exception(
                'Failed to get secondary cart for bunch #'
                . $this->bunch->cart_secondary_id
            );
        }
    }

    public function getCart(bool $refresh = false): ?CartSecondary
    {
        if ($refresh) {
            $this->cart->refresh();
        }

        return $this->cart;
    }

    public function getStrategy(bool $refresh = false): ?InvestStrategy
    {
        if ($refresh) {
            $this->strategy->refresh();
        }

        return $this->strategy;
    }

    public function getLoanService(): LoanService
    {
        if ($this->loanService === null) {
            $this->loanService = \App::make(LoanService::class);
        }

        return $this->loanService;
    }

    public function getInvestService(): InvestService
    {
        if ($this->investService === null) {
            $this->investService = \App::make(InvestService::class);
        }

        return $this->investService;
    }

    public function getPortfolioService(): PortfolioService
    {
        if ($this->portfolioService === null) {
            $this->portfolioService = \App::make(PortfolioService::class);
        }

        return $this->portfolioService;
    }

    public function getWallet(bool $refresh = false)
    {
        if (null === $this->wallet) {
            $this->wallet = $this->getInvestor()->wallet();
        } elseif ($refresh) {
            $this->wallet->refresh();
        }

        return $this->wallet;
    }

    public function getBlockedWallet()
    {
        return $this->getInvestor()->getWalletBlockedForUpdate();
    }

    public function getBlockedCart()
    {
        return $this->getCart()->getBlockedCart();
    }

    public function getBlockedCartLoans()
    {
        return $this->getCart()->getBlockedCartLoans();
    }

    public function getBlockedCartLoansByIds(array $loan_ids)
    {
        return $this->getCart()->getBlockedCartLoansByIds($loan_ids);
    }

    public function getBlockedCartLoan(int $loanId)
    {
        return $this->getCart()->getBlockedCartLoan($loanId);
    }

    public function getBlockedLoans(array $loanIds)
    {
        return Loan::getBlockedLoans($loanIds);
    }

    public function getBlockedQuality()
    {
        return $this->getInvestor()->getQualityPortfolioBlockedForUpdate();
    }

    public function getBlockedMaturity()
    {
        return $this->getInvestor()->getMaturityPortfolioBlockedForUpdate();
    }

    public function getBlockedStrategy(): InvestStrategy
    {
        return InvestStrategy::where([
            'invest_strategy_id' => $this->bunch->invest_strategy_id,
        ])->lockForUpdate()->first();
    }

    public function walletHasMoney(float $amount = 0): bool
    {
        if (empty($amount)) {
            $amount = $this->getMinInvestAmount();
        }

        return $this->getWallet()->hasUninvestedAmount($amount);
    }

    public function getMinInvestAmount()
    {
        return \SettingFacade::getMinAmountForInvest();
    }

    public function getMainBancAccountId()
    {
        if (null === $this->bankAccountId) {
            $this->bankAccountId = $this->getInvestor()->getMainBankAccountId();
        }

        return $this->bankAccountId;
    }

    public function getInvestor()
    {
        if (null === $this->investor) {
            $this->investor = $this->bunch->investor;
        }

        return $this->investor;
    }

    public function getLoanIdsFromCollection(Collection $loans): array
    {
        $loanIds = [];

        $loans->each(function ($row, $index) use (&$loanIds) {
            $loanIds[] = $row->loan_id;
        });

        return $loanIds;
    }

    public function getLoanIdsFromCollectionSecondaryMarket(
        Collection $loans
    ): array {
        $loanIds = [];

        $loans->each(function ($row, $index) use (&$loanIds) {
            $loanIds[] = $row->cart_loan_id;
        });

        return $loanIds;
    }

    public function createUniqueKey(
        int $investorId,
        int $loanId,
        float $amountToBuy
    ): string {
        return md5(
            $investorId
            . '|' . $loanId
            . '|' . $amountToBuy
            . '|' . time()
        );
    }

    /**
     * @param Wallet $wallet
     * @param Loan $loan
     * @param float $amountToBuy
     * @param string $uniqueKey
     * @param int $bunchId
     * @param int $parentId,
     * @param int $secondaryMarketId
     * @return array
     */
    public function prepareInvestment(
        Wallet $wallet,
        Loan $loan,
        float $amountToBuy,
        string $uniqueKey,
        int $bunchId,
        int $parentId = 0
    ): array {
        $percentDetails = $this->getInvestService()->getInvestPercentAndDetails($amountToBuy, $loan);

        return [
            'investment_bunch_id' => $bunchId,
            'investor_id' => $wallet->investor_id,
            'wallet_id' => $wallet->wallet_id,
            'loan_id' => $loan->getId(),
            'amount' => $amountToBuy,
            'key' => $uniqueKey,
            'parent_id' => $parentId,
            'percent' => $percentDetails['percent'],
            'details' => $percentDetails['details'],
        ];
    }

    /**
     * @param Wallet $wallet
     * @param Loan $loan
     * @param float $amountToBuy
     * @param string $uniqueKey
     * @return array
     */
    public function prepareTransaction(
        Wallet $wallet,
        Loan $loan,
        float $amountToBuy,
        string $uniqueKey
    ): array {
        return [
            'loan_id' => $loan->getId(),
            'investor_id' => $wallet->investor_id,
            'wallet_id' => $wallet->wallet_id,
            'amount' => $amountToBuy,
            'bank_account_id' => $this->getMainBancAccountId(),
            'currency_id' => Currency::ID_EUR,
            'originator_id' => Originator::ID_ORIG_STIKCREDIT,
            'direction' => Transaction::DIRECTION_IN,
            'type' => Transaction::TYPE_INVESTMENT,
            'details' => 'InvestAll(by amount/filters) in loan #' . $loan->getId(),
            'key' => $uniqueKey,
        ];
    }

    public function prepareTransactionSecondaryMarketBuyer(
        Wallet $wallet,
        MarketSecondary $marketSecondary,
        float $amountToBuy,
        float $premium,
        float $price,
        string $uniqueKey,
        int $sellerId
    ): array {
        return [
            'premium' => [
                'secondary_market_id' => $marketSecondary->getId(),
                'counteragent' => $sellerId,
                'loan_id' => $marketSecondary->loanOnSale->loan_id,
                'investor_id' => $wallet->investor_id,
                'wallet_id' => $wallet->wallet_id,
                'amount' => CartLoanHelper::calculateRealPremium($premium, $amountToBuy), // recalculate percent to real value
                'bank_account_id' => $this->getMainBancAccountId(),
                'currency_id' => Currency::ID_EUR,
                'originator_id' => Originator::ID_ORIG_STIKCREDIT,
                'direction' => Transaction::DIRECTION_OUT,
                'type' => Transaction::TYPE_SECONDARY_MARKET_PREMIUM,
                'details' => Transaction::SECONDARY_MARKET_DETAILS_INVESTMENT_PREMIUM,
                'key' => $uniqueKey,
            ],
            'principal' => [
                'secondary_market_id' => $marketSecondary->getId(),
                'counteragent' => $sellerId,
                'loan_id' => $marketSecondary->loanOnSale->loan_id,
                'investor_id' => $wallet->investor_id,
                'wallet_id' => $wallet->wallet_id,
                'amount' => CartLoanHelper::formatMoney($amountToBuy),
                'bank_account_id' => $this->getMainBancAccountId(),
                'currency_id' => Currency::ID_EUR,
                'originator_id' => Originator::ID_ORIG_STIKCREDIT,
                'direction' => Transaction::DIRECTION_OUT,
                'type' => Transaction::TYPE_SECONDARY_MARKET_BUY,
                'details' => Transaction::SECONDARY_MARKET_DETAILS_INVESTMENT,
                'key' => $uniqueKey,
            ]
        ];
    }

    public function prepareTransactionSecondaryMarketSeller(
        Wallet $wallet,
        MarketSecondary $marketSecondary,
        float $amountToSell,
        float $amountLeft,
        float $premium,
        float $price,
        string $uniqueKey,
        int $buyerId
    ): array {

        $mainBankAccountId = $wallet->investor->getMainBankAccountId();

        $transactionAmount = $amountToSell - $amountLeft;
        return [
            'premium' => [
                'secondary_market_id' => $marketSecondary->getId(),
                'counteragent' => $buyerId,
                'loan_id' => $marketSecondary->loanOnSale->loan_id,
                'investor_id' => $wallet->investor_id,
                'wallet_id' => $wallet->wallet_id,
                'amount' => CartLoanHelper::calculateRealPremium($premium, $transactionAmount),
                'bank_account_id' => $mainBankAccountId,
                'currency_id' => Currency::ID_EUR,
                'originator_id' => Originator::ID_ORIG_STIKCREDIT,
                'direction' => Transaction::DIRECTION_IN,
                'type' => Transaction::TYPE_SECONDARY_MARKET_PREMIUM,
                'details' => Transaction::SECONDARY_MARKET_DETAILS_SALE_PREMIUM,
                'key' => $uniqueKey,
            ],
            'sale' => [
                'secondary_market_id' => $marketSecondary->getId(),
                'counteragent' => $buyerId,
                'loan_id' => $marketSecondary->loanOnSale->loan_id,
                'investor_id' => $wallet->investor_id,
                'wallet_id' => $wallet->wallet_id,
                'amount' => CartLoanHelper::formatMoney($transactionAmount),
                'bank_account_id' => $mainBankAccountId,
                'currency_id' => Currency::ID_EUR,
                'originator_id' => Originator::ID_ORIG_STIKCREDIT,
                'direction' => Transaction::DIRECTION_IN,
                'type' => Transaction::TYPE_SECONDARY_MARKET_SELL,
                'details' => Transaction::SECONDARY_MARKET_DETAILS_SALE,
                'key' => $uniqueKey,
            ]
        ];
    }

    public function prepareInvQualityRanges(
        Wallet $wallet,
        Loan $loan,
        string $qualityRange
    ): array {
        return [
            'investor_id' => $wallet->investor_id,
            'loan_id' => $loan->getId(),
            'range' => $this->getPortfolioService()
                ->getRangeNumberFromRange($qualityRange),
        ];
    }

    public function prepareLoanAmountsAvailable(
        Loan $loan,
        float $amountBefore,
        float $amountAfter,
        string $uniqueKey
    ): array {
        return [
            'loan_id' => $loan->getId(),
            'amount_before' => $amountBefore,
            'amount_after' => $amountAfter,
            'type' => LoanAmountAvailable::TYPE_INVESTMENT,
            'key' => $uniqueKey,
        ];
    }

    protected function getFilters(): array
    {
        if ($this->filters === null) {
            $this->filters = json_decode($this->bunch->filters, true);
        }

        return $this->filters;
    }

    protected function getInvestorIdForSkippingInvestedLoans($filters): int
    {
        if (
            !empty($filters['my_investment'])
            && 'exclude' == $filters['my_investment']
        ) {
            return $this->bunch->investor_id;
        }

        return 0;
    }

    protected function getEmptyInserts(): array
    {
        return [
            'investments' => [],
            'transactions' => [],
            'qualityRanges' => [],
            'availableAmounts' => [],
        ];
    }

    protected function getEmptyInsertsForSecondaryCart(): array
    {
        return [
            'investments' => [],
            'transactions' => [],
            'qualityRanges' => [],
            'availableAmounts' => [],
        ];
    }

    /**
     * Shitty method, but save space and common for all tactics
     *
     * @param array  $inserts          [description]
     * @param string $uniqueKey        [description]
     * @param Wallet $wallet           [description]
     * @param Loan   $loan             [description]
     * @param string $amountToBuy      [description]
     * @param float  $loanAmountBefore [description]
     * @param float  $loanAmountAfter  [description]
     */
    protected function fillInserts(
        array $inserts,
        string $uniqueKey,
        Wallet $wallet,
        Loan $loan,
        string $qualityRange,
        float $amountToBuy,
        float $loanAmountBefore,
        float $loanAmountAfter
    ): array {
        $inserts['investments'][] = $this->prepareInvestment(
            $wallet,
            $loan,
            $amountToBuy,
            $uniqueKey,
            $this->bunch->getId()
        );
        $inserts['transactions'][] = $this->prepareTransaction(
            $wallet,
            $loan,
            $amountToBuy,
            $uniqueKey
        );
        $inserts['qualityRanges'][] = $this->prepareInvQualityRanges(
            $wallet,
            $loan,
            $qualityRange
        );
        $inserts['availableAmounts'][] = $this->prepareLoanAmountsAvailable(
            $loan,
            $loanAmountBefore,
            $loanAmountAfter,
            $uniqueKey
        );

        return $inserts;
    }

    public function fillInsertsSecondaryMarketBuyer(
        array $inserts,
        string $uniqueKey,
        Wallet $wallet,
        MarketSecondary $marketSecondary,
        float $premium,
        float $price,
        string $qualityRange,
        float $amountToBuy,
        int $sellerId
    ): array {

        $inserts['buyer']['investments'][] = $this->prepareInvestment(
            $wallet,
            $marketSecondary->loan,
            $amountToBuy,
            $uniqueKey,
            $this->bunch->getId(),
            $marketSecondary->investment_id
        );

        $inserts['buyer']['transactions'][] = $this->prepareTransactionSecondaryMarketBuyer(
            $wallet,
            $marketSecondary,
            $amountToBuy,
            $premium,
            $price,
            $uniqueKey,
            $sellerId
        );

        $inserts['buyer']['qualityRanges'][] = $this->prepareInvQualityRanges(
            $wallet,
            $marketSecondary->loan,
            $qualityRange
        );

        return $inserts;
    }

    public function fillInsertsSecondaryMarketSeller(
        array $inserts,
        string $uniqueKey,
        Wallet $wallet,
        MarketSecondary $marketSecondary,
        float $premium,
        float $price,
        string $qualityRange,
        float $amountToSell,
        float $amountInvestment,
        float $amountLeft,
        int $buyerId
    ): array {

        if($amountInvestment > 0) {
            $inserts['seller']['investments'][$marketSecondary->market_secondary_id] = $this->prepareInvestment(
                $wallet,
                $marketSecondary->loan,
                $amountInvestment,
                $uniqueKey,
                $this->bunch->getId(),
                $marketSecondary->investment_id,
            );
        }

        $inserts['seller']['transactions'][] = $this->prepareTransactionSecondaryMarketSeller(
            $wallet,
            $marketSecondary,
            $amountToSell,
            $amountLeft,
            $premium,
            $price,
            $uniqueKey,
            $buyerId
        );

        $inserts['seller']['qualityRanges'][] = $this->prepareInvQualityRanges(
            $wallet,
            $marketSecondary->loan,
            $qualityRange
        );

        return $inserts;
    }

    protected function addInserts(array $inserts): void
    {
        $this->getInvestService()->addInvestments($inserts['investments']);
        $this->getInvestService()->addTransactions($inserts['transactions']);
        $this->getInvestService()->addInvestorQualityRanges($inserts['qualityRanges']);
        $this->getInvestService()->addLoanAmountAvailableStats($inserts['availableAmounts']);
    }

    protected function addInsertsSecondaryMarket(
        array $inserts
    ): void
    {

        $this->getInvestService()->addInvestments($inserts['investments']);

        foreach ($inserts['transactions'] as $insert) {
            foreach ($insert as $item) {
                $this->getInvestService()->addTransactions($item);
            }
        }

        $this->getInvestService()->addInvestorQualityRanges($inserts['qualityRanges']);
    }

    public function addInsertsUpdatesSecondaryMarketSellers(array $inserts, array $updates): void
    {
        try {
            foreach ($updates as $secondary_market_id => $update) {
                $investmentId = $update['investment_id'];
                if (isset($inserts['investments'][$secondary_market_id]) && $inserts['investments'][$secondary_market_id]) {
                    $investmentId = $this->getInvestService()->addInvestmentsSecondaryMarket($inserts['investments'][$secondary_market_id]);
                }

                MarketSecondary::where('market_secondary_id', $secondary_market_id)->update([
                    'principal_for_sale' => $update['principal_for_sale'],
                    'price' => $update['price'],
                    'active' => $update['active'],
                    'investment_id' => $investmentId,
                    'percent_sold' => $update['percent_sold'],
                ]);
                // TODO: Should we delete MarketSecondary once it's 100% sold?

                CartSecondaryLoans::where('cart_loan_id', $update['cart_loan_on_sale'])->update([
                    'principal_for_sale' => $update['principal_for_sale'],
                    'price' => $update['price'],
                    'status' => $update['status'],
                    'investment_id' => $investmentId
                ]);
            }

            foreach ($inserts['transactions'] as $insert) {
                foreach ($insert as $item) {
                    $this->getInvestService()->addTransactions($item);
                }
            }

            $this->getInvestService()->addInvestorQualityRanges($inserts['qualityRanges']);

        } catch (\Throwable $e) {
            echo $e->getMessage() . " " . $e->getFile() . " " . $e->getLine() . PHP_EOL;
            Log::channel('invest_service')->error($e->getTraceAsString());
        }
    }

    public function updateCartLoansBuyer(array $updates)
    {
        foreach ($updates as $key => $update) {
            CartSecondaryLoans::where('cart_loan_id', $key)->update($update);
        }
    }

    public function saveSellerWallet(array $walletData)
    {
        foreach ($walletData as $walletId => $item) {
            $wallet = Wallet::find($walletId);
//            $wallet->actualizeAmountsForSale($item['priceTotal']);

            // need to do it this way otherwise wallet->invested become lower then real investment
            $wallet->actualizeAmountForSecondaryMarketSeller($item['principal'], $item['premium']);
            $wallet->save();
        }
    }

    public function saveBuyerWallet(array $walletData)
    {
        foreach ($walletData as $walletId => $item) {
            $wallet = Wallet::find($walletId);
            $wallet->actualizeAmountsForInvestment($item['priceTotal']);
            $wallet->save();
        }
    }

    public function updatePortfolio(array $update)
    {
        foreach ($update as $portfolio_id => $array) {
            $portfolio = Portfolio::find($portfolio_id);
            foreach ($array as $range => $value) {
                $portfolio->$range = $value;
            }
            $portfolio->save();
        }
    }

    public function deleteOldInstallments(array $oldInstallments): void
    {
        echo "Old Installments : ".PHP_EOL;
        print_r($oldInstallments);

        $installmentIds = [];
        $installmentsData = [];
        foreach ($oldInstallments as $item) {
            $installments = InvestorInstallment::where([
                'investor_id' => $item['investor_id'],
                'investment_id' => $item['investment_id']
            ])->get();

            foreach ($installments as $installment) {
                $installmentIds[] = $installment->investor_installment_id;
            }

            $installmentsData = $installments->toArray();
        }

        InvestorInstallmentHistory::insert($installmentsData);

        echo "Investor installments to delete : ".PHP_EOL;
        print_r($installmentIds);

        InvestorInstallment::destroy($installmentIds);
    }
}
