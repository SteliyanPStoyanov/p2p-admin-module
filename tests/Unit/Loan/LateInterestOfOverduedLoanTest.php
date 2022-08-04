<?php

namespace Tests\Unit\Loan;

use App;
use Carbon\Carbon;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Tests\TestCase;


class LateInterestOfOverduedLoanTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testInvestedAfterLastInstallmentDueDate()
    {
        $date = Carbon::parse('2021-01-23');
        $dueDate = Carbon::parse('2021-01-14');
        $investedAt = Carbon::parse('2021-01-20 18:30:48');
        $investedAmount = 22;
        $loanInterestRatePercent = 13.5;

        if (null !== $investedAt && $dueDate->lt($investedAt)) {
            $dueDate = $investedAt;
        }

        $lateInterest = InstallmentCalculator::calcLateInterest(
            $date,
            $dueDate,
            $investedAmount,
            $loanInterestRatePercent
        );

        $this->assertEquals(0.02, $lateInterest);

        // -----------------------------------------------------

        $date = Carbon::parse('2021-01-23');
        $dueDate = Carbon::parse('2021-01-14');
        $investedAt = Carbon::parse('2021-01-21 18:30:48');
        $investedAmount = 11;
        $loanInterestRatePercent = 13.5;

        if (null !== $investedAt && $dueDate->lt($investedAt)) {
            $dueDate = $investedAt;
        }

        $lateInterest = InstallmentCalculator::calcLateInterest(
            $date,
            $dueDate,
            $investedAmount,
            $loanInterestRatePercent
        );

        $this->assertEquals(0.01, $lateInterest);
    }
}
