<?php

namespace Tests\Unit\Installment;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Tests\TestCase;

class InvestorInstallmentDaysCountTest extends TestCase
{
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testInvestorInstallmentPeriod()
    {
        // ----------------------------------------------------------------

        $loanListingDate = Carbon::parse('2021-03-05');
        $buyDate = Carbon::parse('2021-03-10');
        $dueDate = Carbon::parse('2021-03-15');
        $prevDueDate = null;
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountForInvestor(
            $loanListingDate,
            $buyDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 10);

        // // ----------------------------------------------------------------

        $loanListingDate = Carbon::parse('2021-03-05');
        $buyDate = Carbon::parse('2021-03-15');
        $dueDate = Carbon::parse('2021-03-15');
        $prevDueDate = null;
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountForInvestor(
            $loanListingDate,
            $buyDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 10);

        // // ----------------------------------------------------------------
        // March is 31 so the diff will be 31 too

        $loanListingDate = Carbon::parse('2021-03-05');
        $buyDate = Carbon::parse('2021-03-30');
        $dueDate = Carbon::parse('2021-04-15');
        $prevDueDate = Carbon::parse('2021-03-15');
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountForInvestor(
            $loanListingDate,
            $buyDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 31);

        // // ----------------------------------------------------------------
        // April is 30 so the diff will be 30 too

        $loanListingDate = Carbon::parse('2021-04-05');
        $buyDate = Carbon::parse('2021-04-30');
        $dueDate = Carbon::parse('2021-05-15');
        $prevDueDate = Carbon::parse('2021-04-15');
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountForInvestor(
            $loanListingDate,
            $buyDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 30);

        // // ----------------------------------------------------------------
        // March is 31 so the diff will be 31 too
        $loanListingDate = Carbon::parse('2021-03-05');
        $buyDate = Carbon::parse('2021-03-30');
        $dueDate = Carbon::parse('2021-04-15');
        $prevDueDate = Carbon::parse('2021-03-15');
        $previousInstallmentPaid = true;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountForInvestor(
            $loanListingDate,
            $buyDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 31);

        // // ----------------------------------------------------------------
        // April is 30 so the diff will be 30 too

        $loanListingDate = Carbon::parse('2021-04-05');
        $buyDate = Carbon::parse('2021-04-30');
        $dueDate = Carbon::parse('2021-05-15');
        $prevDueDate = Carbon::parse('2021-04-15');
        $previousInstallmentPaid = true;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountForInvestor(
            $loanListingDate,
            $buyDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 30);

        // // ----------------------------------------------------------------

        $loanListingDate = Carbon::parse('2021-01-05');
        $buyDate = Carbon::parse('2021-02-30');
        $dueDate = Carbon::parse('2021-04-15');
        $prevDueDate = Carbon::parse('2021-03-15');
        $previousInstallmentPaid = true;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountForInvestor(
            $loanListingDate,
            $buyDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 100);
    }
}
