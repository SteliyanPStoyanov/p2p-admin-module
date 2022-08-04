<?php

namespace Tests\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\InvestorQualityRange;
use Modules\Common\Entities\InvestorQualityRangeHistory;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\RepaidInstallment;
use Modules\Common\Entities\RepaidLoan;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Libraries\Calculator\Calculator;

trait TestDataTrait
{
    public function preapreLoan(
        float $amount = 511.30,
        float $amountAfranga = 499.82,
        float $amountRemainingPrincipal = 499.82,
        $interestRate = 16,
        $originatorPercent = 10,
        $period = 18,
        $currencyId = Currency::ID_EUR,
        $issueDate = '2020-06-19',
        $listingDate = '2020-06-19',
        $finalPaymentDate = '2021-12-19'
    ) {
        $loan = new Loan;
        $loan->lender_id = rand(100001, 999999);
        $loan->country_id = 1;
        $loan->originator_id = 1;
        $loan->period = $period;
        $loan->buyback = 1;
        $loan->blocked = 0;
        $loan->unlisted = 0;
        $loan->from_office = 0;
        $loan->type = 'installments';
        $loan->status = 'active';
        $loan->payment_status = Loan::PAY_STATUS_CURRENT;
        $loan->lender_issue_date = $issueDate;
        $loan->final_payment_date = $finalPaymentDate;
        $loan->created_at = $listingDate . ' 09:00:00';
        $loan->prepaid_schedule_payments = 0;
        $loan->borrower_age = '31';
        $loan->borrower_gender = 'male';

        $loan->original_currency_id = Currency::ID_BGN;
        $loan->original_amount = $amount;
        $loan->original_amount_afranga = $amountAfranga;
        $loan->original_remaining_principal = $amountAfranga;
        $loan->original_amount_available = Calculator::getAvailableAmount($amountAfranga, $originatorPercent);

        $loan->currency_id = $currencyId;
        $loan->amount = Calculator::toEuro($amount);
        $loan->amount_afranga = Calculator::toEuro($amountAfranga);
        $loan->remaining_principal = Calculator::toEuro($amountRemainingPrincipal);
        $loan->amount_available = Calculator::getAvailableAmount(
            Calculator::toEuro($amountAfranga),
            $originatorPercent
        );

        $loan->interest_rate_percent = $interestRate;
        $loan->contract_tempate_id = 1;
        $loan->assigned_origination_fee_share = $originatorPercent;
        $loan->save();

        return $loan;
    }

