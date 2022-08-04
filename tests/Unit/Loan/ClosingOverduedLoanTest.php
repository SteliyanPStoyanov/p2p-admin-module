<?php

namespace Tests\Unit\Loan;

use App;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\RepaidLoan;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class ClosingOverduedLoanTest extends TestCase
{
    use WithoutMiddleware;
    use TestDataTrait;

    protected $investService;
    protected $importService;
    protected $distributeService;

    public function setUp(): void
    {
        parent::setUp();

        $this->investService = App::make(InvestService::class);
        $this->importService = App::make(ImportService::class);
        $this->distributeService = App::make(DistributeService::class);
    }

    public function testClosingOverduedLoan()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $loanAmount = 1222.46;
        $remainingPricipal = 1222.46;
        $interestRate = 14.50;
        $issueDate = '2020-11-02';
        $listingDate = '2020-11-02';
        $finalPaymentDate = '2022-05-03';

        $loan = $this->preapreLoan(
            $loanAmount,
            $loanAmount,
            $remainingPricipal,
            $interestRate,
            10, // originator percent
            5, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            $finalPaymentDate
        );
        // Must be overdued
        $loan->overdue_days = 15;
        $loan->save();

        $this->prepareInstallmentsWithStartDate(
            $loan,
            Carbon::today()->subMonth()->toDateString(),
            5
        );

        $investorDeposit = 1000;
        $investor = $this->prepareInvestor('investor_interest_refresh_' . time() . '@test2.com');
        $wallet = $this->getInvestorWallet($investor->investor_id, $investorDeposit, $currencyId);
        $this->preparePortfolios($investor);

        $loan->refresh();

        // do invest
        $investorBuyAmount = 200;
        $now = Carbon::parse($issueDate);
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $loan->installments(),
            $now
        );

        $this->assertEquals(true, $invested);
        $this->assertEquals(15, $loan->overdue_days);

        // emulate repaid loan
        $repaidLoan = $this->emulateRepaidLoan($loan, RepaidLoan::TYPE_LATE);
        $this->assertEquals($repaidLoan->lender_id, $loan->lender_id);
        $this->assertEquals($repaidLoan->repayment_type, RepaidLoan::TYPE_LATE);
        $this->assertEquals($repaidLoan->handled, 0);

        $isDistributed = $this->distributeService->distributeLoan($repaidLoan, Carbon::now());
        $this->assertEquals(true, $isDistributed);

        $loan->refresh();
        $installments = $loan->installments();

        $this->assertEquals(Loan::STATUS_REPAID, $loan->status);
        $this->assertEquals(0, $loan->overdue_days);

        // Test installments
        foreach ($installments as $installment) {
            $this->assertEquals(1, $installment->paid);

            // Test every investor installment
            foreach ($installment->investorInstallments as $investorInstallment) {
                $this->assertEquals(1, $investorInstallment->paid);
            }
        }

        $this->removeTestData($investor, $loan);
    }

    public function tearDown(): void
    {
        $this->beforeApplicationDestroyed(
            function () {
                DB::disconnect();
            }
        );

        parent::tearDown();
    }
}
