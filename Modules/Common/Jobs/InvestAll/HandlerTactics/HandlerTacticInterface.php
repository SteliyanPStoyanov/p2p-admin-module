<?php

namespace Modules\Common\Jobs\InvestAll\HandlerTactics;

use Modules\Common\Entities\Loan;

interface HandlerTacticInterface
{
	// Important
	public function check();
	public function massInvest();

	// old methods(one by one buiyng)
	// public function findAmountToBuy(Loan $loan);
	// public function findLoan(int $afterLoanId = null);

	// parent include (Tactic)
	public function getDetails();
	public function getErrors();
	public function getInvestedLoans();
	public function getStrategy();
	public function getWallet();
	public function walletHasMoney(float $amount);
}