    public function prepareInstallments(
        Loan $loan,
        int $count = 0,
        array $paidInstallmetsSeqNums = [],
        int $currencyId = Currency::ID_BGN
    ) {
        $ourLoans = [
            $loan->lender_id => $loan,
        ];
        $installmentsToImportAll = [
            0 => [
                'lender_installment_id' => $loan->loan_id . 1,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2020-07-19',
                'principal' => 11.48,
                'currency_id' => $currencyId,
            ],
            1 => [
                'lender_installment_id' => $loan->loan_id . 2,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2020-08-19',
                'principal' => 12.60,
                'currency_id' => $currencyId,
            ],
            2 => [
                'lender_installment_id' => $loan->loan_id . 3,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2020-09-19',
                'principal' => 13.84,
                'currency_id' => $currencyId,
            ],
            3 => [
                'lender_installment_id' => $loan->loan_id . 4,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2020-10-19',
                'principal' => 15.19,
                'currency_id' => $currencyId,
            ],
            4 => [
                'lender_installment_id' => $loan->loan_id . 5,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2020-11-19',
                'principal' => 16.67,
                'currency_id' => $currencyId,
            ],
            5 => [
                'lender_installment_id' => $loan->loan_id . 6,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2020-12-19',
                'principal' => 18.29,
                'currency_id' => $currencyId,
            ],
            6 => [
                'lender_installment_id' => $loan->loan_id . 7,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-01-19',
                'principal' => 20.08,
                'currency_id' => $currencyId,
            ],
            7 => [
                'lender_installment_id' => $loan->loan_id . 8,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-02-19',
                'principal' => 22.04,
                'currency_id' => $currencyId,
            ],
            8 => [
                'lender_installment_id' => $loan->loan_id . 9,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-03-19',
                'principal' => 24.19,
                'currency_id' => $currencyId,
            ],
            9 => [
                'lender_installment_id' => $loan->loan_id . 10,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-04-19',
                'principal' => 26.55,
                'currency_id' => $currencyId,
            ],
            10 => [
                'lender_installment_id' => $loan->loan_id . 11,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-05-19',
                'principal' => 29.14,
                'currency_id' => $currencyId,
            ],
            11 => [
                'lender_installment_id' => $loan->loan_id . 12,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-06-19',
                'principal' => 31.98,
                'currency_id' => $currencyId,
            ],
            12 => [
                'lender_installment_id' => $loan->loan_id . 13,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-07-19',
                'principal' => 35.10,
                'currency_id' => $currencyId,
            ],
            13 => [
                'lender_installment_id' => $loan->loan_id . 14,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-08-19',
                'principal' => 38.53,
                'currency_id' => $currencyId,
            ],
            14 => [
                'lender_installment_id' => $loan->loan_id . 15,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-09-19',
                'principal' => 42.28,
                'currency_id' => $currencyId,
            ],
            15 => [
                'lender_installment_id' => $loan->loan_id . 16,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-10-19',
                'principal' => 46.41,
                'currency_id' => $currencyId,
            ],
            16 => [
                'lender_installment_id' => $loan->loan_id . 17,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-11-19',
                'principal' => 50.94,
                'currency_id' => $currencyId,
            ],
            17 => [
                'lender_installment_id' => $loan->loan_id . 18,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => '2021-12-19',
                'principal' => 55.99,
                'currency_id' => $currencyId,
            ],
        ];

        $installmentsToImport = [];
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $installmentsToImport[$i] = $installmentsToImportAll[$i];
            }
        } else {
            $installmentsToImport = $installmentsToImportAll;
        }

        if (!empty($paidInstallmetsSeqNums)) {
            foreach ($paidInstallmetsSeqNums as $key) {
                if (isset($installmentsToImport[$key])) {
                    $installmentsToImport[$key]['paid'] = 1;
                }
            }
        }

        $importData = $this->importService->prepareInstallmentsAndLoansPrepaidSchedule(
            $installmentsToImport,
            $ourLoans
        );


        $installmentsCount = $this->importService->installmentsMassInsert(
            $importData['installments']
        );

        return $installmentsCount;
    }

    public function prepareInstallmentsWithStartDate(
        Loan $loan,
        string $startDate,
        int $count = 0,
        array $paidInstallmetsSeqNums = [],
        int $currencyId = Currency::ID_BGN
    ) {
        $ourLoans = [
            $loan->lender_id => $loan,
        ];
        $installmentsToImportAll = [
            0 => [
                'lender_installment_id' => $loan->loan_id . 1,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => $startDate,
                'principal' => 11.48,
                'currency_id' => $currencyId,
            ],
            1 => [
                'lender_installment_id' => $loan->loan_id . 2,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(1),
                'principal' => 12.60,
                'currency_id' => $currencyId,
            ],
            2 => [
                'lender_installment_id' => $loan->loan_id . 3,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(2),
                'principal' => 13.84,
                'currency_id' => $currencyId,
            ],
            3 => [
                'lender_installment_id' => $loan->loan_id . 4,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(3),
                'principal' => 15.19,
                'currency_id' => $currencyId,
            ],
            4 => [
                'lender_installment_id' => $loan->loan_id . 5,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(4),
                'principal' => 16.67,
                'currency_id' => $currencyId,
            ],
            5 => [
                'lender_installment_id' => $loan->loan_id . 6,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(5),
                'principal' => 18.29,
                'currency_id' => $currencyId,
            ],
            6 => [
                'lender_installment_id' => $loan->loan_id . 7,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(6),
                'principal' => 20.08,
                'currency_id' => $currencyId,
            ],
            7 => [
                'lender_installment_id' => $loan->loan_id . 8,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(7),
                'principal' => 22.04,
                'currency_id' => $currencyId,
            ],
            8 => [
                'lender_installment_id' => $loan->loan_id . 9,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(8),
                'principal' => 24.19,
                'currency_id' => $currencyId,
            ],
            9 => [
                'lender_installment_id' => $loan->loan_id . 10,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(9),
                'principal' => 26.55,
                'currency_id' => $currencyId,
            ],
            10 => [
                'lender_installment_id' => $loan->loan_id . 11,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(10),
                'principal' => 29.14,
                'currency_id' => $currencyId,
            ],
            11 => [
                'lender_installment_id' => $loan->loan_id . 12,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(11),
                'principal' => 31.98,
                'currency_id' => $currencyId,
            ],
            12 => [
                'lender_installment_id' => $loan->loan_id . 13,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(12),
                'principal' => 35.10,
                'currency_id' => $currencyId,
            ],
            13 => [
                'lender_installment_id' => $loan->loan_id . 14,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(13),
                'principal' => 38.53,
                'currency_id' => $currencyId,
            ],
            14 => [
                'lender_installment_id' => $loan->loan_id . 15,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(14),
                'principal' => 42.28,
                'currency_id' => $currencyId,
            ],
            15 => [
                'lender_installment_id' => $loan->loan_id . 16,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(15),
                'principal' => 46.41,
                'currency_id' => $currencyId,
            ],
            16 => [
                'lender_installment_id' => $loan->loan_id . 17,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(16),
                'principal' => 50.94,
                'currency_id' => $currencyId,
            ],
            17 => [
                'lender_installment_id' => $loan->loan_id . 18,
                'lender_id' => $loan->lender_id,
                'paid' => 0,
                'due_date' => Carbon::parse($startDate)->addMonths(17),
                'principal' => 55.99,
                'currency_id' => $currencyId,
            ],
        ];

        $installmentsToImport = [];
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $installmentsToImport[$i] = $installmentsToImportAll[$i];
            }
        } else {
            $installmentsToImport = $installmentsToImportAll;
        }

        if (!empty($paidInstallmetsSeqNums)) {
            foreach ($paidInstallmetsSeqNums as $key) {
                if (isset($installmentsToImport[$key])) {
                    $installmentsToImport[$key]['paid'] = 1;
                }
            }
        }

        $importData = $this->importService->prepareInstallmentsAndLoansPrepaidSchedule(
            $installmentsToImport,
            $ourLoans
        );


        $installmentsCount = $this->importService->installmentsMassInsert(
            $importData['installments']
        );

        return $installmentsCount;
    }

    public function prepareInvestor(
        $email = 'investor@distributeInstallment.test'
    ) {
        $investor = new Investor;
        $investor->email = $email;
        $investor->first_name = 'Distribute';
        $investor->last_name = 'Installment';
        $investor->comment = 'test user for unit test';
        $investor->type = 'individual';
        $investor->status = 'verified';
        $investor->deleted = '0';
        $investor->save();
        $investor->refresh();

        $row = new BankAccount();
        $row->investor_id = $investor->investor_id;
        $row->iban = 'BLABLA' . time();
        $row->save();

        return $investor;
    }

    public function getInvestor($email = 'investor@distributeInstallment.test')
    {
        return Investor::where('email', '=', $email)->first();
    }

    public function prepareWallet(
        Investor $investor,
        float $deposit = 1000,
        $currencyId = Currency::ID_EUR
    ) {
        $wallet = new Wallet;
        $wallet->investor_id = $investor->investor_id;
        $wallet->currency_id = $currencyId;
        $wallet->total_amount = $deposit;
        $wallet->invested = '0';
        $wallet->uninvested = $deposit;
        $wallet->deposit = $deposit;
        $wallet->withdraw = '0';
        $wallet->income = '0';
        $wallet->interest = '0';
        $wallet->late_interest = '0';
        $wallet->bonus = '0';
        $wallet->save();

        $wallet->refresh();
        return $wallet;
    }

    public function preparePortfolios(
        Investor $investor,
        $currencyId = Currency::ID_EUR
    ): array {
        $obj1 = new Portfolio;
        $obj1->investor_id = $investor->investor_id;
        $obj1->currency_id = $currencyId;
        $obj1->type = 'quality';
        $obj1->date = (Carbon::now())->format('Y-m-d');
        $obj1->range1 = '0';
        $obj1->range2 = '0';
        $obj1->range3 = '0';
        $obj1->range4 = '0';
        $obj1->range5 = '0';
        $obj1->save();

        $obj2 = new Portfolio;
        $obj2->investor_id = $investor->investor_id;
        $obj2->currency_id = $currencyId;
        $obj2->type = 'maturity';
        $obj2->date = (Carbon::now())->format('Y-m-d');
        $obj2->range1 = '0';
        $obj2->range2 = '0';
        $obj2->range3 = '0';
        $obj2->range4 = '0';
        $obj2->range5 = '0';
        $obj2->save();

        return [
            'quality' => $obj1,
            'maturity' => $obj2,
        ];
    }

    public function getInvestment(int $investorId, int $loanId, float $amount)
    {
        return Investment::where(
            [
                'investor_id' => $investorId,
                'loan_id' => $loanId,
                'amount' => $amount,
            ]
        )->first();
    }

    public function emulateRepaidInstallment(Loan $loan)
    {
        $obj = new RepaidInstallment();
        $obj->lender_id = $loan->lender_id;
        $obj->lender_installment_id = $loan->getFirstUnpaidInstallment()->lender_installment_id;
        $obj->handled = 0;
        $obj->save();

        return $obj;
    }

    public function emulateRepaidLoan(Loan $loan, string $type = RepaidLoan::TYPE_NORMAL)
    {
        $obj = new RepaidLoan();
        $obj->lender_id = $loan->lender_id;
        $obj->repayment_type = $type;
        $obj->handled = 0;
        $obj->save();

        return $obj;
    }

    public function getTransaction(int $loanId, int $investorId)
    {
        return Transaction::where(
            [
                'loan_id' => $loanId,
                'investor_id' => $investorId
            ]
        )->orderBy('transaction_id', 'DESC')->first();
    }

    public function getInverstorInstallment(int $loanId, int $installmentId)
    {
        return InvestorInstallment::where(
            [
                'loan_id' => $loanId,
                'installment_id' => $installmentId,
            ]
        )->first();
    }

    public function getInvestorQualityRage(int $investorId, int $loanId)
    {
        return InvestorQualityRange::where(
            [
                'investor_id' => $investorId,
                'loan_id' => $loanId,
            ]
        )->get();
    }

    public function getInvestorQualityRageHistory(
        int $investorId,
        int $loanId,
        int $range
    ) {
        return InvestorQualityRangeHistory::where(
            [
                'investor_id' => $investorId,
                'loan_id' => $loanId,
                'range' => $range,
            ]
        )->orderBy('history_id', 'DESC')->first();
    }

    protected function getInvestorWallet(
        int $investorId,
        int $money = 1000,
        int $currencyId = Currency::ID_EUR
    ) {
        $obj = Wallet::where(
            [
                'investor_id' => $investorId,
                'currency_id' => $currencyId,
            ]
        )->first();

        if (!empty($obj)) {
            return $obj;
        }

        $wallet = new Wallet;
        $wallet->investor_id = $investorId;
        $wallet->currency_id = $currencyId;
        $wallet->total_amount = $money;
        $wallet->invested = '0';
        $wallet->uninvested = $money;
        $wallet->deposit = $money;
        $wallet->withdraw = '0';
        $wallet->income = '0';
        $wallet->interest = '0';
        $wallet->late_interest = '0';
        $wallet->bonus = '0';
        $wallet->save();

        $wallet->refresh();
        return $wallet;
    }

    public function removeTestData(Investor $investor = null, Loan $loan = null)
    {
        if (!empty($investor->investor_id)) {
            // DB::select('ALTER TABLE investor DISABLE TRIGGER ALL');
            // DB::select('delete from investor where investor_id = ' . $investor->investor_id);
            // DB::select('ALTER TABLE investor ENABLE TRIGGER ALL');

            DB::table('bank_account')->where('investor_id', $investor->investor_id)->delete();
            DB::table('investor_installment')->where('investor_id', $investor->investor_id)->delete();
            DB::table('wallet')->where('investor_id', $investor->investor_id)->delete();
            DB::table('portfolio')->where('investor_id', $investor->investor_id)->delete();
            DB::table('investment')->where('investor_id', $investor->investor_id)->delete();
            DB::table('investor')->where('investor_id', $investor->investor_id)->delete();
            DB::table('investor_quality_range')->where('investor_id', $investor->investor_id)->delete();
            DB::table('investor_bonus')->where('investor_id', $investor->investor_id)->delete();
            DB::table('task')->where('investor_id', $investor->investor_id)->delete();
        }

        if (!empty($loan->loan_id)) {
            DB::table('repaid_installment')->where('lender_id', $loan->lender_id)->delete();
            DB::table('installment')->where('loan_id', $loan->loan_id)->delete();
            DB::table('transaction')->where('loan_id', $loan->loan_id)->delete();
            DB::table('loan')->where('loan_id', $loan->loan_id)->delete();
        }
    }
}
