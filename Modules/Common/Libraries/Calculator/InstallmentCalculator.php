<?php

namespace Modules\Common\Libraries\Calculator;

use Carbon\Carbon;
use Modules\Common\Libraries\Calculator\CalculatorValues;

class InstallmentCalculator extends Calculator
{
	/**
	 * Calculate installment:
	 * - interest
	 * - late_interest
	 * - total
	 * - status
	 * - due_date
	 *
	 * @param  float       $remainingPricipal
	 * @param  float       $principal
	 * @param  Carbon      $listedDate
	 * @param  Carbon      $dueDate
	 * @param  Carbon|null $prevDueDate
	 *
	 * @return array
	 */
	public static function calcInstallmentAmounts(
		float $remainingPricipal,
		float $principal,
		float $interestPercent,
		Carbon $listingDate,
		Carbon $dueDate,
		Carbon $prevDueDate = null,
		bool $previousInstallmentPaid = false
	): array
	{
		$installment = [];
		$installment['accrued_interest'] = 0.00;
		$installment['interest'] = 0.00;
		$installment['late_interest'] = 0.00;


		// if due date is passed, we do not calc interest(s)
		if ($dueDate->lt($listingDate)) {
            $installment['total'] = $principal;
            return $installment;
        }


        // 1. we need to calc days between
        $days = self::getInstallmentDaysCountGlobal(
        	$listingDate,
        	$dueDate,
        	$prevDueDate,
        	$previousInstallmentPaid // we send this, because of prepaid installments
        );


        // 2. main calculations
        $installment['interest'] = self::calcInstallmentInterest(
        	$remainingPricipal,
        	$interestPercent,
        	$days
        );
        $installment['total'] = $principal + $installment['interest'];


        // Comemnted: only for tests
        // $installment['dates'] = 'listing date: ' . $listingDate->format('Y-m-d') . ', due date: ' . $dueDate->format('Y-m-d') . ', prev date: ' . $prevDueDate->format('Y-m-d');
        // $installment['prev_date'] = (!empty($prevDueDate) ? $prevDueDate->format('Y-m-d') : 'null');
        // $installment['days_between'] = $days;

        return $installment;
	}

	public static function calcInvestorInstallmentAmounts(
		float $remainingPricipal,
		float $installmentPrincipal,
		float $loanInterestRate,
		float $investorPercent,
		Carbon $buyDate,
		Carbon $dueDate,
		Carbon $prevDueDate = null,
		bool $prevInstallmentPaid = false
	): array {

		// 1. calc investor interest and days
		list($interest, $ownDays) = self::calcInvestorInstallmentInterestData(
			$remainingPricipal,
			$loanInterestRate,
			$buyDate,
			$dueDate,
			$prevDueDate,
			$prevInstallmentPaid
		);


		// 2. calc investor principal
		$principal = self::calcInvestorPrincipal(
			$installmentPrincipal,
			$investorPercent
		);


		// 3. prepare result with investor installments sums
		$installment = [
			'remaining_principal' => $remainingPricipal,
			'principal' => $principal,
			'accrued_interest' => 0,
			'interest' => $interest,
			'late_interest' => 0,
			'total' => self::round($interest + $principal),
			'days' => $ownDays,
		];


		return $installment;
	}

	/**
	 * calcInvestorPrincipal
	 * @param  float  $installmentPrincipal
	 * @param  float  $investorPercent
	 * @return float
	 */
	public static function calcInvestorPrincipal(
		float $installmentPrincipal,
		float $investorPercent
	): float {
		return self::round(
			$installmentPrincipal / 100 * $investorPercent
		);
	}

	/**
	 * -----------------------------
	 * Formula: I = Oi * R/360 * Di
	 * -----------------------------
	 * Where:
	 * I = interest for investor installment
	 * Oi = outstanding principal
	 * B = buy date
	 * R = interest rate of the loan
	 * Di = owned days
	 * Pi = installment days
	 *
	 * Return [interest, own days]
	 *
	 * @param  float        $outstandingAmount
	 * @param  float        $loanInterestRate
	 * @param  Carbon       $buyDate
	 * @param  Carbon       $dueDate
	 * @param  Carbon|null  $prevDueDate
	 * @param  bool|boolean $prevInstallmentPaid
	 * @return array
	 *
	 * @return array
	 */
	public static function calcInvestorInstallmentInterestData(
		float $outstandingAmount,
		float $loanInterestRate,
		Carbon $buyDate,
		Carbon $dueDate,
		Carbon $prevDueDate = null,
		bool $prevInstallmentPaid = false
	): array {

		$dateBuy = clone $buyDate;
		$dateBuy->hour(00);
		$dateBuy->minute(00);
		$dateBuy->second(00);

		$ownDays = self::getOwnDays($dateBuy, $dueDate, $prevDueDate, $prevInstallmentPaid);
		$interest = self::round(($outstandingAmount * $loanInterestRate / CalculatorValues::DAYS_IN_YEAR * $ownDays) / 100);

		return [$interest, $ownDays];
	}

