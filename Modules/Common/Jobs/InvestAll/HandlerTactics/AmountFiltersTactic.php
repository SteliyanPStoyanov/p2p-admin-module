<?php

namespace Modules\Common\Jobs\InvestAll\HandlerTactics;

use \Exception;
use \Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\Loan;
use Modules\Common\Jobs\InvestAll\BeforeInvestingCheck;
use Modules\Common\Jobs\InvestAll\HandlerTactics\HandlerTacticInterface;
use Modules\Common\Jobs\InvestAll\HandlerTactics\Tactic;

class AmountFiltersTactic extends Tactic implements HandlerTacticInterface
{
	public function check(): bool
	{
		if (empty($this->bunch->amount)) {
			$this->errors[] = 'No bunch amount';
            return false;
        }

		return true;
	}

	public function findLoan(int $afterLoanId = null): ?Loan
	{
		$filters = $this->getFilters();
		return $this->getLoanService()->getLoanForInvestAll(
			$filters,
            $afterLoanId, // get loans with loan_id > $this->loanId, always NULL for the first run
            $this->bunch->amount,
            $this->getInvestorIdForSkippingInvestedLoans($filters)
		);
	}

	public function findAmountToBuy(Loan $loan): float
	{
		if (empty($this->bunch->amount)) {
			$this->errors[] = 'No bunch amount';
			return 0;
		}

		if (!$loan->isAvailableAmount($this->bunch->amount)) {
			$this->errors[] = 'Loan has no available amount('
				. $this->bunch->amount . ' ~ ' . $loan->amount_available . ')';
			return 0;
		}

		$wallet = $this->getWallet();
		if (empty($wallet->wallet_id)) {
			$this->errors[] = 'Failed to get wallet';
			return 0;
		}
		if (!$wallet->hasUninvestedAmount($this->bunch->amount)) {
			$this->errors[] = 'Wallet has no uninvested amount('
				. $this->bunch->amount . '/' . $wallet->uninvested . ')';
			return 0;
		}


		return $this->bunch->amount;
	}

	public function massInvest(): bool
	{
		// define main objects
		$wallet = $this->getWallet();
		if (empty($wallet->wallet_id)) {
			$this->errors[] = 'Failed to get wallet';
			return false;
		}

		$amountToBuy = floatval($this->bunch->amount);
		if (!$wallet->hasUninvestedAmount($amountToBuy)) {
			$this->errors[] = 'Wallet has no money for investing(' . $wallet->uninvested . ')';
			return false;
		}

		$this->maxCountToBuy = $wallet->getMaxCountToBuy($amountToBuy);
		if ($this->maxCountToBuy < 1) {
			$this->errors[] = 'Wrong count to buy(a:' . $amountToBuy . ', w.u:' . $wallet->uninvested . ')';
			return false;
		}


		// prepare query builder with filtered/sorted data
		$filters = $this->getFilters();
		$builder = $this->getLoanService()->getBuilderForInvestAll(
			$filters,
			null, // after loan_id - not used in mass invest for now
			$amountToBuy,
			$this->getInvestorIdForSkippingInvestedLoans($filters)
		);
		if (null === $builder) {
			$this->errors[] = 'Failed to get builder';
			return false;
		}


		// no loans found
		if ($builder->count() < 1) {
			$this->errors[] = 'No loans found';
			return false;
		}


		// start to get loans on chunks and proceed every chunk separately as mass invest operation
		$chunkSize = (
			$this->maxCountToBuy > $this->chunkCount
			? $this->chunkCount
			: $this->maxCountToBuy
		);
		dump( '$chunkSize = ' . $chunkSize );


		$startTotal = microtime(true);


		$lap = 1;
		$builder->chunkById(
			$chunkSize,
			function ($loanIdsCollection) use ($amountToBuy, &$lap) {

				$start = microtime(true);
				dump('---- lap = ' . $lap);
				$lap++;


				$investedCount = count($this->totalInvestedInLoans);
				if ($investedCount >= $this->maxCountToBuy) {
					return false; // stop chunking: https://stackoverflow.com/questions/39029449/limiting-eloquent-chunks
				}
				dump(' count = ' . $investedCount . ', max = ' . $this->maxCountToBuy);


				// prepare array with loan IDs from chunk
				$loanIds = $this->getLoanIdsFromCollection($loanIdsCollection);
				dump('chunk count = ' . count($loanIds));

				$investedLoansInChunk = [];
				if (!empty($loanIds)) {
					$investedLoansInChunk = $this->massInvestForReservedLoans(
						$amountToBuy,
						$loanIds
					);
				}


				$end = microtime(true);
				dump('Bunch exec.time: ' . round(($end - $start), 2) . ' sec(s)');


				// if nothing to buy -> stop chunking
				if (empty($investedLoansInChunk)) {
					return false;
				}

				// if we reach the needed counts -> stop chunking
				$investedCount = count($this->totalInvestedInLoans);
				if ($investedCount >= $this->maxCountToBuy) {
					return false;
				}

				// if we dont have a money for next chunk -> stop chunking
				if (!$this->getWallet(true)->hasUninvestedAmount($amountToBuy)) {
					return false;
				}

			},
			'loan_id'
		);


		$endTotal = microtime(true);
		$countInvested = count($this->totalInvestedInLoans);


		dump('totalInvestedInLoans:', $this->totalInvestedInLoans);
		dump('details:', $this->details);
		dump('Total exec.time: ' . round(($endTotal - $startTotal), 2) . ' sec(s), for ' . $countInvested . ' loan(s)');

		return ($countInvested > 0);
	}

