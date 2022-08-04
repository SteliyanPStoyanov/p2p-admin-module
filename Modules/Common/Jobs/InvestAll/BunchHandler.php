<?php

namespace Modules\Common\Jobs\InvestAll;

use \Exception;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Wallet;
use Modules\Common\Jobs\InvestAll\HandlerTactics\HandlerTacticInterface;

class BunchHandler
{
	const RETRY_CODE = 808;

	private $handlerTactic = null;
	private $nextStrategy = null;
	private $bunch = null;
	private $retry = false;
	private $errors = [];

	public function __construct(InvestmentBunch $bunch)
	{
		$this->bunch = $bunch;
	}

	/**
	 * Main check function
	 * Check bunch, wallet, strategy, etc..
	 * Every tactic has its own checks
	 *
	 * @return bool
	 */
	public function couldRunBunch(): bool
	{
		// COMMON CHECKS FOR ALL TYPE OF BUNCHES
		if ($this->bunch->isFinished()) {
			$this->errors[] = 'Bunch is already finished';
            return false;
        }

		if (
			intval($this->bunch->total) > 0
			&& intval($this->bunch->count) >= intval($this->bunch->total)
		) {
			$this->errors[] = sprintf(
				'The bunch is full(%s/%s)',
				$this->bunch->count,
				$this->bunch->total
			);
			return false;
		}

        if (!$this->isInvestorReservedForBunch()) {
			$this->errors[] = ' Investor investing via another bunch';
            return false;
        }

        // multirun, and there are another bunches with higher priority
        if (
        	$this->bunch->isMultiRun()
        	&& $this->bunch->hasActiveMultiRunBunchesWithHigherPrioity()
        ) {
        	$msg = 'There are multi-run bunches with higher priority';
        	$this->errors[] = $msg;

        	$this->bunch->addDetails('Skipped, ' . $msg . '.');
            $this->retry = true;

            return false;
        }

        // UNIQUE CHECKS DEPENDS ON BUNCH TYPE AND IT'S TACTIC
        if (!$this->handlerTactic->check()) {
        	$this->errors[] = $this->errors + $this->handlerTactic->getErrors();
            return false;
        }

        return true;
	}

	/**
	 * Looping loans in a chunk according bunch filters
	 * Get blocked loans and proceed next operations:
	 *
		mandatory:
		- wallet (1 update per bunch)
		- quality (1 update per bunch)
		- maturity (1 update per bunch)
		- investments (1 multipple insert per bunch)
		- transactions (1 multipple insert per bunch)
		- quality range ( 1 multipple insert per bunch)
		- investot quality ranges ( 1 multipple insert per bunch)
		- loans amount available reduce (many unique update per bunch)
		- inv bunch (1 update per bunch)

		optional:
		- inv strategy stats
		- cart
		- cart_secondary
		- cart_secondary_pivot
		- loan available stats

		Later on background will be prepared:
		- investor installments
		- loan_contract
		- 'investment_id' na transaction
		- 'investment_id' na loan_amount_available

	 * @return bool
	 */
	public function doMassInvest(): bool
	{
		try {

			return $this->handlerTactic->massInvest();

		} catch(\Throwable $e) {

			$this->errors[] = 'Error! '
				. 'bunch #' . $this->bunch->investment_bunch_id . ', '
				. 'msg: ' . $e->getMessage() . ', '
				. 'file: ' . $e->getFile() . ', '
				. 'line: ' . $e->getLine();

			return false;
		}
	}

	/**
	 * (OLD)
	 * Related to unique investing(one investment per job)
	 * @param  int|null $afterLoanId
	 * @return Loan|null
	 */
	public function getLoan(int $afterLoanId = null): ?Loan
	{
		$loan = $this->handlerTactic->findLoan($afterLoanId);

		if (empty($loan->loan_id)) {
			$this->errors[] = $this->errors + $this->handlerTactic->getErrors();
			return null;
		}

		return $loan;
	}

	/**
	 * (OLD)
	 * Related to unique investing(one investment per job)
	 * @param  Loan $loan
	 * @return float
	 */
	public function getAmountToBuy(Loan $loan): float
	{
		$amount = $this->handlerTactic->findAmountToBuy($loan);

		if (empty($amount)) {
			$this->errors[] = $this->errors + $this->handlerTactic->getErrors();
			return 0;
		}

		// MANDATORY CHECKS

		if ($amount < \SettingFacade::getMinAmountForInvest()) {
			$this->errors[] = 'Too small amount for investing (' . $amount . ')';
			return 0;
		}

		if (!$this->handlerTactic->walletHasMoney($amount)) {
			$this->errors[] = 'Not enough money in wallet (' . $amount . ')';
			return 0;
		}

		if (!$loan->isAvailableAmount($amount)) {
			$this->errors[] = 'No available amount in loan (loan = '
				. $loan->getId() . ', amount' . $amount . ')';
			return 0;
		}

		return $amount;
	}

	public function shouldRetry(): bool
	{
		return (true === $this->retry);
	}

	public function shouldRunNextStrategy(): bool
	{
		// re-run only for invest strategies
		if (empty($this->bunch->invest_strategy_id)) {
			return false;
		}

		// only for multi-run bunches
		if (isset($this->bunch->multi_run) && 1 != $this->bunch->multi_run) {
			return false;
		}

		// only if investor has money
		if (!$this->handlerTactic->walletHasMoney()) {
			return false;
		}

		// before running next strategy, we should check do we have it
		$strategy = $this->getStrategy();
		if (empty($strategy->invest_strategy_id)) {
			return false;
		}
		$nextStrategy = $strategy->getNext();
        if (empty($nextStrategy->invest_strategy_id)) {
        	return false;
        }

        $this->nextStrategy = $nextStrategy;
        return true;
	}

	public function getErrors(): array
	{
		return $this->errors;
	}

	public function getJsonErrors(): string
	{
		return json_encode($this->getErrors());
	}

	public function getJsonDetails(): string
	{
		return json_encode($this->handlerTactic->getDetails());
	}

	public function getJsonInvestedLoans(): string
	{
		return json_encode($this->handlerTactic->getInvestedLoans());
	}

	public function setTactic(HandlerTacticInterface $handlerTactic)
	{
		$this->handlerTactic = $handlerTactic;
	}

	public function getTactic(): HandlerTacticInterface
	{
		return $this->handlerTactic;
	}

	public function getWallet(): ?Wallet
	{
		return $this->handlerTactic->getWallet();
	}

	public function getStrategy(): ?InvestStrategy
	{
		return $this->handlerTactic->getStrategy();
	}

	public function getNextStrategy(): ?InvestStrategy
	{
		return $this->nextStrategy;
	}

	public function getLockedInvestor(): Investor
    {
        return Investor::where('investor_id', $this->bunch->investor_id)
        	->lockForUpdate()
            ->first();
    }

	private function isInvestorReservedForBunch(): bool
	{
		DB::beginTransaction();

        try {

            $investor = $this->getLockedInvestor();
            if (empty($investor->investor_id)) {
                throw new Exception('No investor for bunch #' . $this->bunch->getId());
            }

            if (!$investor->setActiveInvestmentBunch($this->bunch->getId())) {
                throw new Exception('Investor investing via another bunch', self::RETRY_CODE);
            }

            DB::commit();

        } catch (Throwable $e) {

            DB::rollback();

            if (self::RETRY_CODE == $e->getCode()) {
                $this->bunch->addDetails('Skipped, reserved by another bunch.');
                $this->retry = true;
            }

            return false;
        }

        return true;
	}
}
