<?php

namespace Tests\Unit\Command;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Loan;
use Tests\Unit\Invest\InvestTest;

class DailyPaymentStatusTest extends InvestTest
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testQualityPortfolioAfterPaymentStatus()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $loanAmount = 1222.46;
        $remainingPricipal = 1222.46;
        $interestRate = 14.50;
        $issueDate = '2020-11-02';
        $listingDate = '2020-11-02';
        $finalPaymentDate = Carbon::today()->addMonths(4);

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
        $installments = $this->createLoanInstallmentsThird($loan, $issueDate);

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
        $firstUnpaidInstallment = $installments[1];

        $this->assertEquals(true, $invested);
        $this->assertNotEmpty($firstUnpaidInstallment);
        $qualityPortfolio = $portfolios['quality']->refresh();

        // Test is correct in doInvest
        $this->assertEquals(1, $qualityPortfolio->range1);
        $this->assertEquals(0, $qualityPortfolio->range2);
        $this->assertEquals(0, $qualityPortfolio->range3);
        $this->assertEquals(0, $qualityPortfolio->range4);
        $this->assertEquals(0, $qualityPortfolio->range5);

        // Ok lets call daily payment status so we check is the portfolio has changed(dont have to)
        \Artisan::call('script:daily-payment-status-refresh');

        $qualityPortfolio->refresh();
        $this->assertEquals(1, $qualityPortfolio->range1);
        $this->assertEquals(0, $qualityPortfolio->range2);
        $this->assertEquals(0, $qualityPortfolio->range3);
        $this->assertEquals(0, $qualityPortfolio->range4);
        $this->assertEquals(0, $qualityPortfolio->range5);

        // Ok lets change the payment status updated at so the script will get this loan
        $loan->refresh();
        $loan->payment_status_updated_at = null;
        $loan->save();

        $firstUnpaidInstallment->refresh();
        $firstUnpaidInstallment->due_date = Carbon::today()->subDays(5);
        $firstUnpaidInstallment->save();

        \Artisan::call('script:daily-payment-status-refresh');

        $loan->refresh();
        $qualityPortfolio->refresh();
        $firstUnpaidInstallment->refresh();

        $this->assertEquals(Loan::PAY_STATUS_1_15, $loan->payment_status);
        $this->assertEquals(Loan::PAY_STATUS_1_15, $firstUnpaidInstallment->status);
        $this->assertEquals(0, $qualityPortfolio->range1);
        $this->assertEquals(1, $qualityPortfolio->range2);
        $this->assertEquals(0, $qualityPortfolio->range3);
        $this->assertEquals(0, $qualityPortfolio->range4);
        $this->assertEquals(0, $qualityPortfolio->range5);

        $loan->payment_status_updated_at = null;
        $loan->save();

        $firstUnpaidInstallment->refresh();
        $firstUnpaidInstallment->due_date = Carbon::today()->subDays(20);
        $firstUnpaidInstallment->save();

        \Artisan::call('script:daily-payment-status-refresh');

        $loan->refresh();
        $qualityPortfolio->refresh();
        $firstUnpaidInstallment->refresh();

        $this->assertEquals(Loan::PAY_STATUS_16_30, $loan->payment_status);
        $this->assertEquals(Loan::PAY_STATUS_16_30, $firstUnpaidInstallment->status);
        $this->assertEquals(0, $qualityPortfolio->range1);
        $this->assertEquals(0, $qualityPortfolio->range2);
        $this->assertEquals(1, $qualityPortfolio->range3);
        $this->assertEquals(0, $qualityPortfolio->range4);
        $this->assertEquals(0, $qualityPortfolio->range5);

        $loan->payment_status_updated_at = null;
        $loan->save();

        $firstUnpaidInstallment->refresh();
        $firstUnpaidInstallment->due_date = Carbon::today()->subDays(40);
        $firstUnpaidInstallment->save();

        \Artisan::call('script:daily-payment-status-refresh');

        $loan->refresh();
        $qualityPortfolio->refresh();
        $firstUnpaidInstallment->refresh();

        $this->assertEquals(Loan::PAY_STATUS_31_60, $loan->payment_status);
        $this->assertEquals(Loan::PAY_STATUS_31_60, $firstUnpaidInstallment->status);
        $this->assertEquals(0, $qualityPortfolio->range1);
        $this->assertEquals(0, $qualityPortfolio->range2);
        $this->assertEquals(0, $qualityPortfolio->range3);
        $this->assertEquals(1, $qualityPortfolio->range4);
        $this->assertEquals(0, $qualityPortfolio->range5);

        $loan->payment_status_updated_at = null;
        $loan->save();

        $firstUnpaidInstallment->refresh();
        $firstUnpaidInstallment->due_date = Carbon::today()->subDays(62);
        $firstUnpaidInstallment->save();

        \Artisan::call('script:daily-payment-status-refresh');

        $loan->refresh();
        $qualityPortfolio->refresh();
        $firstUnpaidInstallment->refresh();

        $this->assertEquals(Loan::PAY_STATUS_LATE, $loan->payment_status);
        $this->assertEquals(Loan::PAY_STATUS_LATE, $firstUnpaidInstallment->status);
        $this->assertEquals(0, $qualityPortfolio->range1);
        $this->assertEquals(0, $qualityPortfolio->range2);
        $this->assertEquals(0, $qualityPortfolio->range3);
        $this->assertEquals(0, $qualityPortfolio->range4);
        $this->assertEquals(1, $qualityPortfolio->range5);

        $this->removeTestData($investor, $loan);
    }

    protected function createLoanInstallmentsThird(Loan $loan, $fromDate = null)
    {
        $originalCurrencyId = Currency::ID_BGN; // we keep original currency, it will be changed to EUR in import service
        $import = [
            0 => [
                'seq_num' => 1,
                'due_date' => Carbon::today()->addMonth(),
                'currency_id' => $originalCurrencyId,
                'paid' => 1,
                // 'remaining_principal' => 1222.46,
                'principal' => 60.56,
                'status' => 'current',
                'lender_installment_id' => 1,
                'lender_id' => $loan->lender_id,
            ],
            1 => [
                'seq_num' => 2,
                'due_date' => Carbon::today()->addMonths(2),
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 1161.90,
                'principal' => 61.37,
                'status' => 'current',
                'lender_installment_id' => 2,
                'lender_id' => $loan->lender_id,
            ],
            2 => [
                'seq_num' => 3,
                'due_date' => Carbon::today()->addMonths(3),
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 1161.90,
                'principal' => 61.37,
                'status' => 'current',
                'lender_installment_id' => 2,
                'lender_id' => $loan->lender_id,
            ]
        ];

        return $this->getInstallmentsAfterInsert($loan, $import, $fromDate);
    }


    public function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });

        parent::tearDown();
    }
}
