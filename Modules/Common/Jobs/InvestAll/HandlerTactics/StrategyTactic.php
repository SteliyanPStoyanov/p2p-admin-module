<?php

namespace Modules\Common\Jobs\InvestAll\HandlerTactics;

use \Exception;
use \Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Wallet;
use Modules\Common\Jobs\InvestAll\BeforeInvestingCheck;
use Modules\Common\Jobs\InvestAll\HandlerTactics\HandlerTacticInterface;
use Modules\Common\Jobs\InvestAll\HandlerTactics\Tactic;

class StrategyTactic extends Tactic implements HandlerTacticInterface
{
    private $totalInvestedAmount = 0;

	public function __construct(InvestmentBunch $bunch)
	{
		parent::__construct($bunch);
		$this->setStrategy();
	}

	public function check(): bool
	{
		if ($this->bunch->isFull()) {
			$this->errors[] = 'Bunch is full(invested count >= total possible)';
            return false;
        }

		if (empty($this->strategy->invest_strategy_id)) {
			$this->errors[] = 'Bunch has no invest_strategy_id';
			return false;
		}

		if ($this->strategy->isDeleted()) {
			$this->errors[] = 'Strategy is deleted';
			return false;
		}

		if (!$this->strategy->isActive()) {
			$this->errors[] = 'Strategy is not active';
			return false;
		}

		if (!$this->strategy->isAgreed()) {
			$this->errors[] = 'Strategy is not agreed';
			return false;
		}

		if ($this->strategy->isFull()) {
			$this->errors[] = 'Strategy is full(portfolio_size >= max_portfolio_size)';
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
            null, // min amount is flexible for invest strategies
            $this->getInvestorIdForSkippingInvestedLoans($filters)
		);
	}

	public function findAmountToBuy(Loan $loan): float
	{
		$minPossibleAmount = $this->getMinInvestAmount();


		// basic checks
		if (!$loan->isAvailableAmount($minPossibleAmount)) {
			$this->errors[] = 'Loan has no available amount('
				. $minPossibleAmount . ' ~ ' . $loan->amount_available . ')';
			return 0;
		}
		$wallet = $this->getWallet();
		if (empty($wallet->wallet_id)) {
			$this->errors[] = 'Failed to get wallet';
			return 0;
		}
		$strategy = $this->strategy;


        $amount = $this->findAmount(
            $strategy,
            $wallet,
            $loan
        );
        if (empty($amount)) {
            $this->errors[] = 'findAmountToBuy(): Could not find any available amount(wallet/strategy/loan)';
        }


        return $amount;
	}

    public function findAmount(
        InvestStrategy $strategy,
        Wallet $wallet,
        Loan $loan
    ): float
    {
        $minPossibleAmount = $this->getMinInvestAmount();


        // define min/max borders
        $maxPrice = (
            !empty($strategy->max_amount)
            ? floatval($strategy->max_amount)
            : 0
        );
        $minPrice = (
            !empty($strategy->min_amount)
            ? floatval($strategy->min_amount)
            : 0
        );
        if ($minPrice < $minPossibleAmount) {
            $minPrice = $minPossibleAmount;
        }


        // upper limit
        if (
            $maxPrice >= $minPrice
            && $loan->isAvailableAmount($maxPrice)
            && $wallet->hasUninvestedAmount($maxPrice)
            && $strategy->openForInvesting($maxPrice)
        ) {
            return $maxPrice;
        }


        // rest of strategy
        $strategyFreeAmount = $strategy->getUninvestedAmount();
        if (
            (
                empty($maxPrice)
                || ($maxPrice >= $minPrice && $maxPrice >= $strategyFreeAmount)
            ) && (
                $minPrice <= $strategyFreeAmount
                && $loan->isAvailableAmount($strategyFreeAmount)
                && $wallet->hasUninvestedAmount($strategyFreeAmount)
            )
        ) {
            return $strategyFreeAmount;
        }


        // rest of loan
        $loanFreeAmount = floatval($loan->amount_available);
        if (
            (
                empty($maxPrice)
                || ($maxPrice >= $minPrice && $maxPrice >= $loanFreeAmount)
            ) && (
                $minPrice <= $loanFreeAmount
                && $wallet->hasUninvestedAmount($loanFreeAmount)
                && $strategy->openForInvesting($loanFreeAmount)
            )
        ) {
            return $loanFreeAmount;
        }


        // rest of wallet
        $walletFreeAmount = $wallet->uninvested;
        if (
            (
                empty($maxPrice)
                || ($maxPrice >= $minPrice && $maxPrice >= $walletFreeAmount)
            ) && (
                $minPrice <= $walletFreeAmount
                && $loan->isAvailableAmount($walletFreeAmount)
                && $strategy->openForInvesting($walletFreeAmount)
            )
        ) {
            return $walletFreeAmount;
        }


        // bottom limit
        if (
            $loan->isAvailableAmount($minPrice)
            && $wallet->hasUninvestedAmount($minPrice)
            && $strategy->openForInvesting($minPrice)
        ) {
            return $minPrice;
        }


        return 0;
    }

