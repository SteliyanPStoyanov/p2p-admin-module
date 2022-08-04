<?php

namespace Tests\Unit\Distribute;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\RepaidInstallment;
use Modules\Common\Entities\Transaction;
use Modules\Common\Repositories\InvestorInstallmentRepository;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

/**
 * Distribue repaid installment
 * TODO: accrued/late interest
 */
class DistributeInstallmentTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    private $importService;
    private $investService;
    private $distributeService;
    private $investsorInstallmentRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->importService = new ImportService;
        $this->investService = new InvestService;
        $this->distributeService = new DistributeService;
        $this->investsorInstallmentRepository = App::make(InvestorInstallmentRepository::class);
    }

    public function testRepaidloanOnInstallmentRepayment()
    {
        // create loan and installments
        $loan = $this->preapreLoan();
        $this->assertNotEmpty($loan->loan_id);
        $loan->lender_id = rand(10000, 90000);
        $loan->final_payment_date = '2020-07-19';
        $loan->save();

        $installmentsCount = $this->prepareInstallments($loan, 1);
        $this->assertEquals($installmentsCount, 1);

        $currentInstallment = $loan->getFirstUnpaidInstallment();
        $this->assertEquals($currentInstallment->paid, 0);
        $this->assertEmpty($currentInstallment->paid_at);

        $currentInstallment->lender_installment_id = $loan->lender_id . '1';
        $currentInstallment->save();

        // prepare investor
        $investor = $this->prepareInvestor('investor1_' . time() . '@RepaidloanOnInstallmentRepayment.test');
        $this->assertNotEmpty($investor->investor_id);

        $deposit = 1000.00;
        $wallet = $this->prepareWallet($investor, $deposit);
        $this->assertNotEmpty($wallet->wallet_id);
        $this->assertEquals($wallet->uninvested, $deposit);

        $portfolios = $this->preparePortfolios($investor);
        $this->assertNotEmpty($portfolios['quality']->portfolio_id);
        $this->assertNotEmpty($portfolios['maturity']->portfolio_id);
        $maturity = $portfolios['maturity'];
        $quality = $portfolios['quality'];

        // make investment
        $amount = 100;
        $investDate = Carbon::parse('2020-07-01');
        $invested = $this->investService->invest(
            $investor->investor_id,
            $loan->loan_id,
            $amount,
            $investDate
        );
        $this->assertEquals($invested, true);
        $wallet->refresh(); // update


        $repaidInstallment = new RepaidInstallment();
        $repaidInstallment->handled = 0;
        $repaidInstallment->lender_id = $loan->lender_id;
        $repaidInstallment->lender_installment_id = $currentInstallment->lender_installment_id;


        $repaymentDate = Carbon::parse('2020-07-19');
        $distributed = $this->distributeService->distributeInstallment(
            $repaidInstallment,
            $repaymentDate
        );
        $this->assertEquals($distributed, true);

        $loan->refresh();
        $currentInstallment->refresh();

        $this->assertEquals($loan->unlisted, 1);
        $this->assertEquals($loan->status, Loan::STATUS_REPAID);
        $this->assertEquals($currentInstallment->paid, 1);

        $maturity->refresh();
        $this->assertEquals($maturity->range1, 0);
        $this->assertEquals($maturity->range2, 0);
        $this->assertEquals($maturity->range3, 0);
        $this->assertEquals($maturity->range4, 0);
        $this->assertEquals($maturity->range5, 0);

        // remove test data
        $this->removeTestData($investor, $loan);
    }

    public function testRepaidInstallmentWithoutInvestments()
    {
        // create loan and installments
        $loan = $this->preapreLoan();
        $this->assertNotEmpty($loan->loan_id);

        $installmentsCount = $this->prepareInstallments($loan);
        $this->assertEquals($installmentsCount, 18);

        $currentInstallment = $loan->getFirstUnpaidInstallment();
        $this->assertEquals($currentInstallment->paid, 0);
        $this->assertEmpty($currentInstallment->paid_at);


        // emulate payment of installment
        $newRepaidInstallment = $this->emulateRepaidInstallment($loan);
        $this->assertNotEmpty($newRepaidInstallment->repaid_installment_id);
        $this->assertEquals($newRepaidInstallment->handled, 0);
        $this->assertEquals($loan->lender_id, $newRepaidInstallment->lender_id);
        $this->assertEquals($currentInstallment->lender_installment_id, $newRepaidInstallment->lender_installment_id);


        // distribute repaid installments
        $distributed = $this->distributeService->distributeInstallment($newRepaidInstallment);
        $this->assertEquals($distributed, true);


        // checks
        $loan->refresh();
        $nextInstallment = $loan->getFirstUnpaidInstallment();
        $this->assertEquals($loan->prepaid_schedule_payments, 1);
        $this->assertEquals($loan->remaining_principal, $nextInstallment->remaining_principal);
        $this->assertEquals($loan->payment_status, $nextInstallment->status);
        $this->assertLessThan($loan->remaining_principal, $loan->amount_available);

        $currentInstallment->refresh();
        $this->assertEquals($currentInstallment->paid, 1);
        $this->assertNotEmpty($currentInstallment->paid_at);

        $newRepaidInstallment->refresh();
        $this->assertEquals($newRepaidInstallment->handled, 1);


        // remove test data
        $this->removeTestData(null, $loan);
    }

    public function testRepaidInstallmentWithInvestments()
    {
        // clear, to use again same investor
        $investor = $this->getInvestor();
        $this->removeTestData($investor);

        // create loan and installments
        $loan = $this->preapreLoan();
        $this->assertNotEmpty($loan->loan_id);

        $installmentsCount = $this->prepareInstallments($loan);
        $this->assertEquals($installmentsCount, 18);

        $currentInstallment = $loan->getFirstUnpaidInstallment();
        $this->assertEquals($currentInstallment->paid, 0);
        $this->assertEmpty($currentInstallment->paid_at);


        // create investor with main attributes
        $investor = $this->prepareInvestor();
        $this->assertNotEmpty($investor->investor_id);

        $deposit = 1000.00;
        $wallet = $this->prepareWallet($investor, $deposit);
        $this->assertNotEmpty($wallet->wallet_id);
        $this->assertEquals($wallet->total_amount, $deposit);
        $this->assertEquals($wallet->uninvested, $deposit);

        $portfolios = $this->preparePortfolios($investor);
        $this->assertNotEmpty($portfolios);
        $this->assertNotEmpty($portfolios['quality']->portfolio_id);
        $this->assertEquals($portfolios['quality']->range1, 0);
        $this->assertEquals($portfolios['quality']->range2, 0);
        $this->assertEquals($portfolios['quality']->range3, 0);
        $this->assertEquals($portfolios['quality']->range4, 0);
        $this->assertEquals($portfolios['quality']->range5, 0);
        $this->assertNotEmpty($portfolios['maturity']->portfolio_id);
        $this->assertEquals($portfolios['maturity']->range1, 0);
        $this->assertEquals($portfolios['maturity']->range2, 0);
        $this->assertEquals($portfolios['maturity']->range3, 0);
        $this->assertEquals($portfolios['maturity']->range4, 0);
        $this->assertEquals($portfolios['maturity']->range5, 0);


        // add fake difference, that we need for checks
        $loan->payment_status = Loan::PAY_STATUS_31_60;
        $loan->save();
        $currentInstallment->status = Loan::PAY_STATUS_31_60;
        $currentInstallment->save();


        // do investment
        $amount = 100;
        $now = Carbon::parse('2020-07-30');
        $invested = $this->investService->invest(
            $investor->investor_id,
            $loan->loan_id,
            $amount,
            $now
        );
        $this->assertEquals($invested, true);
        $investment = $this->getInvestment(
            $investor->investor_id,
            $loan->loan_id,
            $amount
        );
        $this->assertNotEmpty($investment->investment_id);

        $wallet->refresh();
        $this->assertEquals($wallet->total_amount, $deposit);
        $this->assertEquals($wallet->deposit, $deposit);
        $this->assertEquals($wallet->invested, $amount);
        $this->assertEquals($wallet->uninvested, ($deposit - $amount));

        $quality = $portfolios['quality'];
        $quality->refresh();
        $this->assertEquals($quality->range1, 0);
        $this->assertEquals($quality->range2, 0);
        $this->assertEquals($quality->range3, 0);
        $this->assertEquals($quality->range4, 1);
        $this->assertEquals($quality->range5, 0);

        $ranges = $this->getInvestorQualityRage(
            $investor->investor_id,
            $loan->loan_id
        );
        $this->assertEquals(($ranges->count()), 1);
        $this->assertEquals($ranges->first()->range, 4);

        $installment = $loan->getFirstUnpaidInstallment();

        // WE ARE CLOSING 1st INSTALLMENT - but its not part of investment
        // emulate payment of installment
        $newRepaidInstallment = $this->emulateRepaidInstallment($loan);
        $this->assertNotEmpty($newRepaidInstallment->repaid_installment_id);

        // distribute repaid installments
        $distributed = $this->distributeService->distributeInstallment($newRepaidInstallment);
        $this->assertEquals($distributed, true);

        $installment->refresh();
        $this->assertEquals(Installment::STATUS_PAID_ID, $installment->paid);

        $investorInstallments = $this->investsorInstallmentRepository->getInvestorInstallmentsByInstallmentId(
            $installment->installment_id
        );

        foreach ($investorInstallments as $investorInstallment) {
            $this->assertEquals(Installment::STATUS_PAID_ID, $investorInstallment->paid);
        }

        // since we get payment for installment which are not belongs to investor
        // his portfolio range is still the same
        $quality->refresh();
        $this->assertEquals($quality->range1, 0);
        $this->assertEquals($quality->range2, 0);
        $this->assertEquals($quality->range3, 0);
        $this->assertEquals($quality->range4, 0);
        $this->assertEquals($quality->range5, 1);

        $rangeHistory = $this->getInvestorQualityRageHistory(
            $investor->investor_id,
            $loan->loan_id,
            4
        );
        $this->assertEquals($rangeHistory->range, $ranges->first()->range);
        $this->assertEquals($rangeHistory->investor_quality_range_id, $ranges->first()->investor_quality_range_id);

        $ranges = $this->getInvestorQualityRage(
            $investor->investor_id,
            $loan->loan_id
        );
        $this->assertEquals(($ranges->count()), 1);
        $this->assertEquals($ranges->first()->range, 5);

        // get new unpaid installment
        $currentInstallment = $loan->getFirstUnpaidInstallment();
        $this->assertEquals($currentInstallment->paid, 0);
        $this->assertEmpty($currentInstallment->paid_at);
        $currentInstallment->status = Loan::PAY_STATUS_16_30;
        $currentInstallment->save();

        // fake statuses, just to check the ranges of portfolio
        // so the portfolio should go to: PAY_STATUS_31_60
        // but loan payment status should go to: PAY_STATUS_1_15
        Installment::where('loan_id', '=', $loan->loan_id)
            ->where('installment_id', '>', $currentInstallment->installment_id)
            ->update(['status' => Loan::PAY_STATUS_1_15]);
        $quality->refresh();

        // WE ARE CLOSING 2nd INSTALLMENT - which is already part
        // emulate payment of installment
        $newRepaidInstallment = $this->emulateRepaidInstallment($loan);
        $this->assertNotEmpty($newRepaidInstallment->repaid_installment_id);

        $distributed = $this->distributeService->distributeInstallment($newRepaidInstallment);
        $this->assertEquals($distributed, true);


        // checks
        $loan->refresh();
        $nextInstallment = $loan->getFirstUnpaidInstallment();
        $this->assertEquals($loan->prepaid_schedule_payments, 2);
        $this->assertEquals($loan->remaining_principal, $nextInstallment->remaining_principal);
        $this->assertEquals($loan->payment_status, $nextInstallment->status);
        $this->assertEquals($loan->payment_status, Loan::PAY_STATUS_1_15);
        $this->assertLessThan($loan->remaining_principal, $loan->amount_available);

        $currentInstallment->refresh();
        $this->assertEquals($currentInstallment->paid, 1);
        $this->assertNotEmpty($currentInstallment->paid_at);

        $newRepaidInstallment->refresh();
        $this->assertEquals($newRepaidInstallment->handled, 1);

        $investorInstallment = $this->getInverstorInstallment(
            $currentInstallment->loan_id,
            $currentInstallment->installment_id
        );
        $this->assertEquals($investorInstallment->paid, 1);
        $this->assertNotEmpty($investorInstallment->paid_at);

        $previousTotal = $wallet->total_amount;
        $previousIncome = $wallet->income;
        $previousInterest = $wallet->interest;
        $wallet->refresh();
        $this->assertEquals($wallet->total_amount, ($previousTotal + $investorInstallment->getInstallmentIncome()));
        $this->assertEquals($wallet->income, ($previousIncome + $investorInstallment->getInstallmentIncome()));
        $this->assertEquals($wallet->interest, ($previousInterest + $investorInstallment->getInstallmentInterest()));

        $quality->refresh();
        $this->assertEquals($quality->range1, 0);
        $this->assertEquals($quality->range2, 1);
        $this->assertEquals($quality->range3, 0);
        $this->assertEquals($quality->range4, 0);
        $this->assertEquals($quality->range5, 0);

        $rangeHistory = $this->getInvestorQualityRageHistory(
            $investor->investor_id,
            $loan->loan_id,
            5
        );
        $this->assertEquals($rangeHistory->range, $ranges->first()->range);
        $this->assertEquals($rangeHistory->investor_quality_range_id, $ranges->first()->investor_quality_range_id);

        $ranges = $this->getInvestorQualityRage(
            $investor->investor_id,
            $loan->loan_id
        );
        $this->assertEquals(($ranges->count()), 1);
        $this->assertEquals($ranges->first()->range, 2);

        $transaction = $this->getTransaction(
            $loan->loan_id,
            $investor->investor_id
        );
        $this->assertEquals($transaction->originator_id, $loan->originator_id);
        $this->assertEquals($transaction->direction, Transaction::DIRECTION_OUT);
        $this->assertEquals($transaction->type, Transaction::TYPE_INSTALLMENT_REPAYMENT);
        $this->assertEquals($transaction->bank_account_id, $investor->getMainBankAccountId());
        $this->assertEquals(
            $transaction->amount,
            ($investorInstallment->principal + $investorInstallment->getInstallmentInterest())
        );
        $this->assertEquals($transaction->principal, $investorInstallment->principal);
        $this->assertEquals($transaction->interest, $investorInstallment->getInstallmentInterest());


        // remove test data
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
