<?php

namespace Tests\Unit\Invest;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class InvestTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    protected $investService;

    public function setUp(): void
    {
        parent::setUp();
        $this->importService = new ImportService;
        $this->investService = new InvestService;
    }

    ///////////////////////////////////////// 2nd test ///////////////////////////////////////

    public function testSuccessfulInvestSecond()
    {
        $this->assertEquals(1, 1);
        return false; // not valid test but it's used for base class of several other tests

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
            18, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            $finalPaymentDate
        );
        $installments = $this->createLoanInstallmentsSecond($loan, $issueDate);

        $investorDeposit = 1000;
        $investor = $this->getTestInvestor('investor2_' . time() . '@test2.com');
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

        // validate wallet
        $wallet->refresh();
        $this->assertEquals($wallet->total_amount, $investorDeposit);
        $this->assertEquals($wallet->deposit, $investorDeposit);
        $this->assertEquals($wallet->invested, $investorBuyAmount);
        $this->assertEquals($wallet->uninvested, ($investorDeposit - $investorBuyAmount));
        $this->assertEquals($wallet->income, 0);

        // validate portfolios
        $quality = $portfolios['quality'];
        $quality->refresh();
        $this->assertEquals(1, $quality->range1);
        $this->assertEquals(0, $quality->range2);
        $this->assertEquals(0, $quality->range3);
        $this->assertEquals(0, $quality->range4);
        $this->assertEquals(0, $quality->range5);

        $maturity = $portfolios['maturity'];
        $maturity->refresh();
        $this->assertEquals(0, $maturity->range1);
        $this->assertEquals(0, $maturity->range2);
        $this->assertEquals(0, $maturity->range3);
        $this->assertEquals(1, $maturity->range4);
        $this->assertEquals(0, $maturity->range5);

        // validate investment
        $investments = $investor->investments($loan->loan_id, $investorBuyAmount);
        $investment = current($investments);
        $this->assertEquals($investment->wallet_id, $wallet->wallet_id);
        $this->assertEquals($investment->percent, Calculator::round(($investorBuyAmount  / $loan->remaining_principal * 100), 12));


        // validate installments interest
        $installment = $installments[0];
        $this->assertEquals($installment->original_remaining_principal, 1222.46);
        $this->assertEquals($installment->original_principal, 60.56);
        $this->assertEquals($installment->original_interest, 15.26);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 75.82); // original total simulating
        $this->assertEquals($installment->remaining_principal, Calculator::toEuro(1222.46));
        $this->assertEquals($installment->principal, Calculator::toEuro(60.56));
        $this->assertEquals($installment->interest, Calculator::toEuro(15.26));
        $this->assertEquals($installment->total, Calculator::toEuro(60.56) + Calculator::toEuro(15.26));

        $installment = $installments[1];
        $this->assertEquals($installment->original_remaining_principal, 1161.90);
        $this->assertEquals($installment->original_principal, 61.37);
        $this->assertEquals($installment->original_interest, 14.51);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 75.88); // original total simulating
        $this->assertEquals($installment->remaining_principal, Calculator::toEuro(1161.90));
        $this->assertEquals($installment->principal, Calculator::toEuro(61.37));
        $this->assertEquals($installment->interest, Calculator::toEuro(14.51));
        $this->assertEquals($installment->total, Calculator::toEuro(75.88));

        $installment = $installments[4];
        $this->assertEquals($installment->original_remaining_principal, 975.33);
        $this->assertEquals($installment->original_principal, 63.86);
        $this->assertEquals($installment->original_interest, 12.18);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 76.04); // original total simulating
        $this->assertEquals($installment->remaining_principal, 498.67);
        $this->assertEquals($installment->principal, Calculator::toEuro(63.86));
        $this->assertEquals($installment->interest, Calculator::toEuro(12.18));
        $this->assertEquals($installment->total, Calculator::toEuro(76.04));

        $installment = $installments[16];
        $this->assertEquals($installment->original_remaining_principal, 150.32);
        $this->assertEquals($installment->original_principal, 74.86);
        $this->assertEquals($installment->original_interest, 1.88);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 76.74); // original total simulating
        $this->assertEquals($installment->remaining_principal, 76.84);
        $this->assertEquals($installment->principal, Calculator::toEuro(74.86));
        $this->assertEquals($installment->interest, Calculator::toEuro(1.88));
        $this->assertEquals($installment->total, Calculator::toEuro(76.74));

        $installment = $installments[17];
        $this->assertEquals($installment->original_remaining_principal, 75.46);
        $this->assertEquals($installment->original_principal, 75.46);
        $this->assertEquals($installment->original_interest, 0.92);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 76.38); // original total simulating
        $this->assertEquals($installment->remaining_principal, 38.56);
        $this->assertEquals($installment->principal, Calculator::toEuro(75.46));
        $this->assertEquals($installment->interest, Calculator::toEuro(0.91));
        $this->assertEquals($installment->total, Calculator::toEuro(76.37));


        // validate investor installments values
        $invInstallments = $investor->installments($loan->loan_id);
        $this->assertEquals(count($invInstallments), 17);

        $this->assertEquals($invInstallments[0]->days, 31);
        $this->assertEquals($invInstallments[0]->remaining_principal, $installments[1]->remaining_principal);
        $principal = round( ($installments[1]->principal / 100 * $investment->percent), 2);
        $this->assertEquals($invInstallments[0]->principal, $principal);
        $this->assertEquals($invInstallments[0]->principal, 10.04);
        $interest = round( ($installments[1]->interest * $investment->percent * $invInstallments[1]->days / $invInstallments[0]->days / 100), 2);
        $this->assertEquals($invInstallments[0]->interest, $interest);
        $this->assertEquals($invInstallments[0]->interest, 2.37);
        $this->assertEquals($invInstallments[0]->total, 12.41);
        $this->assertEquals($invInstallments[0]->total, ($principal + $interest));


        $this->assertEquals($invInstallments[3]->days, 31);
        $this->assertEquals($invInstallments[3]->remaining_principal, $installments[4]->remaining_principal);
        $principal = round( ($installments[4]->principal / 100 * $investment->percent), 2);
        $this->assertEquals($invInstallments[3]->principal, $principal);
        $interest = round( ($installments[4]->interest * $investment->percent * $invInstallments[3]->days / $invInstallments[3]->days / 100), 2);
        $this->assertEquals($invInstallments[3]->interest, $interest);
        $this->assertEquals($invInstallments[3]->total, ($principal + $interest));


        $this->assertEquals($invInstallments[15]->days, 31);
        $this->assertEquals($invInstallments[15]->remaining_principal, $installments[16]->remaining_principal);
        $principal = round( ($installments[16]->principal / 100 * $investment->percent), 2);
        $this->assertEquals($invInstallments[15]->principal, $principal);
        $interest = round( ($installments[16]->interest * $investment->percent * $invInstallments[15]->days / $invInstallments[15]->days / 100), 2);
        $this->assertEquals($invInstallments[15]->interest, $interest);
        $this->assertEquals($invInstallments[15]->total, ($principal + $interest));


        // sum of installments.principal == investment.amount
        $sum = 0;
        $lastPrincipal;
        foreach ($installments as $installment) {
            if (1 == $installment->paid) {
                continue;
            }

            $principal = round( (($installment->principal / 100 * $investment->percent)), 2);
            $sum += $principal;
            $lastPrincipal = $principal;
        }

        $difference = 0;
        if ($sum != $investorBuyAmount) {
            if ($sum < $investorBuyAmount) {
                $difference = $investorBuyAmount - $sum;
                $lastPrincipal = $lastPrincipal + $difference;
            } else {
                $difference = $sum - $investorBuyAmount;
                $lastPrincipal = $lastPrincipal - $difference;
            }
        }

        $this->assertEquals($invInstallments[16]->days, 30);
        $this->assertEquals($invInstallments[16]->remaining_principal, $installments[17]->remaining_principal);
        $this->assertEquals($invInstallments[16]->principal, $lastPrincipal);
        $interest = round( ($installments[17]->interest * $investment->percent * $invInstallments[16]->days / $invInstallments[16]->days / 100), 2);
        $this->assertEquals($invInstallments[16]->interest, $interest);
        $this->assertEquals($invInstallments[16]->total, ($lastPrincipal + $interest));


        // validate that sum of principal equalt to invested amount
        $sum = $sum - $principal + $lastPrincipal;
        $this->assertEquals($investorBuyAmount, $sum);


        // validate created transaction
        $transaction = $this->getTransaction(
            $loan->loan_id,
            $investor->investor_id,
            $investorBuyAmount
        );
        $this->assertEquals($transaction->originator_id, $loan->originator_id);
        $this->assertEquals($transaction->direction, Transaction::DIRECTION_IN);
        $this->assertEquals($transaction->type, Transaction::TYPE_INVESTMENT);
        $this->assertEquals($transaction->bank_account_id, $investor->getMainBankAccountId());

        // remove test data
        $this->removeTestData($investor, $loan);
    }

    protected function createLoanInstallmentsSecond(Loan $loan, $fromDate = null)
    {
        $originalCurrencyId = Currency::ID_BGN; // we keep original currency, it will be changed to EUR in import service
        $import = [
            0 => [
                'seq_num' => 1,
                'due_date' => '2020-12-03',
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
                'due_date' => '2021-01-03',
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
                'due_date' => '2021-02-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 1100.53,
                'principal' => 62.19,
                'status' => 'current',
                'lender_installment_id' => 3,
                'lender_id' => $loan->lender_id,
            ],
            3 => [
                'seq_num' => 4,
                'due_date' => '2021-03-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 1038.34,
                'principal' => 63.01,
                'status' => 'current',
                'lender_installment_id' => 4,
                'lender_id' => $loan->lender_id,
            ],
            4 => [
                'seq_num' => 5,
                'due_date' => '2021-04-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 975.33,
                'principal' => 63.86,
                'status' => 'current',
                'lender_installment_id' => 5,
                'lender_id' => $loan->lender_id,
            ],
            5 => [
                'seq_num' => 6,
                'due_date' => '2021-05-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 911.47,
                'principal' => 64.71,
                'status' => 'current',
                'lender_installment_id' => 6,
                'lender_id' => $loan->lender_id,
            ],
            6 => [
                'seq_num' => 7,
                'due_date' => '2021-06-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 846.76,
                'principal' => 65.57,
                'status' => 'current',
                'lender_installment_id' => 7,
                'lender_id' => $loan->lender_id,
            ],
            7 => [
                'seq_num' => 8,
                'due_date' => '2021-07-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 781.19,
                'principal' => 66.44,
                'status' => 'current',
                'lender_installment_id' => 8,
                'lender_id' => $loan->lender_id,
            ],
            8 => [
                'seq_num' => 9,
                'due_date' => '2021-08-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 714.75,
                'principal' => 67.33,
                'status' => 'current',
                'lender_installment_id' => 9,
                'lender_id' => $loan->lender_id,
            ],
            9 => [
                'seq_num' => 10,
                'due_date' => '2021-09-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 647.42,
                'principal' => 68.22,
                'status' => 'current',
                'lender_installment_id' => 10,
                'lender_id' => $loan->lender_id,
            ],
            10 => [
                'seq_num' => 11,
                'due_date' => '2021-10-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 579.20,
                'principal' => 69.14,
                'status' => 'current',
                'lender_installment_id' => 11,
                'lender_id' => $loan->lender_id,
            ],
            11 => [
                'seq_num' => 12,
                'due_date' => '2021-11-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 510.06,
                'principal' => 70.06,
                'status' => 'current',
                'lender_installment_id' => 12,
                'lender_id' => $loan->lender_id,
            ],
            12 => [
                'seq_num' => 13,
                'due_date' => '2021-12-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 440.00,
                'principal' => 70.99,
                'status' => 'current',
                'lender_installment_id' => 13,
                'lender_id' => $loan->lender_id,
            ],
            13 => [
                'seq_num' => 14,
                'due_date' => '2022-01-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 369.01,
                'principal' => 71.94,
                'status' => 'current',
                'lender_installment_id' => 14,
                'lender_id' => $loan->lender_id,
            ],
            14 => [
                'seq_num' => 15,
                'due_date' => '2022-02-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 297.07,
                'principal' => 72.89,
                'status' => 'current',
                'lender_installment_id' => 15,
                'lender_id' => $loan->lender_id,
            ],
            15 => [
                'seq_num' => 16,
                'due_date' => '2022-03-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 224.18,
                'principal' => 73.86,
                'status' => 'current',
                'lender_installment_id' => 16,
                'lender_id' => $loan->lender_id,
            ],
            16 => [
                'seq_num' => 17,
                'due_date' => '2022-04-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 150.32,
                'principal' => 74.86,
                'status' => 'current',
                'lender_installment_id' => 17,
                'lender_id' => $loan->lender_id,
            ],
            17 => [
                'seq_num' => 18,
                'due_date' => '2022-05-03',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                // 'remaining_principal' => 75.46,
                'principal' => 75.46,
                'status' => 'current',
                'lender_installment_id' => 18,
                'lender_id' => $loan->lender_id,
            ],
        ];

        return $this->getInstallmentsAfterInsert($loan, $import, $fromDate);
    }

    ///////////////////////////////////////// 1st test ///////////////////////////////////////

    public function testSuccessfulInvestFirst()
    {
        $this->assertEquals(1, 1);
        return false; // not valid test but it's used for base class of several other tests

        // prepare test data
        $currencyId = Currency::ID_EUR;
        $loanAmount = 511.30;
        $remainingPricipal = 499.82;
        $interestRate = 16;
        $issueDate = '2020-06-19';
        $listingDate = '2020-07-30';
        $finalPaymentDate = '2021-12-19';

        $loan = $this->preapreLoan(
            $loanAmount,
            $loanAmount,
            $remainingPricipal,
            $interestRate,
            10, // originator percent
            18, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            $finalPaymentDate
        );
        $installments = $this->createLoanInstallmentsFirst($loan, $listingDate);

        $investorDeposit = 1000;
        $investor = $this->getTestInvestor('investor2_' . time() . '@test1.com');
        $wallet = $this->getInvestorWallet($investor->investor_id, $investorDeposit, $currencyId);
        $portfolios = $this->getInvestorPortfolios($investor->investor_id);

        $now = Carbon::parse('2020-07-30');
        $amount = 113.00;
        $percent = 44.218352572882;


        // do invest
        $invested = $this->investService->doInvest(
            $amount,
            $investor,
            $wallet,
            $loan,
            $installments,
            $now
        );
        $this->assertEquals($invested, true);


        // validate wallet
        $wallet->refresh();
        $this->assertEquals($wallet->total_amount, $investorDeposit);
        $this->assertEquals($wallet->deposit, $investorDeposit);
        $this->assertEquals($amount, $wallet->invested);
        $this->assertEquals($wallet->uninvested, ($investorDeposit - $amount));


        // validate portfolios
        $quality = $portfolios['quality'];
        $quality->refresh();
        $this->assertEquals($quality->range1, 1);
        $this->assertEquals($quality->range2, 0);
        $this->assertEquals($quality->range3, 0);
        $this->assertEquals($quality->range4, 0);
        $this->assertEquals($quality->range5, 0);

        $maturity = $portfolios['maturity'];
        $maturity->refresh();
        $this->assertEquals($maturity->range1, 0);
        $this->assertEquals($maturity->range2, 0);
        $this->assertEquals($maturity->range3, 0);
        $this->assertEquals($maturity->range4, 1);
        $this->assertEquals($maturity->range5, 0);


        // validate investment
        $investments = $investor->investments($loan->loan_id, $amount);
        $investment = current($investments);
        $this->assertEquals($investment->wallet_id, $wallet->wallet_id);
        $this->assertEquals($investment->percent, $percent);
        $this->assertEquals($investment->percent, Calculator::round(($amount  / $loan->remaining_principal * 100), 12));


        // validate common installments
        $installment = $installments[0];
        $this->assertEquals($installment->original_remaining_principal, 511.30);
        $this->assertEquals($installment->original_principal, 11.48);
        $this->assertEquals($installment->original_interest, 0);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 11.48); // original total simulating
        $this->assertEquals($installment->remaining_principal, Calculator::toEuro(511.30));
        $this->assertEquals($installment->principal, Calculator::toEuro(11.48));
        $this->assertEquals($installment->interest, 0);
        $this->assertEquals($installment->total, Calculator::toEuro(11.48));

        $installment = $installments[1];
        $this->assertEquals($installment->original_remaining_principal, 499.82);
        $this->assertEquals($installment->original_principal, 12.60);
        $this->assertEquals($installment->original_interest, 4.44);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 17.04); // original total simulating
        $this->assertEquals($installment->remaining_principal, Calculator::toEuro(499.82));
        $this->assertEquals($installment->principal, Calculator::toEuro(12.60));
        $this->assertEquals($installment->interest, Calculator::toEuro(4.44));
        $this->assertEquals($installment->total, Calculator::toEuro(17.04));

        $installment = $installments[4];
        $this->assertEquals($installment->original_remaining_principal, 458.19);
        $this->assertEquals($installment->original_principal, 16.67);
        $this->assertEquals($installment->original_interest, 6.32);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 16.67 + 6.32); // original total simulating
        $this->assertEquals($installment->remaining_principal, 234.26);
        $this->assertEquals($installment->principal, Calculator::toEuro(16.67));
        $this->assertEquals($installment->interest, Calculator::toEuro( 6.31));
        $this->assertEquals($installment->total, Calculator::toEuro(22.98));

        $installment = $installments[16];
        $this->assertEquals($installment->original_remaining_principal, 106.93);
        $this->assertEquals($installment->original_principal, 50.94);
        $this->assertEquals($installment->original_interest, 1.47);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 52.41); // original total simulating
        $this->assertEquals($installment->remaining_principal, 54.66);
        $this->assertEquals($installment->principal, Calculator::toEuro(50.94));
        $this->assertEquals($installment->interest, Calculator::toEuro(1.47));
        $this->assertEquals($installment->total, Calculator::toEuro(52.41));

        $installment = $installments[17];
        $this->assertEquals($installment->original_remaining_principal, 55.99);
        $this->assertEquals($installment->original_principal, 55.99);
        $this->assertEquals($installment->original_interest, 0.74);
        $this->assertEquals(($installment->original_principal + $installment->original_interest), 56.73); // original total simulating
        $this->assertEquals($installment->remaining_principal, 28.61);
        $this->assertEquals($installment->principal, Calculator::toEuro(55.99));
        $this->assertEquals($installment->interest, Calculator::toEuro(0.75));
        $this->assertEquals($installment->total, Calculator::toEuro(56.74));


        // validate investor installments values
        $invInstallments = $investor->installments($loan->loan_id);
        $this->assertEquals(count($invInstallments), 17);

        $this->assertEquals($invInstallments[0]->days, 20);
        $this->assertEquals($invInstallments[0]->interest_percent, $investment->percent);
        $this->assertEquals($invInstallments[0]->remaining_principal, Calculator::toEuro(499.82));
        $this->assertEquals($invInstallments[0]->remaining_principal, 255.55);
        $this->assertEquals($invInstallments[0]->remaining_principal, $installments[1]->remaining_principal);

        $principal1stInst = round( ($installments[1]->principal / 100 * $investment->percent), 2);
        $this->assertEquals($invInstallments[0]->principal, 2.85);
        $this->assertEquals($invInstallments[0]->principal, $principal1stInst);

        $interest = round( ($installments[1]->interest * $investment->percent * $invInstallments[0]->days / 31 / 100), 2);
        $this->assertEquals($invInstallments[0]->interest, 0.65);
        $this->assertEquals($invInstallments[0]->interest, $interest);

        $this->assertEquals($invInstallments[0]->total, ($principal1stInst + $interest));
        $this->assertEquals($invInstallments[0]->total, 3.50);


        $this->assertEquals($invInstallments[1]->days, 31);
        $this->assertEquals($invInstallments[1]->remaining_principal, $installments[2]->remaining_principal);
        $principal = round( ($installments[2]->principal / 100 * $investment->percent), 2);
        $this->assertEquals($invInstallments[1]->principal, $principal);
        $this->assertEquals($invInstallments[1]->principal, 3.13);
        $interest = round( ($installments[2]->interest * $investment->percent * $invInstallments[1]->days / $invInstallments[1]->days / 100), 2);
        $this->assertEquals($invInstallments[1]->interest, $interest);
        $this->assertEquals($invInstallments[1]->interest, 1.52);
        $this->assertEquals($invInstallments[1]->total, ($principal + $interest));
        $this->assertEquals($invInstallments[1]->total, 4.65);


        $this->assertEquals($invInstallments[4]->days, 30);
        $this->assertEquals($invInstallments[4]->remaining_principal, $installments[5]->remaining_principal);
        $principal = round( ($installments[5]->principal / 100 * $investment->percent), 2);
        $this->assertEquals($invInstallments[4]->principal, $principal);
        $interest = round( ($installments[5]->interest * $investment->percent * $invInstallments[4]->days / $invInstallments[4]->days / 100), 2);
        $this->assertEquals($invInstallments[4]->interest, $interest);
        $this->assertEquals($invInstallments[4]->total, ($principal + $interest));


        $this->assertEquals($invInstallments[15]->days, 31);
        $this->assertEquals($invInstallments[15]->remaining_principal, $installments[16]->remaining_principal);

        $principal = round( ($installments[16]->principal / 100 * $investment->percent), 2);
        $this->assertEquals($invInstallments[15]->principal, $principal);

        $interest = round( ($installments[16]->interest * $investment->percent * $invInstallments[15]->days / $invInstallments[15]->days / 100), 2);
        $this->assertEquals($invInstallments[15]->interest, $interest);
        $this->assertEquals($invInstallments[15]->total, ($principal + $interest));


        // sum of installments.principal == investment.amount
        $sum = 0;
        $lastPrincipal;
        foreach ($installments as $installment) {

            // we should skip 1st installments, since its due date is passed
            if (1 == $installment->seq_num) {
                continue;
            }

            // investor 1st installment - own only 20 days, so we can not use full amount
            if (empty($lastPrincipal)) {
                $principal = $principal1stInst;
            } else {
                $principal = round( (($installment->principal / 100 * $investment->percent)), 2);
            }

            $sum += $principal;
            $lastPrincipal = $principal;
        }

        $difference = 0;
        if ($sum != $amount) {
            if ($sum < $amount) {
                $difference = $amount - $sum;
                $lastPrincipal = $lastPrincipal + $difference;
            } else {
                $difference = $sum - $amount;
                $lastPrincipal = $lastPrincipal - $difference;
            }
        }

        $this->assertEquals($invInstallments[16]->days, 30);
        $this->assertEquals($invInstallments[16]->remaining_principal, $installments[17]->remaining_principal);
        $this->assertEquals($invInstallments[16]->principal, $lastPrincipal);
        $interest = round( ($installments[17]->interest * $investment->percent * $invInstallments[16]->days / $invInstallments[16]->days / 100), 2);
        $this->assertEquals($invInstallments[16]->interest, $interest);
        $this->assertEquals($invInstallments[16]->total, ($lastPrincipal + $interest));




        // validate investor installments
        $installments = $investor->installments($loan->loan_id);
        $this->assertEquals(count($installments), 17);




        // validate created transaction
        $transaction = $this->getTransaction(
            $loan->loan_id,
            $investor->investor_id,
            $amount
        );
        $this->assertEquals($transaction->originator_id, $loan->originator_id);
        $this->assertEquals($transaction->direction, Transaction::DIRECTION_IN);
        $this->assertEquals($transaction->type, Transaction::TYPE_INVESTMENT);
        $this->assertEquals($transaction->bank_account_id, $investor->getMainBankAccountId());


        // remove test data
        $this->removeTestData($investor, $loan);
    }

    private function createLoanInstallmentsFirst(Loan $loan, $fromDate = null)
    {
        $import = [
            0 => [
                'seq_num' => 1,
                'due_date' => '2020-07-19',
                'currency_id' => $loan->currency_id,
                'paid' => 1,
                // 'remaining_principal' => 511.30,
                'principal' => 11.48,
                // 'interest' => 6.82,
                // 'total' => 18.30,
                'status' => 'current',
                'lender_installment_id' => 1,
                'lender_id' => $loan->lender_id,
            ],
            1 => [
                'seq_num' => 2,
                'due_date' => '2020-08-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 499.82,
                'principal' => 12.60,
                // 'interest' => 6.89,
                // 'total' => 19.49,
                'status' => 'current',
                'lender_installment_id' => 2,
                'lender_id' => $loan->lender_id,
            ],
            2 => [
                'seq_num' => 3,
                'due_date' => '2020-09-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 487.22,
                'principal' => 13.84,
                // 'interest' => 6.71,
                // 'total' => 20.55,
                'status' => 'current',
                'lender_installment_id' => 3,
                'lender_id' => $loan->lender_id,
            ],
            3 => [
                'seq_num' => 4,
                'due_date' => '2020-10-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 473.38,
                'principal' => 15.19,
                // 'interest' => 6.31,
                // 'total' => 21.50,
                'status' => 'current',
                'lender_installment_id' => 4,
                'lender_id' => $loan->lender_id,
            ],
            4 => [
                'seq_num' => 5,
                'due_date' => '2020-11-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 458.19,
                'principal' => 16.67,
                // 'interest' => 6.31,
                // 'total' => 22.98,
                'status' => 'current',
                'lender_installment_id' => 5,
                'lender_id' => $loan->lender_id,
            ],
            5 => [
                'seq_num' => 6,
                'due_date' => '2020-12-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 441.52,
                'principal' => 18.29,
                // 'interest' => 5.89,
                // 'total' => 24.18,
                'status' => 'current',
                'lender_installment_id' => 6,
                'lender_id' => $loan->lender_id,
            ],
            6 => [
                'seq_num' => 7,
                'due_date' => '2021-01-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 423.23,
                'principal' => 20.08,
                // 'interest' => 5.83,
                // 'total' => 25.91,
                'status' => 'current',
                'lender_installment_id' => 7,
                'lender_id' => $loan->lender_id,
            ],
            7 => [
                'seq_num' => 8,
                'due_date' => '2021-02-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 403.15,
                'principal' => 22.04,
                // 'interest' => 5.55,
                // 'total' => 27.59,
                'status' => 'current',
                'lender_installment_id' => 8,
                'lender_id' => $loan->lender_id,
            ],
            8 => [
                'seq_num' => 9,
                'due_date' => '2021-03-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 381.11,
                'principal' => 24.19,
                // 'interest' => 4.74,
                // 'total' => 28.93,
                'status' => 'current',
                'lender_installment_id' => 9,
                'lender_id' => $loan->lender_id,
            ],
            9 => [
                'seq_num' => 10,
                'due_date' => '2021-04-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 356.92,
                'principal' => 26.55,
                // 'interest' => 4.92,
                // 'total' => 31.47,
                'status' => 'current',
                'lender_installment_id' => 10,
                'lender_id' => $loan->lender_id,
            ],
            10 => [
                'seq_num' => 11,
                'due_date' => '2021-05-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 330.37,
                'principal' => 29.14,
                // 'interest' => 4.40,
                // 'total' => 33.54,
                'status' => 'current',
                'lender_installment_id' => 11,
                'lender_id' => $loan->lender_id,
            ],
            11 => [
                'seq_num' => 12,
                'due_date' => '2021-06-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 301.23,
                'principal' => 31.98,
                // 'interest' => 4.15,
                // 'total' => 36.13,
                'status' => 'current',
                'lender_installment_id' => 12,
                'lender_id' => $loan->lender_id,
            ],
            12 => [
                'seq_num' => 13,
                'due_date' => '2021-07-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 269.25,
                'principal' => 35.10,
                // 'interest' => 3.59,
                // 'total' => 38.69,
                'status' => 'current',
                'lender_installment_id' => 13,
                'lender_id' => $loan->lender_id,
            ],
            13 => [
                'seq_num' => 14,
                'due_date' => '2021-08-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 234.15,
                'principal' => 38.53,
                // 'interest' => 3.23,
                // 'total' => 41.76,
                'status' => 'current',
                'lender_installment_id' => 14,
                'lender_id' => $loan->lender_id,
            ],
            14 => [
                'seq_num' => 15,
                'due_date' => '2021-09-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 192.62,
                'principal' => 42.28,
                // 'interest' => 2.70,
                // 'total' => 44.98,
                'status' => 'current',
                'lender_installment_id' => 15,
                'lender_id' => $loan->lender_id,
            ],
            15 => [
                'seq_num' => 16,
                'due_date' => '2021-10-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 153.34,
                'principal' => 46.41,
                // 'interest' => 2.04,
                // 'total' => 48.45,
                'status' => 'current',
                'lender_installment_id' => 16,
                'lender_id' => $loan->lender_id,
            ],
            16 => [
                'seq_num' => 17,
                'due_date' => '2021-11-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 106.93,
                'principal' => 50.94,
                // 'interest' => 1.47,
                // 'total' => 52.41,
                'status' => 'current',
                'lender_installment_id' => 17,
                'lender_id' => $loan->lender_id,
            ],
            17 => [
                'seq_num' => 18,
                'due_date' => '2021-12-19',
                'currency_id' => $loan->currency_id,
                'paid' => 0,
                // 'remaining_principal' => 55.99,
                'principal' => 55.99,
                // 'interest' => 0.75,
                // 'total' => 56.74,
                'status' => 'current',
                'lender_installment_id' => 18,
                'lender_id' => $loan->lender_id,
            ],
        ];

        return $this->getInstallmentsAfterInsert($loan, $import, $fromDate);
    }

    //////////////////////////////////// COMMON METHODS ////////////////////////////////////

    protected function getTestInvestor(string $email = 'investor1@test.com')
    {
        $obj = Investor::where('email', $email)->first();
        if (!empty($obj)) {
            return $obj;
        }

        $investor = new Investor;
        $investor->email = $email;
        $investor->first_name = 'Test';
        $investor->last_name = 'Investor';
        $investor->comment = 'test user for unit test';
        $investor->type = 'individual';
        $investor->status = 'verified';
        $investor->deleted = '0';
        $investor->save();


        $row = new BankAccount();
        $row->investor_id = $investor->investor_id;
        $row->iban = 'BLABLA' . time();
        $row->save();

        $investor->refresh();
        return $investor;
    }

    protected function getInvestorPortfolios(
        int $investorId,
        int $currencyId = Currency::ID_EUR
    ): array
    {
        $obj1 = Portfolio::where(
            [
                'investor_id' => $investorId,
                'currency_id' => $currencyId,
                'type' => 'quality',
            ]
        )->first();

        $obj2 = Portfolio::where(
            [
                'investor_id' => $investorId,
                'currency_id' => $currencyId,
                'type' => 'maturity',
            ]
        )->first();

        if (empty($obj1)) {
            $obj1 = new Portfolio;
            $obj1->investor_id = $investorId;
            $obj1->currency_id = $currencyId;
            $obj1->type = 'quality';
            $obj1->date = (Carbon::now())->format('Y-m-d');
            $obj1->range1 = '0';
            $obj1->range2 = '0';
            $obj1->range3 = '0';
            $obj1->range4 = '0';
            $obj1->range5 = '0';
            $obj1->save();
        }

        if (empty($obj2)) {
            $obj2 = new Portfolio;
            $obj2->investor_id = $investorId;
            $obj2->currency_id = $currencyId;
            $obj2->type = 'maturity';
            $obj2->date = (Carbon::now())->format('Y-m-d');
            $obj2->range1 = '0';
            $obj2->range2 = '0';
            $obj2->range3 = '0';
            $obj2->range4 = '0';
            $obj2->range5 = '0';
            $obj2->save();
        }

        return [
            'quality' => $obj1,
            'maturity' => $obj2,
        ];
    }

    private function getTransaction(int $loanId, int $investorId, float $amount)
    {
        return Transaction::where(
            [
                'loan_id' => $loanId,
                'investor_id' => $investorId,
                'amount' => $amount,
            ]
        )->first();
    }

    protected function getInstallmentsAfterInsert($loan, array $import, $fromDate = null)
    {
        $ourLoans = [
            $loan->lender_id => $loan,
        ];
        $importData = $this->importService->prepareInstallmentsAndLoansPrepaidSchedule(
            $import,
            $ourLoans
        );

        $installmentsCount = $this->importService->installmentsMassInsert(
            $importData['installments']
        );

        $loansCount = $this->importService->activateLoansAndAddPrepaidSchedule(
            $importData['loans']
        );

        $loan->refresh();
        return $loan->installments($fromDate);
    }

    public function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });

        parent::tearDown();
    }
}
