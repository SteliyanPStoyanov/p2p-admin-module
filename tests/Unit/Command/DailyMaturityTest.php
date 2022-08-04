<?php

namespace Tests\Unit\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Tests\Unit\Invest\InvestTest;

class DailyMaturityTest extends InvestTest
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMaturityPortfolio()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $loanAmount = 1222.46;
        $remainingPricipal = 1222.46;
        $interestRate = 14.50;
        $issueDate = Carbon::today()->toDateString();
        $listingDate = Carbon::today()->toDateString();
        $finalPaymentDate = Carbon::today()->addMonths(2);

        $loan = $this->preapreLoan(
            $loanAmount,
            $loanAmount,
            $remainingPricipal,
            $interestRate,
            10, // originator percent
            2, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            $finalPaymentDate
        );
        $installments = $this->createLoanInstallmentsSecond($loan, $issueDate);


        $investorDeposit = 1000;
        $investor = $this->getTestInvestor('investor3_' . time() . '@test2.com');
        $wallet = $this->getInvestorWallet($investor->investor_id, $investorDeposit, $currencyId);
        $portfolios = $this->getInvestorPortfolios($investor->investor_id);


        // do invest
        $investorBuyAmount = 200;
        $now = Carbon::parse($issueDate);
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $installments,
            $now
        );

        $this->assertEquals(true, $invested);
        $maturityPortfolio = $portfolios['maturity']->refresh();

        // Test is correct in doInvest
        $this->assertEquals(1, $maturityPortfolio->range1);
        $this->assertEquals(0, $maturityPortfolio->range2);
        $this->assertEquals(0, $maturityPortfolio->range3);
        $this->assertEquals(0, $maturityPortfolio->range4);
        $this->assertEquals(0, $maturityPortfolio->range5);

        // Ok lets call daily maturity update and payment status so we check is the portfolios has changed(dont have to)
        \Artisan::call('script:daily-maturity-refresh');

        $maturityPortfolio->refresh();
        $this->assertEquals(1, $maturityPortfolio->range1);
        $this->assertEquals(0, $maturityPortfolio->range2);
        $this->assertEquals(0, $maturityPortfolio->range3);
        $this->assertEquals(0, $maturityPortfolio->range4);
        $this->assertEquals(0, $maturityPortfolio->range5);

        // Ok lets change the final payment date of loan so we check the maturity range of the investor
        $loan->refresh();
        $loan->final_payment_date = Carbon::parse($loan->final_payment_date)->addMonths(3);
        $loan->save();

        // We need to set ranges update at before today or null so the script calculate
        $maturityPortfolio->ranges_updated_at = null;
        $maturityPortfolio->save();

        \Artisan::call('script:daily-maturity-refresh');

        $loan->refresh();
        $maturityPortfolio->refresh();

        $this->assertEquals(0, $maturityPortfolio->range1);
        $this->assertEquals(1, $maturityPortfolio->range2);
        $this->assertEquals(0, $maturityPortfolio->range3);
        $this->assertEquals(0, $maturityPortfolio->range4);
        $this->assertEquals(0, $maturityPortfolio->range5);

        $loan->final_payment_date = Carbon::parse($loan->final_payment_date)->addMonths(3);
        $loan->save();

        // We need to set ranges update at before today or null so the script calculate
        $maturityPortfolio->ranges_updated_at = null;
        $maturityPortfolio->save();

        \Artisan::call('script:daily-maturity-refresh');

        $loan->refresh();
        $maturityPortfolio->refresh();

        $this->assertEquals(0, $maturityPortfolio->range1);
        $this->assertEquals(0, $maturityPortfolio->range2);
        $this->assertEquals(1, $maturityPortfolio->range3);
        $this->assertEquals(0, $maturityPortfolio->range4);
        $this->assertEquals(0, $maturityPortfolio->range5);

        $loan->final_payment_date = Carbon::parse($loan->final_payment_date)->addMonths(4);
        $loan->save();

        // We need to set ranges update at before today or null so the script calculate
        $maturityPortfolio->ranges_updated_at = null;
        $maturityPortfolio->save();

        \Artisan::call('script:daily-maturity-refresh');

        $loan->refresh();
        $maturityPortfolio->refresh();

        $this->assertEquals(0, $maturityPortfolio->range1);
        $this->assertEquals(0, $maturityPortfolio->range2);
        $this->assertEquals(0, $maturityPortfolio->range3);
        $this->assertEquals(1, $maturityPortfolio->range4);
        $this->assertEquals(0, $maturityPortfolio->range5);

        $loan->final_payment_date = Carbon::today()->subMonth();
        $loan->save();

        // We need to set ranges update at before today or null so the script calculate
        $maturityPortfolio->ranges_updated_at = null;
        $maturityPortfolio->save();

        \Artisan::call('script:daily-maturity-refresh');

        $loan->refresh();
        $maturityPortfolio->refresh();

        $this->assertEquals(0, $maturityPortfolio->range1);
        $this->assertEquals(0, $maturityPortfolio->range2);
        $this->assertEquals(0, $maturityPortfolio->range3);
        $this->assertEquals(0, $maturityPortfolio->range4);
        $this->assertEquals(1, $maturityPortfolio->range5);

        $this->removeTestData($investor, $loan);
    }

    public function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });

        parent::tearDown();
    }
}