	/**
	 * Get days count investor will own for 1 installment
	 * @param  Carbon       $buyDate
	 * @param  Carbon       $dueDate
	 * @param  Carbon|null  $prevDueDate
	 * @param  bool|boolean $prevInstallmentPaid
	 * @return int
	 */
	public static function getOwnDays(
		Carbon $buyDate,
		Carbon $dueDate,
		Carbon $prevDueDate = null,
		bool $prevInstallmentPaid = false
	): int {

		// 1) |--[ ]--o----o----o---
		if (is_null($prevDueDate) && $buyDate->lt($dueDate)) {
			return self::simpleDateDiff($dueDate, $buyDate);
		}
		if (!is_null($prevDueDate) && $buyDate->lt($prevDueDate) && !$prevInstallmentPaid && $buyDate->lt($dueDate)) {
			return self::simpleDateDiff($prevDueDate, $dueDate);
		}

		// 2) |----[o]--o----o---
		if (is_null($prevDueDate) && $buyDate->eq($dueDate)) {
			return 0;
		}
		if (!is_null($prevDueDate) && $buyDate->eq($prevDueDate) && !$prevInstallmentPaid && $buyDate->lt($dueDate)) {
			return self::simpleDateDiff($prevDueDate, $dueDate);
		}

		// 3) |----o--[ ]--o----o---
		if (is_null($prevDueDate) && $buyDate->gt($dueDate)) {
			return 0;
		}
		if (!is_null($prevDueDate) && $buyDate->gt($prevDueDate) && !$prevInstallmentPaid && $buyDate->lt($dueDate)) {
			return self::simpleDateDiff($dueDate, $buyDate);
		}

		// 4) |----(x)--[ ]--o----o---
		if (!is_null($prevDueDate) && $buyDate->gt($prevDueDate) && $prevInstallmentPaid && $buyDate->lt($dueDate)) {
			return self::simpleDateDiff($dueDate, $buyDate);
		}

		// 5) |----(x)--[ ]--(x)----o---
		if (!is_null($prevDueDate) && $buyDate->lte($prevDueDate) && $prevInstallmentPaid && $buyDate->lt($dueDate)) {
			return self::simpleDateDiff($dueDate, $buyDate);
		}

		return 0;
	}

	/**
	 * Get installment days count
	 *
	 * Used for generating common installment.
     *
	 * @param  Carbon      $listingDate
	 * @param  Carbon      $buyDate
	 * @param  Carbon      $dueDate
	 * @param  Carbon|null $prevDueDate
	 * @param  bool 	   $previousInstallmentPaid
	 * @return int
	 */
	public static function getInstallmentDaysCountGlobal(
		Carbon $listingDate,
		Carbon $dueDate,
		Carbon $prevDueDate = null,
		bool $previousInstallmentPaid = false
	): int {

		// to be save, we should null the dates, since they are used in date comparing
		$dateListing = clone $listingDate;
		$dateListing->hour(00);
		$dateListing->minute(00);
		$dateListing->second(00);
		if ($dueDate->lt($listingDate)) {
			return 0;
		}


		// for 1st installment - we calculate days between listing and next installment due date
		if (null == $prevDueDate) {
			return self::simpleDateDiff($dateListing, $dueDate);
		}


		// if previous installment is before listing date
		// start will be the listing date
		// start for own days should be buy date
		if ($prevDueDate->lt($dateListing)) {
			return self::simpleDateDiff($dateListing, $dueDate);
		}


		// prepaid in future (previous  installemnt due date is after listing date)
		if ($dateListing->lt($prevDueDate) && $previousInstallmentPaid) {
			return self::simpleDateDiff($dateListing, $dueDate);
		}


		// for other installments we calculcate days between installments(current one and previous)
		return self::simpleDateDiff($prevDueDate, $dueDate);
	}

	public static function simpleDateDiff(Carbon $date1, Carbon $date2)
	{
		$date1t = clone $date1;
		$date1t->hour(00);
		$date1t->minute(00);
		$date1t->second(00);

		$date2t = clone $date2;
		$date2t->hour(00);
		$date2t->minute(00);
		$date2t->second(00);

		return $date1t->diffInDays($date2t);

		// $start = strtotime($date1->format('Y-m-d'));
		// $end = strtotime($date2->format('Y-m-d'));
		// return (int) ceil(abs($end - $start) / 86400);
	}

	public static function calcInstallmentInterest(
		float $remPrincipal,
		float $interestPercent,
		int $daysPast
	):float {
		return self::round(
			$remPrincipal * $daysPast * $interestPercent / CalculatorValues::DAYS_IN_YEAR / 100
		);
	}

	public static function calcAccruedInterest(
		Carbon $today,
		Carbon $nextDueDate,
		Carbon $previousDueDate,
		float $investorInstallmentInterest
	)
	{
		return self::round(
            self::simpleDateDiff($today, $previousDueDate)
			/ self::simpleDateDiff($nextDueDate, $previousDueDate)
			* $investorInstallmentInterest
		);
	}

	public static function calcLateInterest(
		Carbon $today,
		Carbon $previousDueDate,
		float $investorInstallmentPrincipal,
		float $loanInterestRatePercent
	)
	{
		return self::round(
            self::simpleDateDiff($today, $previousDueDate)
			* $investorInstallmentPrincipal
			* $loanInterestRatePercent
			/ CalculatorValues::DAYS_IN_YEAR
			/ 100
		);
	}
}
