<?php

namespace Tests\Unit\Import;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Loan;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\ImportService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class ImportFlowTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    public function setUp(): void
    {
        $this->importService = new ImportService;
        parent::setUp();
    }

    public function testLoanWithUnpaidInstallment()
    {
        // prepare test data
        $amountEur = 153.39;
        $originalCurrencyId = Currency::ID_BGN;
        $currencyId = Currency::ID_EUR;
        $loanAmount = Calculator::toBgn($amountEur);
        $remainingPricipal = Calculator::toBgn($amountEur);
        $interestRate = 16;
        $issueDate = '2020-11-16';
        $listingDate = '2020-12-17';
        $finalPaymentDate = '2021-02-16';

        $loan = $this->preapreLoan(
            $loanAmount,
            $loanAmount,
            $remainingPricipal,
            $interestRate,
            10, // originator percent
            3, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            $finalPaymentDate
        );

        $import = [
            0 => [
                'seq_num' => 1,
                'due_date' => '2020-12-16',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                'principal' => Calculator::toBgn(50.00),
                'status' => 'current',
                'lender_installment_id' => 1,
                'lender_id' => $loan->lender_id,
            ],
            1 => [
                'seq_num' => 2,
                'due_date' => '2021-01-16',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                'principal' => Calculator::toBgn(51.09),
                'status' => 'current',
                'lender_installment_id' => 2,
                'lender_id' => $loan->lender_id,
            ],
            2 => [
                'seq_num' => 3,
                'due_date' => '2021-02-16',
                'currency_id' => $originalCurrencyId,
                'paid' => 0,
                'principal' => Calculator::toBgn(52.30),
                'status' => 'current',
                'lender_installment_id' => 3,
                'lender_id' => $loan->lender_id,
            ],
        ];

        $installments = $this->getInstallmentsAfterInsert($loan, $import);


        $this->assertEquals(Carbon::parse($loan->lender_issue_date)->format('Y-m-d'), $issueDate);
        $this->assertEquals(Carbon::parse($loan->final_payment_date)->format('Y-m-d'), $finalPaymentDate);
        $this->assertEquals(Carbon::parse($loan->created_at)->format('Y-m-d'), $listingDate);

        $this->assertEquals($loan->original_currency_id, $originalCurrencyId);
        $this->assertEquals($loan->original_amount, 300);
        $this->assertEquals($loan->original_amount_available, 270);
        $this->assertEquals($loan->original_remaining_principal, 300);

        $this->assertEquals($loan->currency_id, $currencyId);
        $this->assertEquals($loan->amount, $amountEur);
        $this->assertEquals($loan->amount_available, Calculator::round($amountEur - ($amountEur/10)));
        $this->assertEquals($loan->remaining_principal, $amountEur);
        $this->assertEquals($loan->period, 3);
        $this->assertEquals($loan->prepaid_schedule_payments, 0);
        $this->assertEquals($loan->status, 'active');



        \Artisan::call('script:daily-payment-status-refresh ' . $listingDate . ' ' . $loan->loan_id);


        $loan->refresh();
        $installments = $loan->installments();

        $this->assertEquals($loan->status, 'active');
        $this->assertEquals($loan->overdue_days, 1);
        $this->assertEquals($loan->payment_status, '1-15 days');
        $this->assertEquals($installments[0]->status, '1-15 days');
        $this->assertEquals($installments[1]->status, Loan::PAY_STATUS_CURRENT);
        $this->assertEquals($installments[2]->status, Loan::PAY_STATUS_CURRENT);

        $this->assertEquals($installments[0]->original_interest, 0);
        $this->assertEquals($installments[1]->original_interest, 2.70);
        $this->assertEquals($installments[2]->original_interest, 1.41);

        $this->assertEquals($installments[0]->interest, 0);
        $this->assertEquals($installments[1]->interest, 1.38);
        $this->assertEquals($installments[2]->interest, 0.72);


        $this->removeTestData(null, $loan);
    }

    public function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });

        parent::tearDown();
    }

    public function getInstallmentsAfterInsert($loan, array $import, $fromDate = null)
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
}
