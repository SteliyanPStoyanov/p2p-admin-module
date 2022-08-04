<?php

namespace Tests\Unit\Installment;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Tests\TestCase;

class InstallmentDaysCountTest extends TestCase
{
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testInstallmentPeriod()
    {
        $today = Carbon::parse('2021-01-26');

        // ----------------------------------------------------------------

        $loanListingDate = Carbon::parse('2020-12-15');
        $dueDate = Carbon::parse('2020-12-25');
        $prevDueDate = null;
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 10);

        // // ----------------------------------------------------------------

        $loanListingDate = Carbon::parse('2020-12-15');
        $dueDate = Carbon::parse('2021-01-25');
        $prevDueDate = Carbon::parse('2020-12-25');
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 31);

        // // ----------------------------------------------------------------

        $loanListingDate = Carbon::parse('2020-12-15');
        $dueDate = Carbon::parse('2021-02-25');
        $prevDueDate = Carbon::parse('2021-01-25');
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 31);

        // // ----------------------------------------------------------------

        $loanListingDate = Carbon::parse('2020-12-15');
        $dueDate = Carbon::parse('2021-03-25');
        $prevDueDate = Carbon::parse('2021-02-25');
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 28);

        // ----------------------------------------------------------------

        $loanListingDate = Carbon::parse('2020-12-20');
        $dueDate = Carbon::parse('2020-12-25');
        $prevDueDate = Carbon::parse('2020-11-25');
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 5);

        // ----------------------------------------------------------------
        // No previous installment

        $loanListingDate = Carbon::parse('2021-01-01');
        $dueDate = Carbon::parse('2021-01-20');
        $prevDueDate = null;
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 19);

        // ----------------------------------------------------------------
        // Previous installment date less then listing - not paid

        $loanListingDate = Carbon::parse('2021-01-01');
        $dueDate = Carbon::parse('2021-01-20');
        $prevDueDate = Carbon::parse('2020-12-20');
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 19);

        // ----------------------------------------------------------------
        // Previous installment date less then listing - paid

        $loanListingDate = Carbon::parse('2021-01-01');
        $dueDate = Carbon::parse('2021-01-20');
        $prevDueDate = Carbon::parse('2020-12-20');
        $previousInstallmentPaid = true;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 19);

        // ----------------------------------------------------------------
        // Previous installment is prepaid in future(after listing date)

        $loanListingDate = Carbon::parse('2021-01-01');
        $dueDate = Carbon::parse('2021-02-20');
        $prevDueDate = Carbon::parse('2021-01-20');
        $previousInstallmentPaid = true;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 50);

        // ----------------------------------------------------------------
        // Normal, buy at prev.installment in the listing date - not paid

        $loanListingDate = Carbon::parse('2021-01-20');
        $dueDate = Carbon::parse('2021-02-20');
        $prevDueDate = Carbon::parse('2021-01-20');
        $previousInstallmentPaid = false;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 31);

        // ----------------------------------------------------------------
        // Normal, buy at prev.installment in the listing date - paid

        $loanListingDate = Carbon::parse('2021-01-20');
        $dueDate = Carbon::parse('2021-02-20');
        $prevDueDate = Carbon::parse('2021-01-20');
        $previousInstallmentPaid = true;

        $installmentDaysCount = InstallmentCalculator::getInstallmentDaysCountGlobal(
            $loanListingDate,
            $dueDate,
            $prevDueDate,
            $previousInstallmentPaid
        );
        $this->assertEquals($installmentDaysCount, 31);
    }
}
