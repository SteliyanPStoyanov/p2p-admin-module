<?php

namespace Modules\Common\Jobs\InvestAll\HandlerTactics;

use Modules\Common\Entities\Loan;
use Modules\Common\Jobs\InvestAll\HandlerTactics\HandlerTacticInterface;
use Modules\Common\Jobs\InvestAll\HandlerTactics\Tactic;

class CartTactic extends Tactic implements HandlerTacticInterface
{
	public function check(): bool
	{
		return true;
	}

	public function findLoan(int $afterLoanId = null): ?Loan
	{
		return null;
	}
}
