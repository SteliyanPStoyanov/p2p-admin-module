<?php

namespace Tests\Unit\Installment;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Tests\TestCase;

class InvestorInstallmentAmountsTest extends TestCase
{
    use WithoutMiddleware;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testInvestForInstallmentFromPast()
    {
        // define main params
        $remainingPrincipal = 485.73;
        $principal = 19.85;
        $interest = 0.00;
        $investorPercent = 2.05875692257;
        $listingDate = Carbon::parse('2020-12-17');
        $buyDate = Carbon::parse('2020-12-23');
        $installmentDueDate = Carbon::parse('2020-12-17');
        $prevDate = null;


        // do calculations
        $result = InstallmentCalculator::calcInvestorInstallmentAmounts(
            $remainingPrincipal,
            $principal,
            $interest,
            $investorPercent,
            $buyDate,
            $installmentDueDate,
            $prevDate
        );

        // check results
        $this->assertNotEmpty($result);
        $this->assertEquals($result['principal'], 0.41);
        $this->assertEquals($result['interest'], 0);
        $this->assertEquals($result['late_interest'], 0);
        $this->assertEquals($result['accrued_interest'], 0);
        $this->assertEquals($result['total'], 0.41);
    }
}
