<?php

namespace Tests\Unit\Distribute;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\RepaidLoan;
use Modules\Common\Entities\Transaction;
use Modules\Common\Repositories\InstallmentRepository;
use Modules\Common\Repositories\InvestorInstallmentRepository;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class DistributeLoanTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    private $importService;
    private $investService;
    private $distributeService;
    private $installmentRepository;
    private $assertEquals;
    private $investorInstallmentRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->importService = new ImportService;
        $this->investService = new InvestService;
        $this->distributeService = new DistributeService;
        $this->installmentRepository = App::make(InstallmentRepository::class);
        $this->investorInstallmentRepository = App::make(InvestorInstallmentRepository::class);
    }

    public function testLoanEarlyRepayment()
    {
        // clear, to use again same investor
        $investor = $this->getInvestor();
        $this->removeTestData($investor);


        // create loan and installments
        $loan = $this->preapreLoan();
        $finalPaymentDate = Carbon::parse('2021-12-19');

        $this->assertNotEmpty($loan->loan_id);

        $installmentsCount = $this->prepareInstallments($loan, 0, [0]);
        $this->assertEquals($installmentsCount, 18);


        // create investor with main attributes
        $investor = $this->prepareInvestor('investor1_' . time() . '@distributeInstallment.test');
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
        $now = Carbon::parse('2020-07-30');
        $invested = $this->investService->invest(
            $investor->investor_id,
            $loan->loan_id,
            $amount,
            $now
        );
        $this->assertEquals($invested, true);
        $wallet->refresh(); // update


        // emulate accrued interest on investor installment
        $fakeInterest = 4.04;
        $accrInterest = 2.02;
        $investment = Investment::where(
            [
                'loan_id' => $loan->getId(),
                'investor_id' => $investor->getId(),
            ]
        )->first();
        $unpaidInstallments = $investor->getUnpaidInstallments(
            $loan->loan_id,
            $investment->getId()
        );
        $firstUnpaidInstallment = reset($unpaidInstallments);
        $firstUnpaidInstallment->interest = $fakeInterest;
        $firstUnpaidInstallment->accrued_interest = $accrInterest;
        $firstUnpaidInstallment->late_interest = 0.00;
        $firstUnpaidInstallment->save();
        $this->assertLessThan($firstUnpaidInstallment->interest, $firstUnpaidInstallment->getInstallmentInterest());
        $this->assertEquals($firstUnpaidInstallment->getInstallmentInterest(), $accrInterest);
        $unpaidInstallments = $investor->getUnpaidInstallments(
            $loan->loan_id,
            $investment->getId()
        );


        // emulate repaid loan
        $newRepaidLoan = $this->emulateRepaidLoan($loan, RepaidLoan::TYPE_EARLY);
        $this->validateNewRepaidLoan($loan, $newRepaidLoan, RepaidLoan::TYPE_EARLY);


        // do repayment
        $repaymentDate = Carbon::parse('2020-08-10');

        // we should manually update maturity, since we emulate repayment on specific date
        $maturity->refresh();
        $maturity->range1 = 0;
        $maturity->range2 = 0;
        $maturity->range3 = 0;
        $maturity->range4 = 0;
        $maturity->range5 = 0;
        $maturity->save();

        // set maturity bases on repayment date
        $range = Portfolio::getMaturityRangeColumnByDate(
            $finalPaymentDate,
            $repaymentDate
        );
        $maturity->{$range} = 1;
        $maturity->save();

        $distributed = $this->distributeService->distributeLoan($newRepaidLoan, $repaymentDate);
        $this->assertEquals($distributed, true);


        // checks

        // loan repaid
        $loan->refresh();
        $this->assertEquals($loan->status, Loan::STATUS_REPAID_EARLY);
        $this->assertEquals($loan->unlisted, 1);

        // investor maturity
        $maturity->refresh();
        $this->assertEquals($maturity->range1, 0);
        $this->assertEquals($maturity->range2, 0);
        $this->assertEquals($maturity->range3, 0);
        $this->assertEquals($maturity->range4, 0);
        $this->assertEquals($maturity->range5, 0);

        // investor quality
        $quality->refresh();
        $this->assertEquals($quality->range1, 0);
        $this->assertEquals($quality->range2, 0);
        $this->assertEquals($quality->range3, 0);
        $this->assertEquals($quality->range4, 0);
        $this->assertEquals($quality->range5, 0);
        $ranges = $this->getInvestorQualityRage(
            $investor->investor_id,
            $loan->loan_id
        );
        $this->assertEquals(($ranges->count()), 0);

        // investor wallet
        $total = $wallet->total_amount;
        $income = $wallet->income;
        $interest = $wallet->interest;
        $uninvested = $wallet->uninvested;
        $lateInterest = $wallet->late_interest;
        $sumPrincipal = $this->sumPrincipal($unpaidInstallments);
        $wallet->refresh();

        $this->assertEquals($wallet->interest, $firstUnpaidInstallment->getInstallmentInterest());
        $this->assertEquals($wallet->late_interest, $lateInterest);
        $this->assertEquals($wallet->income, ($income + $firstUnpaidInstallment->getInstallmentIncome()));
        $this->assertEquals($wallet->income, ($income + $accrInterest));
        $this->assertEquals($wallet->total_amount, ($total + $firstUnpaidInstallment->getInstallmentIncome()));
        $this->assertEquals($wallet->total_amount, ($total + $accrInterest));
        $this->assertEquals(
            $wallet->uninvested,
            ($uninvested + $firstUnpaidInstallment->getInstallmentIncome() + $sumPrincipal)
        );

        // transaction
        $transaction = $this->getTransaction(
            $loan->loan_id,
            $investor->investor_id
        );

        $invInstallments = $investor->installments($loan->loan_id);
        $sumOfPrincipals = 0;
        foreach ($invInstallments as $installment) {
            $sumOfPrincipals += $installment->principal;
        }

        $this->assertEquals($transaction->investor_id, $investor->investor_id);
        $this->assertEquals($transaction->wallet_id, $wallet->wallet_id);
        $this->assertEquals($transaction->direction, Transaction::DIRECTION_OUT);
        $this->assertEquals($transaction->type, Transaction::TYPE_EARLY_REPAYMENT);
        $this->assertEquals($transaction->principal, $sumOfPrincipals);
        $this->assertEquals($transaction->accrued_interest, $firstUnpaidInstallment->getInstallmentIncome());
        $this->assertEquals($transaction->interest, 0);
        $this->assertEquals($transaction->late_interest, 0);
        $this->assertEquals($transaction->amount, $sumOfPrincipals + $firstUnpaidInstallment->getInstallmentIncome());

        $installments = $this->installmentRepository->getInstallmentsByLoanId($loan->loan_id);
        $loanInvestorInstallments = $this->investorInstallmentRepository->getInvestorInstallmentsByLoanId
        (
            $loan->loan_id
        );

        foreach ($installments as $installment) {
            $this->assertEquals(Installment::STATUS_PAID_ID, $installment->paid);
        }

        foreach ($loanInvestorInstallments as $investorInstallment) {
            $this->assertEquals(InvestorInstallment::INVESTOR_INSTALLMENT_PAID_ID, $investorInstallment->paid);
        }

        // remove test data
        $this->removeTestData($investor, $loan);
    }

    public function testLoanEarlyRepaymentTwoInvestments()
    {
        // create loan and installments
        $loan = $this->preapreLoan(1000);
        $finalPaymentDate = Carbon::parse('2021-12-19');

        $this->assertNotEmpty($loan->loan_id);

        $installmentsCount = $this->prepareInstallments($loan, 0, [0]);
        $this->assertEquals($installmentsCount, 18);


        // create investor with main attributes
        $investor = $this->prepareInvestor('investor1_' . time() . '@distributeInstallment.test');
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


        // make first investment
        $amount = 100;
        $now = Carbon::parse('2020-07-30');
        $invested = $this->investService->invest(
            $investor->investor_id,
            $loan->loan_id,
            $amount,
            $now
        );
        $this->assertEquals($invested, true);

        $amount = 150;
        $invested = $this->investService->invest(
            $investor->investor_id,
            $loan->loan_id,
            $amount,
            $now
        );
        $this->assertEquals($invested, true);

        $wallet->refresh(); // update


        // emulate accrued interest on investor installment
        $fakeInterestFirstInvestment = 4.04;
        $accrInterestFirstInvestment = 2.02;
        $fakeInterestSecondInvestment = 6.23;
        $accrInterestSecondInvestment = 3.32;

        $firstInvestment = Investment::where(
            [
                'loan_id' => $loan->getId(),
                'investor_id' => $investor->getId(),
            ]
        )->orderBy('investment_id', 'ASC')->first();
        $secondInvestment = Investment::where(
            [
                'loan_id' => $loan->getId(),
                'investor_id' => $investor->getId(),
            ]
        )->orderBy('investment_id', 'DESC')->first();

        $unpaidInstallmentsFirstInvestment = $investor->getUnpaidInstallments(
            $loan->loan_id,
            $firstInvestment->getId()
        );
        $unpaidInstallmentsSecondInvestment = $investor->getUnpaidInstallments(
            $loan->loan_id,
            $secondInvestment->getId()
        );
        $firstUnpaidInstallment = reset($unpaidInstallmentsFirstInvestment);
        $firstUnpaidInstallment->interest = $fakeInterestFirstInvestment;
        $firstUnpaidInstallment->accrued_interest = $accrInterestFirstInvestment;
        $firstUnpaidInstallment->late_interest = 0.00;
        $firstUnpaidInstallment->save();
        $this->assertLessThan($firstUnpaidInstallment->interest, $firstUnpaidInstallment->getInstallmentInterest());
        $this->assertEquals($firstUnpaidInstallment->getInstallmentInterest(), $accrInterestFirstInvestment);
        $unpaidInstallmentsFirstInvestment = $investor->getUnpaidInstallments(
            $loan->loan_id,
            $firstInvestment->getId()
        );

        $secondUnpaidInstallment = reset($unpaidInstallmentsSecondInvestment);
        $secondUnpaidInstallment->interest = $fakeInterestSecondInvestment;
        $secondUnpaidInstallment->accrued_interest = $accrInterestSecondInvestment;
        $secondUnpaidInstallment->late_interest = 0.00;
        $secondUnpaidInstallment->save();
        $this->assertLessThan($secondUnpaidInstallment->interest, $secondUnpaidInstallment->getInstallmentInterest());
        $this->assertEquals($secondUnpaidInstallment->getInstallmentInterest(), $accrInterestSecondInvestment);
        $unpaidInstallmentsSecondInvestment = $investor->getUnpaidInstallments(
            $loan->loan_id,
            $secondInvestment->getId()
        );

        $allInvestments = Investment::where(
            [
                'loan_id' => $loan->getId(),
                'investor_id' => $investor->getId(),
            ]
        )->get();

        $this->assertNotEmpty($allInvestments);
        $this->assertCount(2, $allInvestments);


        // emulate repaid loan
        $newRepaidLoan = $this->emulateRepaidLoan($loan, RepaidLoan::TYPE_EARLY);
        $this->validateNewRepaidLoan($loan, $newRepaidLoan, RepaidLoan::TYPE_EARLY);


        // do repayment
        $repaymentDate = Carbon::parse('2020-08-10');

        $distributed = $this->distributeService->distributeLoan($newRepaidLoan, $repaymentDate);
        $this->assertEquals($distributed, true);

        // investor wallet
        $total = $wallet->total_amount;
        $income = $wallet->income;
        $interest = $wallet->interest;
        $uninvested = $wallet->uninvested;
        $lateInterest = $wallet->late_interest;
        $sumPrincipal = $this->sumPrincipal($unpaidInstallmentsFirstInvestment) + $this->sumPrincipal($unpaidInstallmentsSecondInvestment);
        $wallet->refresh();

//        dd($firstUnpaidInstallment, $secondUnpaidInstallment);
        $this->assertEquals($wallet->interest, $firstUnpaidInstallment->getInstallmentInterest() + $secondUnpaidInstallment->getInstallmentInterest());
        $this->assertEquals($wallet->late_interest, $lateInterest);
        $this->assertEquals($wallet->income, ($income + $firstUnpaidInstallment->getInstallmentIncome() + $secondUnpaidInstallment->getInstallmentIncome()));
        $this->assertEquals($wallet->income, ($income + $accrInterestFirstInvestment + $accrInterestSecondInvestment));
        $this->assertEquals($wallet->total_amount, ($total + $firstUnpaidInstallment->getInstallmentIncome() + $secondUnpaidInstallment->getInstallmentIncome()));
        $this->assertEquals($wallet->total_amount, ($total + $accrInterestFirstInvestment + $accrInterestSecondInvestment));
        $this->assertEquals(
            $wallet->uninvested,
            ($uninvested + $firstUnpaidInstallment->getInstallmentIncome() + $secondUnpaidInstallment->getInstallmentIncome() + $sumPrincipal)
        );

        // transaction
        $transaction1 = Transaction::where(
            [
                'investment_id' => $firstInvestment->getId(),
                'investor_id' => $investor->getId(),
                'loan_id' => $loan->getId(),
            ]
        )->orderBy('transaction_id', 'DESC')->first();

        $sumOfPrincipalsFirst = InvestorInstallment::where(
            [
                'investment_id' => $firstInvestment->getId(),
                'investor_id' => $investor->getId(),
                'loan_id' => $loan->getId(),
            ]
        )->sum('principal');

        $this->assertNotEmpty($transaction1);
        $this->assertEquals($transaction1->investor_id, $investor->investor_id);
        $this->assertEquals($transaction1->wallet_id, $wallet->wallet_id);
        $this->assertEquals($transaction1->investment_id, $firstInvestment->getId());
        $this->assertEquals($transaction1->direction, Transaction::DIRECTION_OUT);
        $this->assertEquals($transaction1->type, Transaction::TYPE_EARLY_REPAYMENT);
        $this->assertEquals($transaction1->principal, $sumOfPrincipalsFirst);
        $this->assertEquals($transaction1->accrued_interest, $firstUnpaidInstallment->getInstallmentIncome());
        $this->assertEquals($transaction1->interest, 0);
        $this->assertEquals($transaction1->late_interest, 0);
        $this->assertEquals($transaction1->amount, $sumOfPrincipalsFirst + $firstUnpaidInstallment->getInstallmentIncome());

        $transaction2 = Transaction::where(
            [
                'investment_id' => $secondInvestment->getId(),
                'investor_id' => $investor->getId(),
                'loan_id' => $loan->getId(),
            ]
        )->orderBy('transaction_id', 'DESC')->first();

        $sumOfPrincipalsSecond = InvestorInstallment::where(
            [
                'investment_id' => $secondInvestment->getId(),
                'investor_id' => $investor->getId(),
                'loan_id' => $loan->getId(),
            ]
        )->sum('principal');

        $this->assertNotEmpty($transaction2);
        $this->assertEquals($transaction2->investor_id, $investor->investor_id);
        $this->assertEquals($transaction2->wallet_id, $wallet->wallet_id);
        $this->assertEquals($transaction2->investment_id, $secondInvestment->getId());
        $this->assertEquals($transaction2->direction, Transaction::DIRECTION_OUT);
        $this->assertEquals($transaction2->type, Transaction::TYPE_EARLY_REPAYMENT);
        $this->assertEquals($transaction2->principal, $sumOfPrincipalsSecond);
        $this->assertEquals($transaction2->accrued_interest, $secondUnpaidInstallment->getInstallmentIncome());
        $this->assertEquals($transaction2->interest, 0);
        $this->assertEquals($transaction2->late_interest, 0);
        $this->assertEquals($transaction2->amount, $sumOfPrincipalsSecond + $secondUnpaidInstallment->getInstallmentIncome());

        // remove test data
        $this->removeTestData($investor, $loan);
    }

    public function testLoanNormalRepayment()
    {
        // clear, to use again same investor
        $investor = $this->getInvestor();
        $this->removeTestData($investor);


        // create loan and installments
        $loan = $this->preapreLoan();
        $this->assertNotEmpty($loan->loan_id);

        // emulate that loan should be repaid before 2 days
        $date = Carbon::now()->sub('2 days');
        $loan->final_payment_date = $date->format('Y-m-d');
        $loan->save();

        $installmentsCount = $this->prepareInstallments($loan, 2, [0]);
        $this->assertEquals($installmentsCount, 2);


        // create investor with main attributes
        $investor = $this->prepareInvestor();
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
        $now = Carbon::parse('2020-07-30');
        $invested = $this->investService->invest(
            $investor->investor_id,
            $loan->loan_id,
            $amount,
            $now
        );
        $this->assertEquals($invested, true);
        $wallet->refresh(); // update

        $investment = Investment::where(
            [
                'loan_id' => $loan->getId(),
                'investor_id' => $investor->getId(),
            ]
        )->first();
        // emulate accrued interest equal to interest, means due_date is passed
        $unpaidInstallments = $investor->getUnpaidInstallments(
            $loan->loan_id,
            $investment->getId()
        );
        $firstUnpaidInstallment = reset($unpaidInstallments);
        $firstUnpaidInstallment->accrued_interest = $firstUnpaidInstallment->interest;
        $firstUnpaidInstallment->late_interest = 0.00;
        $firstUnpaidInstallment->save();
        $this->assertEquals($firstUnpaidInstallment->interest, $firstUnpaidInstallment->getInstallmentInterest());
        $this->assertEquals(
            $firstUnpaidInstallment->getInstallmentInterest(),
            $firstUnpaidInstallment->accrued_interest
        );


        // emulate repaid loan
        $newRepaidLoan = $this->emulateRepaidLoan($loan, RepaidLoan::TYPE_NORMAL);
        $this->validateNewRepaidLoan($loan, $newRepaidLoan, RepaidLoan::TYPE_NORMAL);


        // do repayment
        $repaymentDate = Carbon::parse("2020-08-19");
        $distributed = $this->distributeService->distributeLoan($newRepaidLoan, $repaymentDate);
        $this->assertEquals($distributed, true);


        // checks

        // loan repaid
        $loan->refresh();
        $this->assertEquals($loan->status, Loan::STATUS_REPAID);
        $this->assertEquals($loan->unlisted, 1);

        // investor maturity
        $maturity->refresh();
        $this->assertEquals($maturity->range1, 0);
        $this->assertEquals($maturity->range2, 0);
        $this->assertEquals($maturity->range3, 0);
        $this->assertEquals($maturity->range4, 0);
        $this->assertEquals($maturity->range5, 0);

        // investor quality
        $quality->refresh();
        $this->assertEquals($quality->range1, 0);
        $this->assertEquals($quality->range2, 0);
        $this->assertEquals($quality->range3, 0);
        $this->assertEquals($quality->range4, 0);
        $this->assertEquals($quality->range5, 0);
        $ranges = $this->getInvestorQualityRage(
            $investor->investor_id,
            $loan->loan_id
        );
        $this->assertEquals(($ranges->count()), 0);

        // investor wallet
        $total = $wallet->total_amount;
        $income = $wallet->income;
        $interest = $wallet->interest;
        $uninvested = $wallet->uninvested;
        $lateInterest = $wallet->late_interest;
        $sumPrincipal = $this->sumPrincipal($unpaidInstallments);
        $wallet->refresh();
        $this->assertEquals($wallet->interest, $firstUnpaidInstallment->getInstallmentInterest());
        $this->assertEquals($wallet->late_interest, $lateInterest);
        $this->assertEquals($wallet->income, ($income + $firstUnpaidInstallment->getInstallmentIncome()));
        $this->assertEquals($wallet->total_amount, ($total + $firstUnpaidInstallment->getInstallmentIncome()));
        $this->assertEquals(
            $wallet->uninvested,
            ($uninvested + $firstUnpaidInstallment->getInstallmentIncome() + $sumPrincipal)
        );

        // transaction
        $transaction = $this->getTransaction(
            $loan->loan_id,
            $investor->investor_id
        );

        $invInstallments = $investor->installments($loan->loan_id);
        $sumOfPrincipals = 0;
        foreach ($invInstallments as $installment) {
            $sumOfPrincipals += $installment->principal;
        }

        $this->assertEquals($transaction->investor_id, $investor->investor_id);
        $this->assertEquals($transaction->wallet_id, $wallet->wallet_id);
        $this->assertEquals($transaction->direction, Transaction::DIRECTION_OUT);
        $this->assertEquals($transaction->type, Transaction::TYPE_REPAYMENT);
        $this->assertEquals($transaction->principal, $sumOfPrincipals);
        $this->assertEquals($transaction->interest, $firstUnpaidInstallment->getInstallmentIncome());
        $this->assertEquals($transaction->amount, $sumOfPrincipals + $firstUnpaidInstallment->getInstallmentIncome());

        // remove test data
        $this->removeTestData($investor, $loan);
    }

    public function testLoanLateRepayment()
    {
        // clear, to use again same investor
        $investor = $this->getInvestor();
        $this->removeTestData($investor);


        // create loan and installments
        $finalPaymentDate = '2020-08-19';
        $loan = $this->preapreLoan(
            511.30,
            499.82,
            499.82,
            16,
            10,
            18,
            Currency::ID_EUR,
            '2020-06-19',
            '2020-06-19',
            $finalPaymentDate
        );
        $finalPaymentDate = Carbon::parse('2020-08-19');
        $this->assertNotEmpty($loan->loan_id);

        $installmentsCount = $this->prepareInstallments($loan, 2, [0]);
        $this->assertEquals($installmentsCount, 2);


        // create investor with main attributes
        $investor = $this->prepareInvestor('investor_' . time() . '@late.test');
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
        $now = Carbon::parse('2020-07-30');
        $invested = $this->investService->invest(
            $investor->investor_id,
            $loan->loan_id,
            $amount,
            $now
        );
        $this->assertEquals($invested, true);
        $wallet->refresh();


        // emulate accrued interest equal to interest, means due_date is passed
        $interest = 5;
        $interestReal = 5;
        $fakeLateInterest = 3.79;
        $investment = Investment::where(
            [
                'loan_id' => $loan->getId(),
                'investor_id' => $investor->getId(),
            ]
        )->first();
        $unpaidInstallments = $investor->getUnpaidInstallments(
            $loan->loan_id,
            $investment->getId()
        );
        $this->assertEquals(1, count($unpaidInstallments));
        $firstUnpaidInstallment = reset($unpaidInstallments);
        $firstUnpaidInstallment->interest = $interest;
        $firstUnpaidInstallment->accrued_interest = $interest;
        $firstUnpaidInstallment->late_interest = $fakeLateInterest;
        $firstUnpaidInstallment->save();
        $this->assertEquals($firstUnpaidInstallment->interest, $firstUnpaidInstallment->getInstallmentInterest());
        $this->assertEquals(
            $firstUnpaidInstallment->getInstallmentInterest(),
            $firstUnpaidInstallment->accrued_interest
        );
        $unpaidInstallments = $investor->getUnpaidInstallments(
            $loan->loan_id,
            $investment->getId()
        );


        // emulate repaid loan
        $newRepaidLoan = $this->emulateRepaidLoan($loan, RepaidLoan::TYPE_NORMAL);
        $this->validateNewRepaidLoan($loan, $newRepaidLoan, RepaidLoan::TYPE_NORMAL);


        // do repayment
        $repaymentDate = Carbon::parse('2020-08-29');

        // we should manually update maturity, since we emulate repayment on specific date
        $maturity->refresh();
        $maturity->range1 = 0;
        $maturity->range2 = 0;
        $maturity->range3 = 0;
        $maturity->range4 = 0;
        $maturity->range5 = 0;
        $maturity->save();

        // set maturity bases on repayment date
        $range = Portfolio::getMaturityRangeColumnByDate(
            $finalPaymentDate,
            $repaymentDate
        );
        $maturity->{$range} = 1;
        $maturity->save();

        $distributed = $this->distributeService->distributeLoan(
            $newRepaidLoan,
            $repaymentDate
        );
        $this->assertEquals($distributed, true);


        // checks

        // loan repaid
        $loan->refresh();
        $this->assertEquals($loan->status, Loan::STATUS_REPAID);
        $this->assertEquals($loan->unlisted, 1);

        // investor maturity
        $maturity->refresh();
        $this->assertEquals($maturity->range1, 0);
        $this->assertEquals($maturity->range2, 0);
        $this->assertEquals($maturity->range3, 0);
        $this->assertEquals($maturity->range4, 0);
        $this->assertEquals($maturity->range5, 0);

        // investor quality
        $quality->refresh();
        $this->assertEquals($quality->range1, 0);
        $this->assertEquals($quality->range2, 0);
        $this->assertEquals($quality->range3, 0);
        $this->assertEquals($quality->range4, 0);
        $this->assertEquals($quality->range5, 0);
        $ranges = $this->getInvestorQualityRage(
            $investor->investor_id,
            $loan->loan_id
        );
        $this->assertEquals(($ranges->count()), 0);


        // investor wallet
        $total = $wallet->total_amount;
        $income = $wallet->income;
        $interest = $wallet->interest;
        $uninvested = $wallet->uninvested;
        $lateInterest = $wallet->late_interest;
        $sumPrincipal = $this->sumPrincipal($unpaidInstallments);
        $wallet->refresh();

        $this->assertEquals($wallet->interest, $firstUnpaidInstallment->getInstallmentInterest());
        $this->assertLessThan($wallet->late_interest, $lateInterest);
        $this->assertEquals($wallet->late_interest, ($lateInterest + $fakeLateInterest));
        $this->assertEquals($wallet->income, ($income + $firstUnpaidInstallment->getInstallmentIncome()));
        $this->assertEquals($wallet->total_amount, ($total + $firstUnpaidInstallment->getInstallmentIncome()));
        $this->assertEquals(
            $wallet->uninvested,
            ($uninvested + $firstUnpaidInstallment->getInstallmentIncome() + $sumPrincipal)
        );


        // sum of installments.principal == investment.amount
        $sum = 0;
        foreach ($unpaidInstallments as $installment) {
            $sum += $installment->principal;
        }

        // transaction
        $transaction = $this->getTransaction(
            $loan->loan_id,
            $investor->investor_id
        );
        $this->assertEquals($transaction->investor_id, $investor->investor_id);
        $this->assertEquals($transaction->wallet_id, $wallet->wallet_id);
        $this->assertEquals($transaction->direction, Transaction::DIRECTION_OUT);
        $this->assertEquals($transaction->type, Transaction::TYPE_REPAYMENT);
        $this->assertEquals($transaction->principal, $sum);
        $this->assertEquals($transaction->accrued_interest, 0);
        $this->assertEquals($transaction->interest, $interestReal);
        $this->assertEquals($transaction->late_interest, $fakeLateInterest);
        $this->assertEquals($transaction->amount, $sum + 0 + 5 + 3.79);


        // remove test data
        $this->removeTestData($investor, $loan);
    }

    ///////////////////////// HELPER FUNCTIONS ////////////////////////

    private function validateNewRepaidLoan(
        Loan $loan,
        RepaidLoan $newRepaidLoan,
        string $type
    ) {
        $this->assertEquals($newRepaidLoan->lender_id, $loan->lender_id);
        $this->assertEquals($newRepaidLoan->repayment_type, $type);
        $this->assertEquals($newRepaidLoan->handled, 0);
    }

    private function sumPrincipal(array $unpaidInstallments)
    {
        $sumPrincipal = 0;

        foreach ($unpaidInstallments as $unpaidInstallment) {
            $sumPrincipal += $unpaidInstallment->principal;
        }

        return $sumPrincipal;
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
