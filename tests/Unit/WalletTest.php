<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\Transaction;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\InvestService;
use Modules\Common\Services\TransactionService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class WalletTest extends TestCase
{
    use TestDataTrait;

    protected $investService;
    protected $importService;
    protected $investorService;
    protected $distributeService;

    public function setUp(): void
    {
        parent::setUp();

        $this->investService = \App::make(InvestService::class);
        $this->importService = \App::make(ImportService::class);
        $this->investorService = \App::make(InvestorService::class);
        $this->distributeService = \App::make(DistributeService::class);
    }

    public function testWalletTestWithOneInvestment()
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
        $this->prepareInstallmentsWithStartDate(
            $loan,
            Carbon::today()->subMonth()->toDateString(),
            5,
            [],
            Currency::ID_EUR,
        );
        $installments = $loan->installments();


        $investorDeposit = 1000;
        $investor = $this->prepareInvestor('investor_wallet_' . time() . '@test2.com');
        $bankAccount = BankAccount::where(['investor_id' => $investor->investor_id])->first();
        $wallet = $this->getInvestorWallet($investor->investor_id, 0, $currencyId);
        $this->investorService->prepareDataAndAddFunds(
            $investor->investor_id,
            [
                'bank_account_id' => $bankAccount->bank_account_id,
                'amount' => $investorDeposit,
                'bank_transaction_id' => '12314251231',
            ]
        );
        $wallet->refresh();
        $this->preparePortfolios($investor);

        $transactionsDeposit = Transaction::where(
            [
                'investor_id' => $investor->investor_id,
                'type' => Transaction::TYPE_DEPOSIT,
            ]
        )->get();


        // validate invested amount, amount in wallet and transaction amount
        $this->assertEquals(1, $transactionsDeposit->count());
        $transactionsDeposit = $transactionsDeposit->first();
        $this->assertNotEmpty($transactionsDeposit);
        $this->assertEquals($investorDeposit, $transactionsDeposit->amount);

        $this->assertNotEmpty($wallet);
        $this->assertEquals($investorDeposit, $wallet->total_amount);
        $this->assertEquals(0, $wallet->invested);
        $this->assertEquals($investorDeposit, $wallet->uninvested);
        $this->assertEquals($investorDeposit, $wallet->deposit);
        $this->assertEquals(0, $wallet->withdraw);
        $this->assertEquals(0, $wallet->income);
        $this->assertEquals(0, $wallet->interest);
        $this->assertEquals(0, $wallet->late_interest);
        $this->assertEquals(0, $wallet->bonus);


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

        $this->assertTrue($invested);
        $wallet->refresh();

        // validate investment amount VS wallet vs principal of investor installments
        $this->assertEquals($investorDeposit, $wallet->total_amount);
        $this->assertEquals($investorBuyAmount, $wallet->invested);
        $this->assertEquals($investorDeposit - $investorBuyAmount, $wallet->uninvested);
        $this->assertEquals($investorDeposit, $wallet->deposit);
        $this->assertEquals(0, $wallet->withdraw);
        $this->assertEquals(0, $wallet->income);
        $this->assertEquals(0, $wallet->interest);
        $this->assertEquals(0, $wallet->late_interest);
        $this->assertEquals(0, $wallet->bonus);

        $transactionInvestment1 = Transaction::where(
            [
                'investor_id' => $investor->investor_id,
                'type' => Transaction::TYPE_INVESTMENT,
            ]
        )->get();
        $this->assertCount(1, $transactionInvestment1);
        $transactionInvestment1 = $transactionInvestment1->first();
        $this->assertNotEmpty($transactionInvestment1);
        $this->assertEquals($loan->loan_id, $transactionInvestment1->loan_id);
        $this->assertEquals($investorBuyAmount, $transactionInvestment1->amount);

        $investorInstallments = InvestorInstallment::where('investor_id', $investor->investor_id)->get();
        $this->assertNotEmpty($investorInstallments);

        $principalOfInvestent = 0;
        foreach ($investorInstallments as $investorInstallment) {
            $principalOfInvestent += $investorInstallment->principal;
        }
        $this->assertEquals($investorBuyAmount, $principalOfInvestent);

        \Artisan::call('script:daily-interest-refresh');
        $today = Carbon::today();
        foreach ($investorInstallments as $investorInstallment) {
            $installment = $investorInstallment->installment();
            $interest = mt_rand(15, 50) / 10;

            if ($today->lt($installment->due_date)) {
                $investorInstallment->accrued_interest = $interest - (mt_rand(1, 5) / 10);
                $investorInstallment->interest = $interest;
                $investorInstallment->late_interest = 0;
            } elseif ($today->is($installment->due_date)) {
                $investorInstallment->accrued_interest = $interest;
                $investorInstallment->interest = $interest;
                $investorInstallment->late_interest = 0;
            } else {
                $investorInstallment->accrued_interest = $interest;
                $investorInstallment->interest = $interest;
                $investorInstallment->late_interest = mt_rand(5, 10) / 10;
            }

            $investorInstallment->save();
        }

        // make repament
        $this->distributeService->distributeLoan($this->emulateRepaidLoan($loan));

        $wallet->refresh();
        $this->assertEquals(0, $wallet->invested);

        $sums = $this->validateRepayment($investor, $wallet, $loan);

        $wallet->refresh();
        $this->assertEquals($wallet->income, ($sums['accrued_interest'] + $sums['interest'] + $sums['late_interest']));
        $this->assertEquals($wallet->total_amount, ($investorDeposit + ($sums['accrued_interest'] + $sums['interest'] + $sums['late_interest'])));
        $this->assertEquals($wallet->uninvested, ($investorDeposit + ($sums['accrued_interest'] + $sums['interest'] + $sums['late_interest'])));
        $this->assertEquals($wallet->invested, 0);
        $this->assertEquals($wallet->interest, ($sums['accrued_interest'] + $sums['interest']));
        $this->assertEquals($wallet->late_interest, $sums['late_interest']);


        DB::table('transaction')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor, $loan);
    }

    public function testWalletTestWithThreeInvestments()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $loanAmount = 1222.46;
        $remainingPricipal = 1222.46;
        $interestRate = 14.50;
        $issueDate = Carbon::today()->toDateString();
        $listingDate = Carbon::today()->toDateString();
        $finalPaymentDate = Carbon::today()->addMonths(4);

        $loan1 = $this->preapreLoan(
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
        $loan2 = $this->preapreLoan(
            1200,
            1200,
            1200,
            16,
            10, // originator percent
            4, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            Carbon::today()->subMonth()
        );
        $loan3 = $this->preapreLoan(
            500,
            500,
            500,
            16,
            10, // originator percent
            4, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            Carbon::today()->addMonth()
        );
        $this->prepareInstallmentsWithStartDate(
            $loan1,
            Carbon::today()->subMonth()->toDateString(),
            5,
            [],
            Currency::ID_EUR,
        );
        $this->prepareInstallmentsWithStartDate(
            $loan2,
            Carbon::today()->subMonths(5)->toDateString(),
            4,
            [],
            Currency::ID_EUR,
        );
        $this->prepareInstallmentsWithStartDate(
            $loan3,
            Carbon::today()->subMonths(2)->toDateString(),
            4,
            [],
            Currency::ID_EUR,
        );

        $installments1 = $loan1->installments();
        $installments2 = $loan2->installments();
        $installments3 = $loan3->installments();

        $investorDeposit = 2000;
        $investor = $this->prepareInvestor('investor_wallet_' . time() . '@test2.com');
        $bankAccount = BankAccount::where(['investor_id' => $investor->investor_id])->first();
        $wallet = $this->getInvestorWallet($investor->investor_id, 0, $currencyId);
        $this->investorService->prepareDataAndAddFunds(
            $investor->investor_id,
            [
                'bank_account_id' => $bankAccount->bank_account_id,
                'amount' => $investorDeposit,
                'bank_transaction_id' => '12314251231',
            ]
        );
        $wallet->refresh();
        $this->preparePortfolios($investor);

        $transactionsDeposit = Transaction::where(
            [
                'investor_id' => $investor->investor_id,
                'type' => Transaction::TYPE_DEPOSIT,
            ]
        )->get();
        $this->assertEquals(1, $transactionsDeposit->count());
        $transactionsDeposit = $transactionsDeposit->first();
        $this->assertNotEmpty($transactionsDeposit);
        $this->assertEquals($investorDeposit, $transactionsDeposit->amount);

        $this->assertNotEmpty($wallet);
        $this->assertEquals($investorDeposit, $wallet->total_amount);
        $this->assertEquals(0, $wallet->invested);
        $this->assertEquals($investorDeposit, $wallet->uninvested);
        $this->assertEquals($investorDeposit, $wallet->deposit);
        $this->assertEquals(0, $wallet->withdraw);
        $this->assertEquals(0, $wallet->income);
        $this->assertEquals(0, $wallet->interest);
        $this->assertEquals(0, $wallet->late_interest);
        $this->assertEquals(0, $wallet->bonus);

        // do invest
        $investorBuyAmount1 = 200;
        $investorBuyAmount2 = 200;
        $investorBuyAmount3 = 200;
        $now = Carbon::parse($issueDate);
        $invested = $this->investService->doInvest(
            $investorBuyAmount1,
            $investor,
            $wallet,
            $loan1,
            $installments1,
            $now
        );
        $this->assertTrue($invested);
        $invested = $this->investService->doInvest(
            $investorBuyAmount2,
            $investor,
            $wallet,
            $loan2,
            $installments2,
            $now
        );
        $this->assertTrue($invested);
        $invested = $this->investService->doInvest(
            $investorBuyAmount3,
            $investor,
            $wallet,
            $loan3,
            $installments3,
            $now
        );
        $this->assertTrue($invested);

        $wallet->refresh();

        $invested = ($investorBuyAmount1 + $investorBuyAmount2 + $investorBuyAmount3);
        $this->assertEquals($investorDeposit, $wallet->total_amount);
        $this->assertEquals($invested, $wallet->invested);
        $this->assertEquals($investorDeposit - $invested, $wallet->uninvested);
        $this->assertEquals($investorDeposit, $wallet->deposit);
        $this->assertEquals(0, $wallet->withdraw);
        $this->assertEquals(0, $wallet->income);
        $this->assertEquals(0, $wallet->interest);
        $this->assertEquals(0, $wallet->late_interest);
        $this->assertEquals(0, $wallet->bonus);

        $transactionInvestments = Transaction::where(
            [
                'investor_id' => $investor->investor_id,
                'type' => Transaction::TYPE_INVESTMENT,
            ]
        )->get();
        $this->assertCount(3, $transactionInvestments);
        $transactionInvestments1 = $transactionInvestments->where('loan_id', $loan1->loan_id)->first();
        $transactionInvestments2 = $transactionInvestments->where('loan_id', $loan2->loan_id)->first();
        $transactionInvestments3 = $transactionInvestments->where('loan_id', $loan3->loan_id)->first();

        $this->assertNotEmpty($transactionInvestments1);
        $this->assertEquals($loan1->loan_id, $transactionInvestments1->loan_id);
        $this->assertEquals($investorBuyAmount1, $transactionInvestments1->amount);
        $this->assertNotEmpty($transactionInvestments2);
        $this->assertEquals($loan2->loan_id, $transactionInvestments2->loan_id);
        $this->assertEquals($investorBuyAmount2, $transactionInvestments2->amount);
        $this->assertNotEmpty($transactionInvestments3);
        $this->assertEquals($loan3->loan_id, $transactionInvestments3->loan_id);
        $this->assertEquals($investorBuyAmount3, $transactionInvestments3->amount);

        $investorInstallmentsPrincipalSum1 = InvestorInstallment::where(
                [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan1->loan_id
            ]
        )->sum('principal');
        $this->assertEquals($investorInstallmentsPrincipalSum1, $transactionInvestments1->amount);

        $investorInstallmentsPrincipalSum2 = InvestorInstallment::where(
                [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan2->loan_id
            ]
        )->sum('principal');
        $this->assertEquals($investorInstallmentsPrincipalSum2, $transactionInvestments2->amount);

        $investorInstallmentsPrincipalSum3 = InvestorInstallment::where(
                [
                'investor_id' => $investor->investor_id,
                'loan_id' => $loan3->loan_id
            ]
        )->sum('principal');
        $this->assertEquals($investorInstallmentsPrincipalSum3, $transactionInvestments3->amount);



        $investorInstallments = InvestorInstallment::where('investor_id', $investor->investor_id)->get();
        $this->assertNotEmpty($investorInstallments);



        \Artisan::call('script:daily-interest-refresh');
        $today = Carbon::today();
        foreach ($investorInstallments as $investorInstallment) {
            $installment = $investorInstallment->installment();
            $interest = mt_rand(15, 50) / 10;

            if ($today->lt($installment->due_date)) {
                $investorInstallment->accrued_interest = $interest - (mt_rand(1, 5) / 10);
                $investorInstallment->interest = $interest;
                $investorInstallment->late_interest = 0;
            } elseif ($today->is($installment->due_date)) {
                $investorInstallment->accrued_interest = $interest;
                $investorInstallment->interest = $interest;
                $investorInstallment->late_interest = 0;
            } else {
                $investorInstallment->accrued_interest = $interest;
                $investorInstallment->interest = $interest;
                $investorInstallment->late_interest = mt_rand(5, 10) / 10;
            }

            $investorInstallment->save();
        }

        $this->distributeService->distributeLoan($this->emulateRepaidLoan($loan1));
        $this->distributeService->distributeLoan($this->emulateRepaidLoan($loan2));
        $this->distributeService->distributeLoan($this->emulateRepaidLoan($loan3));


        $wallet->refresh();

        $this->assertEquals(0, $wallet->invested);


        $sums1 = $this->validateRepayment($investor, $wallet, $loan1);
        $sums2 = $this->validateRepayment($investor, $wallet, $loan2);
        $sums3 = $this->validateRepayment($investor, $wallet, $loan3);

        $sums = [
            'accrued_interest' => ($sums1['accrued_interest'] + $sums2['accrued_interest'] + $sums3['accrued_interest']),
            'late_interest' => ($sums1['late_interest'] + $sums2['late_interest'] + $sums3['late_interest']),
            'interest' => ($sums1['interest'] + $sums2['interest'] + $sums3['interest']),
        ];

        $wallet->refresh();
        $this->assertEquals($wallet->income, ($sums['accrued_interest'] + $sums['interest'] + $sums['late_interest']));
        $this->assertEquals($wallet->total_amount, ($investorDeposit + ($sums['accrued_interest'] + $sums['interest'] + $sums['late_interest'])));
        $this->assertEquals($wallet->uninvested, ($investorDeposit + ($sums['accrued_interest'] + $sums['interest'] + $sums['late_interest'])));
        $this->assertEquals($wallet->invested, 0);
        $this->assertEquals($wallet->interest, ($sums['accrued_interest'] + $sums['interest']));
        $this->assertEquals($wallet->late_interest, $sums['late_interest']);

        DB::table('transaction')->where('investor_id', $investor->investor_id)->delete();
        $this->removeTestData($investor, $loan1);
        $this->removeTestData(null, $loan2);
        $this->removeTestData(null, $loan3);
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

    private function validateRepayment($investor, $wallet, $loan)
    {
        $investorInstallments = InvestorInstallment::where([
            'investor_id' => $investor->investor_id,
            'loan_id' => $loan->loan_id,
        ])->get()->all();


        $sums = [
            'principal' => 0,
            'accrued_interest' => 0,
            'interest' => 0,
            'late_interest' => 0,
        ];
        foreach ($investorInstallments as $investorInstallment) {
            $sums['principal'] += $investorInstallment->principal;
            if ($investorInstallment->accrued_interest < $investorInstallment->interest) {
                $sums['accrued_interest'] += $investorInstallment->accrued_interest;
            } else {
                $sums['interest'] += $investorInstallment->interest;
            }

            $sums['late_interest'] += $investorInstallment->late_interest;
        }

        // validate repayments: transaction vs investor installments vs wallet
        $repaymentTransaction = Transaction::where([
            'investor_id' => $investor->investor_id,
            'loan_id' => $loan->loan_id,
            'type' => Transaction::TYPE_REPAYMENT
        ])->get();
        $this->assertCount(1, $repaymentTransaction);

        $repaymentTransaction = $repaymentTransaction->first();
        $this->assertEquals(
            ($sums['principal'] + $sums['accrued_interest'] + $sums['interest'] + $sums['late_interest']),
            $repaymentTransaction->amount
        );
        $this->assertEquals($sums['principal'], $repaymentTransaction->principal);
        $this->assertEquals($sums['accrued_interest'], $repaymentTransaction->accrued_interest);
        $this->assertEquals($sums['interest'], $repaymentTransaction->interest);
        $this->assertEquals($sums['late_interest'], $repaymentTransaction->late_interest);

        return $sums;
    }
}
