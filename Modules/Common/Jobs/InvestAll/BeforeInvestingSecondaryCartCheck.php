<?php

namespace Modules\Common\Jobs\InvestAll;

use Illuminate\Support\Facades\Log;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\Wallet;
use Modules\Common\Services\PortfolioService;

/**
 * Common checks for every tactic of mass invest
 */
class BeforeInvestingSecondaryCartCheck
{
    const ACTION_STOP = 'break';
    const ACTION_SKIP = 'continue';

    // by default we set negative behavior
    private bool $good = false;
    private string $msg = 'Default error msg';
    private string $action = self::ACTION_SKIP;

    private $portfolioService = '';
    private string $qualityRange = '';
    private string $maturityRange = '';

    private $loan = null;
    private $cartLoanOnSale = null;
    private $loanId = null;
    private $wallet = null;
    private $amountToBuy = null;
    private $investedCount = null;
    private $investedLoans = [];
    private $maxInvestedCount = null;
    private float $walletAvailableAmount;

    private $ranges = [
        Portfolio::PORTFOLIO_RANGE_1,
        Portfolio::PORTFOLIO_RANGE_2,
        Portfolio::PORTFOLIO_RANGE_3,
        Portfolio::PORTFOLIO_RANGE_4,
        Portfolio::PORTFOLIO_RANGE_5,
    ];

    public function __construct(
        Wallet $wallet,
        CartSecondaryLoans $loan,
        array $investedLoans,
        int $maxInvestedCount,
        float $amountToBuy,
        PortfolioService $service,
        CartSecondaryLoans $cartLoanOnSale,
        float $walletAvailableAmount
    )
    {

        $this->wallet = $wallet;
        $this->loan = $loan;
        $this->cartLoanOnSale = $cartLoanOnSale;
        $this->investedCount = count($investedLoans);
        $this->investedLoans = $investedLoans;
        $this->maxInvestedCount = $maxInvestedCount;
        $this->amountToBuy = $amountToBuy;
        $this->portfolioService = $service;
        $this->walletAvailableAmount = $walletAvailableAmount;

        $this->validate();
    }

    public function getMsg(): string
    {
        return $this->msg;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getKey(): string
    {
        return $this->loanId ?? time();
    }

    public function getQualityRange(): string
    {
        return $this->qualityRange;
    }

    public function getMaturityRange(): string
    {
        return $this->maturityRange;
    }

    public function isOk(): bool
    {
        return $this->good;
    }

    public function validate()
    {
        // first check do we have something to buy according to counts
        if ($this->investedCount >= $this->maxInvestedCount) {
            $this->msg = sprintf(
                'Completed investing, count is full(%s/%s)',
                $this->investedCount,
                $this->maxInvestedCount
            );

            $this->action = self::ACTION_STOP;

            return;

        }

        // before investing, always check investor free money
        if ($this->walletAvailableAmount < $this->amountToBuy) {
            $this->msg = sprintf(
                'Could not invest in loan #%s, no money(%s)',
                $this->loan->getId(),
                $this->walletAvailableAmount
            );

            $this->action = self::ACTION_SKIP;

            return;
        }

        if (!$this->wallet->hasUninvestedAmount($this->amountToBuy)) {
            $this->msg = sprintf(
                'Could not invest in loan #%s, no money(%s)',
                $this->loan->getId(),
                $this->wallet->uninvested
            );

            $this->action = self::ACTION_SKIP;

            return;

        }

        // check if proper loan
        if (empty($this->loan->cart_loan_id)) {
            $this->msg = 'Wrong object, cart_loan has no cart_loan_id';

            return;

        }

        $this->loanId = $this->loan->getId();

        // just to make sure for no collision, and to not roll twice same loans
        if (array_key_exists($this->loanId, $this->investedLoans)) {
            $this->msg = 'Already invested #' . $this->loanId;


            return ;

        }

        // check main loan params
        // Check if loan on sale has enough principal for sale
        if ($this->cartLoanOnSale->principal_for_sale < $this->loan->principal_for_sale) {
            $this->msg = sprintf(
                'Could not invest in loan #%s, no available amount(%s)',
                $this->cartLoanOnSale->getId(),
                $this->cartLoanOnSale->principal_for_sale
            );

            $this->action = self::ACTION_SKIP;


            return ;

        }

        // get quality and detect range
        $this->qualityRange = $this->portfolioService->getQualityRange(
            $this->loan->loan->payment_status
        );
        if (
            empty($this->qualityRange) ||
            !in_array($this->qualityRange, $this->ranges)
        ) {
            $this->msg = sprintf(
                'Failed to get quality range for loan #%s, payment_status = %s, range = %s',
                $this->loan->getId(),
                $this->loan->loan()->payment_status,
                $this->qualityRange
            );

            return ;
        }

        // get maturity and detect range
        $this->maturityRange = $this->portfolioService->getMaturityRange(
            $this->loan->loan->final_payment_date
        );

        if (
            empty($this->maturityRange) ||
            !in_array($this->maturityRange, $this->ranges)
        ) {
            $this->msg = sprintf(
                'Failed to get maturity range for loan #%s, final_payment_date = %s, range = %s',
                $this->loan->getId(),
                $this->loan->final_payment_date,
                $this->maturityRange
            );
            return ;

        }

        $this->msg = '';
        $this->action = '';
        $this->good = true;
    }
}