    public function massInvest()
    {
        // define main objects
        $wallet = $this->getWallet();
        if (empty($wallet->wallet_id)) {
            $this->errors[] = 'Failed to get wallet';
            return false;
        }

        $amountToBuy = $this->strategy->min_amount;
        if (!empty($amountToBuy) && !$wallet->hasUninvestedAmount($amountToBuy)) {
            $this->errors[] = sprintf(
                'Wallet has no money for investing(%s/%s)',
                $wallet->uninvested,
                $amountToBuy
            );
            return false;
        }

        $this->maxCountToBuy = $this->getMaxCountToBuy($amountToBuy);
        if ($this->maxCountToBuy < 1) {
            $this->errors[] = sprintf(
                'Wrong count to buy(a: %s, w.u: %s, s.a: %s)',
                $amountToBuy,
                $wallet->uninvested,
                $this->strategy->getAvailableAmountForInvestments()
            );
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

                $this->strategy->refresh();

                // if strategy is full -> stop chunking
                if (!$this->strategy->openForInvesting($amountToBuy)) {
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

    private function massInvestForReservedLoans(array $loanIds): array
    {
        $investedInLoans = [];

        DB::beginTransaction();
        try {

            $loans = $this->getLoanService()->getBlockedLoansByIds($loanIds);
            if (empty($loans) || count($loans) < 1) {
                throw new Exception("Can not get blocked loans");
            }


            $wallet = $this->getBlockedWallet();
            $strategy = $this->getBlockedStrategy();
            $portfolioQuality = $this->getBlockedQuality();
            $portfolioMaturity = $this->getBlockedMaturity();
            $inserts = $this->getEmptyInserts();

            foreach ($loans as $key => $loan) {

                // mandatory checks
                $check = new BeforeInvestingCheck(
                    $wallet,
                    $loan,
                    $this->totalInvestedInLoans,
                    $this->maxCountToBuy,
                    $strategy->min_amount,
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


                // check if strategy is full
                if (!$strategy->openForInvesting($strategy->min_amount)) {
                    $this->details[$loan->getId()] = sprintf(
                        'Strategy is full(%s/%s) | Action: break',
                        $strategy->getAvailableAmountForInvestments(),
                        $strategy->max_portfolio_size
                    );
                    break;
                }


                // search for possible amount - check loan, wallet and strategy
                // all of them should have available/free amount
                $amountToBuy = $this->findAmount(
                    $strategy,
                    $wallet,
                    $loan
                );
                if (empty($amountToBuy)) {
                    $this->details[$loan->getId()] = sprintf(
                        'Can not define amount, l#%s - l.a:%s, w#%s - w.u:%s, s:%s',
                        $loan->getId(),
                        $loan->amount_available,
                        $wallet->wallet_id,
                        $wallet->uninvested,
                        $strategy->getAvailableAmountForInvestments()
                    );
                    continue;
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

                $strategy->portfolio_size = (float) $strategy->portfolio_size + $amountToBuy;
                $strategy->total_invested = (float) $strategy->total_invested + $amountToBuy;
                $this->totalInvestedAmount += $amountToBuy;


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


            if (empty($inserts['investments'])) {
                Log::channel('invest_service')->error(
                    'Error(ST)! Could not prepare data for investing, '
                    . 'investor #' . $this->bunch->investor_id . ', '
                    . 'bunch #' . $this->bunch->investment_bunch_id . ', '
                    . 'loanIds: ' . implode(', ', $loanIds) . ', '
                    . 'InvestedInLoans: ' . implode(', ', $investedInLoans)
                );

                throw new Exception("Empty array with investments, rollback transaction");
            }
            // multiple inserts
            $this->addInserts($inserts);


            // updates
            $wallet->save();
            $strategy->save();
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
                'Error(ST)! '
                . 'investor #' . $this->bunch->investor_id . ', '
                . 'strategy #' . $this->bunch->invest_strategy_id . ', '
                . 'bunch #' . $this->bunch->investment_bunch_id . ', '
                . $msg
            );
        }

        // just for optimization, to not make another query on next lap,
        // and get directly fresh/updated strategy
        if (!empty($strategy->invest_strategy_id)) {
            $this->strategy = $strategy;
        }

        return $investedInLoans;
    }

    protected function getFilters(): array
    {
        $filters = parent::getFilters();

        if ($this->strategy->include_invested != 1) {
            $filters['my_investment'] = 'exclude';
        }

        if (isset($filters['include_invested'])) {
            unset($filters['include_invested']);
        }

        return $filters;
    }

    private function getMaxCountToBuy(float $amount): int
    {
        $wallet = $this->getWallet();
        $possibleCountForWallet = $wallet->getMaxCountToBuy($amount);
        $possibleCountForStrategy = $this->strategy->getMaxPossibleInvestmentsCount($amount);

        if ($possibleCountForStrategy > $possibleCountForWallet) {
            return $possibleCountForWallet;
        }

        return $possibleCountForStrategy;
    }
}
