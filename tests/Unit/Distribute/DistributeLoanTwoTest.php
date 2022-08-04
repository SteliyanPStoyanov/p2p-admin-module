<?php

namespace Tests\Unit\Distribute ;

use App;
use Artisan;
use Carbon\Carbon;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\Transaction;
use Modules\Common\Services\DistributeService;
use Tests\Unit\Invest\InvestingTest;

class DistributeLoanTwoTest extends InvestingTest
{
    protected $distributeService;

    public function setUp(): void
    {
        parent::setUp();
        $this->distributeService = App::make(DistributeService::class);
    }

    /**
     * @depends testLoanOne
     *
     * @param array $data
     */
    public function testDistributeLoanOne(array $data)
    {
        list($investor, $loan) = $data;
        $loan->refresh();

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-01-02'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-01-02'
        ]);

        $investorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();
        $this->assertCount(6, $investorInstallments);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.31, $investorInstallment->interest);
        $this->assertEquals(0.31, $investorInstallment->accrued_interest);
        $this->assertEquals($investorInstallment->interest, $investorInstallment->accrued_interest);
        $this->assertEquals(0.04, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_LATE, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.81, $investorInstallment->interest);
        $this->assertEquals(0.21, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.65, $investorInstallment->interest);
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.44, $investorInstallment->interest);
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.33, $investorInstallment->interest);
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.16, $investorInstallment->interest);
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $repaidLoan = $this->emulateRepaidLoan($loan);

        $wallet = $investor->wallet();
        $wallet->refresh();
        $totalAmount = $wallet->total_amount;
        $uninvested = $wallet->uninvested;
        $invested = $wallet->invested;
        $income = $wallet->income;
        $interest = $wallet->interest;
        $lateInterest = $wallet->late_interest;

        $this->distributeService->distributeLoan($repaidLoan, Carbon::parse('2021-01-02'));

        $sumPrincipal = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('principal');
        $sumAccruedInterest = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('accrued_interest');
        $sumLateInterest = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('late_interest');
        $sumTotal = $sumPrincipal + $sumAccruedInterest + $sumLateInterest;

        $transaction = Transaction::where(
            [
                'loan_id' => $loan->loan_id,
                'type' => Transaction::TYPE_REPAYMENT,
            ]
        )->first();

        $this->assertNotEmpty($transaction);
        $this->assertEquals($sumTotal, $transaction->amount);

        $wallet->refresh();

        $this->assertEquals($totalAmount + $sumLateInterest + $sumAccruedInterest, $wallet->total_amount);
        $this->assertEquals($uninvested + $sumTotal, $wallet->uninvested);
        $this->assertEquals($invested - $sumPrincipal, $wallet->invested);
        $this->assertEquals($sumAccruedInterest + $sumLateInterest + $income, $wallet->income);
        $this->assertEquals($sumAccruedInterest + $interest, $wallet->interest);
        $this->assertEquals($sumLateInterest + $lateInterest, $wallet->late_interest);

        $paidInvestorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
                'paid' => 1,
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();
        $unpaidInvestorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
                'paid' => 0
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();

        $this->assertCount(6, $paidInvestorInstallments);
        $this->assertCount(0, $unpaidInvestorInstallments);

        $this->removeTestData($investor, $loan);
    }

    /**
     * @depends testLoanTwo
     *
     * @param array $data
     */
    public function testDistributeLoanTwo(array $data)
    {
        list($investor, $loan) = $data;
        $loan->refresh();

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2020-12-23'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2020-12-23'
        ]);

        $investorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();
        $this->assertCount(6, $investorInstallments);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.04, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $repaidLoan = $this->emulateRepaidLoan($loan);

        $wallet = $investor->wallet();
        $wallet->refresh();
        $totalAmount = $wallet->total_amount;
        $uninvested = $wallet->uninvested;
        $invested = $wallet->invested;
        $income = $wallet->income;
        $interest = $wallet->interest;
        $lateInterest = $wallet->late_interest;

        $this->distributeService->distributeLoan($repaidLoan, Carbon::parse('2020-12-23'));

        $sumPrincipal = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('principal');
        $sumAccruedInterest = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('accrued_interest');
        $sumLateInterest = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('late_interest');
        $sumTotal = $sumPrincipal + $sumAccruedInterest + $sumLateInterest;

        $transaction = Transaction::where(
            [
                'loan_id' => $loan->loan_id,
                'type' => Transaction::TYPE_REPAYMENT,
            ]
        )->first();

        $this->assertNotEmpty($transaction);
        $this->assertEquals($sumTotal, $transaction->amount);

        $wallet->refresh();

        $this->assertEquals($totalAmount + $sumLateInterest + $sumAccruedInterest, $wallet->total_amount);
        $this->assertEquals($uninvested + $sumTotal, $wallet->uninvested);
        $this->assertEquals($invested - $sumPrincipal, $wallet->invested);
        $this->assertEquals($sumAccruedInterest + $sumLateInterest + $income, $wallet->income);
        $this->assertEquals($sumAccruedInterest + $interest, $wallet->interest);
        $this->assertEquals($sumLateInterest + $lateInterest, $wallet->late_interest);

        $paidInvestorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
                'paid' => 1,
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();
        $unpaidInvestorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
                'paid' => 0
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();

        $this->assertCount(6, $paidInvestorInstallments);
        $this->assertCount(0, $unpaidInvestorInstallments);

        $this->removeTestData($investor, $loan);
    }

    /**
     * @depends testLoanThree
     *
     * @param array $data
     */
    public function testDistributeLoanThree(array $data)
    {
        list($investor, $loan) = $data;

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2020-11-27'
        ]);

        $wallet = $investor->wallet();
        $wallet->refresh();
        $totalAmount = $wallet->total_amount;
        $uninvested = $wallet->uninvested;
        $invested = $wallet->invested;
        $income = $wallet->income;
        $interest = $wallet->interest;
        $lateInterest = $wallet->late_interest;

        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2020-11-27'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2020-12-15'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2020-12-15'
        ]);

        $investorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();
        $this->assertCount(6, $investorInstallments);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.23, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);
        $this->assertEquals(1, $investorInstallment->paid);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(3.60, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.00, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $repaidLoan = $this->emulateRepaidLoan($loan);

        $sumPrincipal = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('principal');
        $sumAccruedInterest = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('accrued_interest');
        $sumLateInterest = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('late_interest');
        $sumTotal = $sumPrincipal + $sumAccruedInterest + $sumLateInterest;

        $this->distributeService->distributeLoan($repaidLoan, Carbon::parse('2020-12-15'));

        $transactionRepaymentLoan = Transaction::where(
            [
                'loan_id' => $loan->loan_id,
                'type' => Transaction::TYPE_REPAYMENT,
            ]
        )->first();

        $transactionRepaymentFirstInstallment = Transaction::where(
            [
                'loan_id' => $loan->loan_id,
                'type' => Transaction::TYPE_INSTALLMENT_REPAYMENT,
            ]
        )->first();

        $this->assertNotEmpty($transactionRepaymentLoan);
        $this->assertNotEmpty($transactionRepaymentFirstInstallment);
        $this->assertEquals($sumTotal, $transactionRepaymentLoan->amount + $transactionRepaymentFirstInstallment->amount);

        $wallet->refresh();

        $this->assertEquals($totalAmount + $sumLateInterest + $sumAccruedInterest, $wallet->total_amount);
        $this->assertEquals($uninvested + $sumTotal, $wallet->uninvested);
        $this->assertEquals($invested - $sumPrincipal, $wallet->invested);
        $this->assertEquals($sumAccruedInterest + $sumLateInterest + $income, $wallet->income);
        $this->assertEquals($sumAccruedInterest + $interest, $wallet->interest);
        $this->assertEquals($sumLateInterest + $lateInterest, $wallet->late_interest);

        $paidInvestorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
                'paid' => 1,
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();
        $unpaidInvestorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
                'paid' => 0
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();

        $this->assertCount(6, $paidInvestorInstallments);
        $this->assertCount(0, $unpaidInvestorInstallments);

        $this->removeTestData($investor, $loan);
    }

    /**
     * @depends testLoanFour
     *
     * @param array $data
     */
    public function testDistributeLoanFour(array $data)
    {
        list($investor, $loan) = $data;

        $wallet = $investor->wallet();
        $wallet->refresh();
        $totalAmount = $wallet->total_amount;
        $uninvested = $wallet->uninvested;
        $invested = $wallet->invested;
        $income = $wallet->income;
        $interest = $wallet->interest;
        $lateInterest = $wallet->late_interest;

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-01-21'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-01-21'
        ]);
        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2021-01-21'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-02-21'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-02-21'
        ]);
        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2021-02-21'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-03-21'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-03-21'
        ]);
        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2021-03-21'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-04-21'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-04-21'
        ]);
        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2021-04-21'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-05-21'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-05-21'
        ]);
        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2021-05-21'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-06-21'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-06-21'
        ]);
        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2021-06-21'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-07-21'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-07-21'
        ]);
        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2021-07-21'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-08-21'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-08-21'
        ]);
        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2021-08-21'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-09-21'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-09-21'
        ]);

        $this->distributeService->distributeInstallment($this->emulateRepaidInstallment($loan), Carbon::parse('2021-09-21'));

        Artisan::call('script:daily-interest-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-11-16'
        ]);
        Artisan::call('script:daily-payment-status-refresh', [
            'loanId' => $loan->loan_id,
            'date' => '2021-11-16'
        ]);

        $investorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();
        $this->assertCount(24, $investorInstallments);
        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(2.39, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);
        $this->assertEquals(1, $investorInstallment->paid);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(4.60, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(4.12, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(4.53, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(4.34, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(4.43, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(4.24, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(4.31, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(4.24, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_PAID, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(4.02, $investorInstallment->accrued_interest);
        $this->assertEquals(0.08, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_LATE, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(3.41, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $investorInstallment = $investorInstallments->shift();
        $this->assertEquals(0.0, $investorInstallment->accrued_interest);
        $this->assertEquals(0.00, $investorInstallment->late_interest);
        $this->assertEquals(Installment::STATUS_SCHEDULED, $investorInstallment->installment()->payment_status);

        $repaidLoan = $this->emulateRepaidLoan($loan);

        $sumPrincipal = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('principal');
        $sumAccruedInterest = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('accrued_interest');
        $sumLateInterest = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id,
            ]
        )->sum('late_interest');
        $sumTotal = $sumPrincipal + $sumAccruedInterest + $sumLateInterest;

        $this->distributeService->distributeLoan($repaidLoan, Carbon::parse('2021-11-16'));

        $transactionRepaymentLoan = Transaction::where(
            [
                'loan_id' => $loan->loan_id,
                'type' => Transaction::TYPE_REPAYMENT,
            ]
        )->first();

        $transactionRepaymentInstallmentsSum = Transaction::where(
            [
                'loan_id' => $loan->loan_id,
                'type' => Transaction::TYPE_INSTALLMENT_REPAYMENT,
            ]
        )->sum('amount');

        $this->assertNotEmpty($transactionRepaymentLoan);
        $this->assertNotEmpty($transactionRepaymentInstallmentsSum);
        $this->assertEquals($sumTotal, $transactionRepaymentLoan->amount + $transactionRepaymentInstallmentsSum);

        $wallet->refresh();

        $this->assertEquals($totalAmount + $sumLateInterest + $sumAccruedInterest, $wallet->total_amount);
        $this->assertEquals($uninvested + $sumTotal, $wallet->uninvested);
        $this->assertEquals($invested - $sumPrincipal, $wallet->invested);
        $this->assertEquals($sumAccruedInterest + $sumLateInterest + $income, $wallet->income);
        $this->assertEquals($sumAccruedInterest + $interest, $wallet->interest);
        $this->assertEquals($sumLateInterest + $lateInterest, $wallet->late_interest);

        $paidInvestorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
                'paid' => 1,
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();
        $unpaidInvestorInstallments = InvestorInstallment::where(
            [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan->loan_id,
                'paid' => 0
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();

        $this->assertCount(24, $paidInvestorInstallments);
        $this->assertCount(0, $unpaidInvestorInstallments);

        $this->removeTestData($investor, $loan);
    }
}