    /**
     * Mass invest for loans from chunk
     *
	 * db transaction:
	 * wallet (1 update)
	 * quality (1 update)
	 * maturity (1 update)
	 * investments (1 multipple insert)
	 * transactions (1 multipple insert)
	 * quality range( 1 multipple insert)
	 * loan available stats( 1 multipple insert)
	 * loans amount available reduce (many unique update)
	 * bunch count update
	 *
	 * @return array - includes ids of all loans invested by the bunch
	 */
    private function massInvestForReservedLoans(
    	float $amountToBuy,
    	array $loanIds
    ): array
    {
    	$investedInLoans = [];

		DB::beginTransaction();
		try {

        	$loans = $this->getLoanService()->getBlockedLoansByIds($loanIds);
        	if (empty($loans) || count($loans) < 1) {
				throw new Exception("Can not get blocked loans");
			}


			$wallet = $this->getBlockedWallet();
			$portfolioQuality = $this->getBlockedQuality();
			$portfolioMaturity = $this->getBlockedMaturity();
			$inserts = $this->getEmptyInserts();

			foreach ($loans as $key => $loan) {

				// mandatory checks before investing
				$check = new BeforeInvestingCheck(
                    $wallet,
                    $loan,
                    $this->totalInvestedInLoans,
                    $this->maxCountToBuy,
                    $amountToBuy,
                    $this->getPortfolioService()
                );
                if (!$check->isOk()) {
                	$this->details[$check->getKey()] = sprintf(
                        'Msg: %s | Action: %s',
                        $check->getMsg(),
                        $check->getAction()
                    );
                    dump('getAction = ' . $check->getAction() . ' | Msg: ' . $check->getMsg());

                    if ('break' == $check->getAction()) {
                    	break;
                    }

                    if ('continue' == $check->getAction()) {
                    	continue;
                    }
                }


				// get portfolio ranges
				$qualityRange = $check->getQualityRange();
				$maturityRange = $check->getMaturityRange();


				// update loan
				$loanAmountBefore = $loan->amount_available;
				$loan->reduceAmountAvailable($amountToBuy);
				$loanAmountAfter = $loan->amount_available;
				$loan->save();
				$investedInLoans[] = $loan->getId();


				// prepare data for mass inserts
				$uniqueKey = $this->createUniqueKey(
					$wallet->investor_id,
					$loan->getId(),
					$amountToBuy
				);
				$inserts = $this->fillInserts(
                    $inserts,
                    $uniqueKey,
                    $wallet,
                    $loan,
                    $qualityRange,
                    $amountToBuy,
                    $loanAmountBefore,
                    $loanAmountAfter
                );


				// change values of object, without real saving
				$walletAmountBefore = $wallet->uninvested;
				$wallet->actualizeAmountsForInvestment($amountToBuy);
				$walletAmountAfter = $wallet->uninvested;

				$portfolioMaturity->$maturityRange += 1;
				$portfolioQuality->$qualityRange += 1;


				// invest stats
				$this->totalInvestedInLoans[$loan->getId()] = sprintf(
					'loan #' . $loan->getId() . ', '
					. 'amount = ' . $amountToBuy . ', '
					. 'uniqueKey =' . $uniqueKey . ', '
					. 'loan amount b. =' . $loanAmountBefore . ', '
					. 'loan amount a. =' . $loanAmountAfter . ', '
					. 'wallet amount b. =' . $walletAmountBefore . ', '
					. 'wallet amount a. =' . $walletAmountAfter
				);
			}


			// dump('$investments', $investments);
			if (empty($inserts['investments'])) {
				Log::channel('invest_service')->error(
					'Error(AFT)! Could not prepare data for investing, '
					. 'investor #' . $this->bunch->investor_id . ', '
					. 'bunch #' . $this->bunch->investment_bunch_id . ', '
					. 'amount = ' . $this->bunch->amount . ', '
					. 'loanIds: ' . implode(', ', $loanIds) . ', '
					. 'InvestedInLoans: ' . implode(', ', $investedInLoans)
				);

				throw new Exception("Empty array with investments, rollback transaction");
			}
			// multiple inserts
			$this->addInserts($inserts);


			// updates
			$wallet->save();
			$portfolioQuality->save();
			$portfolioMaturity->save();
			$this->bunch->addCount(count($investedInLoans));


			DB::commit();

        } catch (Throwable $e) {
            DB::rollback();

            $investedInLoans = [];

            $msg = 'msg: ' . $e->getMessage() . ', file: ' . $e->getFile() . ', line: ' . $e->getLine();
            $this->details[time()] = $msg;
            Log::channel('invest_service')->error(
                'Error(AFT)! '
                . 'investor #' . $this->bunch->investor_id . ', '
                . 'bunch #' . $this->bunch->investment_bunch_id . ', '
                . 'amount = ' . $this->bunch->amount . ', '
                . $msg
            );
        }

        return $investedInLoans;
    }
}
