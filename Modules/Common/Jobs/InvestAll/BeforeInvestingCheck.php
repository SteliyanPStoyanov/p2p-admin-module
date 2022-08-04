<?php

namespace Modules\Common\Jobs\InvestAll;

use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\Wallet;
use Modules\Common\Services\PortfolioService;

/**
 * Common checks for every tactic of mass invest
 */
class BeforeInvestingCheck
{
	CONST ACTION_STOP = 'break';
	CONST ACTION_SKIP = 'continue';

	// by default we set negative behavior
	private bool $good = false;
	private string $msg = 'Default error msg';
	private string $action = self::ACTION_SKIP;

	private $portfolioService = '';
	private string $qualityRange = '';
	private string $maturityRange = '';

	private $loan = null;
	private $loanId = null;
	private $wallet = null;
	private $amountToBuy = null;
	private $investedCount = null;
	private $investedLoans = [];
	private $maxInvestedCount = null;

	private $ranges = [
		Portfolio::PORTFOLIO_RANGE_1,
		Portfolio::PORTFOLIO_RANGE_2,
		Portfolio::PORTFOLIO_RANGE_3,
		Portfolio::PORTFOLIO_RANGE_4,
		Portfolio::PORTFOLIO_RANGE_5,
	];

	public function __construct(
		Wallet $wallet,
		Loan $loan,
		array $investedLoans,
		int $maxInvestedCount,
		float $amountToBuy,
		PortfolioService $service
	)
	{
		$this->wallet = $wallet;
		$this->loan = $loan;
		$this->investedCount = count($investedLoans);
		$this->investedLoans = $investedLoans;
		$this->maxInvestedCount = $maxInvestedCount;
		$this->amountToBuy = $amountToBuy;
		$this->portfolioService = $service;

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
            return ;
        }

		// before investing, always check investor free money
		if (!$this->wallet->hasUninvestedAmount($this->amountToBuy)) {
			$this->msg = sprintf(
				'Could not invest in loan #%s, no money(%s)',
				$this->loan->getId(),
				$this->wallet->uninvested
			);
			$this->action = self::ACTION_STOP;
			return ;
		}

        // check if proper loan
        if (empty($this->loan->loan_id)) {
            $this->msg = 'Wrong object, loan has no loan_id';
            return ;
        }

        $this->loanId = $this->loan->getId();
        if (empty($this->loan->payment_status)) {
            $this->msg = 'Wrong object, empty payment_status, #' . $this->loanId;
            return ;
        }
        if (empty($this->loan->final_payment_date)) {
            $this->msg = 'Wrong object, empty final_payment_date, #' . $this->loanId;
            return ;
        }

        // just to make sure for no collision, and to not roll twice same loans
		if (array_key_exists($this->loanId, $this->investedLoans)) {
			$this->msg = 'Already invested #' . $this->loanId;
            return ;
		}

		// check main loan params
		if (!$this->loan->isAvailableAmount($this->amountToBuy)) {
			$this->msg = sprintf(
				'Could not invest in loan #%s, no available amount(%s)',
				$this->loan->getId(),
				$this->loan->amount_available
			);
			return ;
		}

		// get quality and detect range
		$this->qualityRange = $this->portfolioService->getQualityRange(
			$this->loan->payment_status
		);
		if (
			empty($this->qualityRange)
			|| !in_array($this->qualityRange, $this->ranges)
		) {
			$this->msg = sprintf(
				'Failed to get quality range for loan #%s, payment_status = %s, range = %s',
				$this->loan->getId(),
				$this->loan->payment_status,
				$this->qualityRange
			);
			return ;
		}

		// get maturity and detect range
		$this->maturityRange = $this->portfolioService->getMaturityRange(
			$this->loan->final_payment_date
		);
		if (
			empty($this->maturityRange)
			|| !in_array($this->maturityRange, $this->ranges)
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
