<?php

namespace Tests\Unit\Installment;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Loan;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\ImportService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class InstallmentTest extends TestCase
{
    use WithoutMiddleware;
    use TestDataTrait;

    private $importService;

    public function setUp(): void
    {
        parent::setUp();
        $this->importService = new ImportService;
    }

    public function testInstallmentsWithListingDateAfterLoanIssueDate()
    {
        $loan = new Loan;
        $loan->loan_id = 1;
        $loan->original_amount = 511.30;
        $loan->original_remaining_principal = 511.30;
        $loan->remaining_principal = Calculator::toEuro(511.30);
        $loan->interest_rate_percent = 16;
        $loan->lender_issue_date = '2020-06-19';
        $loan->created_at = '2020-07-30';

        $ourLoans = [
            '1' => $loan,
        ];

        $originalCurrencyId = Currency::ID_BGN; // we keep original currency, it will be changed to EUR in import service
        $installmentsToImport = [
            0 => [
                'lender_installment_id' => 1,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2020-07-19',
                'principal' => 11.48,
                'currency_id' => $originalCurrencyId,
            ],
            1 => [
                'lender_installment_id' => 2,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2020-08-19',
                'principal' => 12.60,
                'currency_id' => $originalCurrencyId,
            ],
            2 => [
                'lender_installment_id' => 3,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2020-09-19',
                'principal' => 13.84,
                'currency_id' => $originalCurrencyId,
            ],
            3 => [
                'lender_installment_id' => 4,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2020-10-19',
                'principal' => 15.19,
                'currency_id' => $originalCurrencyId,
            ],
            4 => [
                'lender_installment_id' => 5,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2020-11-19',
                'principal' => 16.67,
                'currency_id' => $originalCurrencyId,
            ],
            5 => [
                'lender_installment_id' => 6,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2020-12-19',
                'principal' => 18.29,
                'currency_id' => $originalCurrencyId,
            ],
            6 => [
                'lender_installment_id' => 7,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-01-19',
                'principal' => 20.08,
                'currency_id' => $originalCurrencyId,
            ],
            7 => [
                'lender_installment_id' => 8,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-02-19',
                'principal' => 22.04,
                'currency_id' => $originalCurrencyId,
            ],
            8 => [
                'lender_installment_id' => 9,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-03-19',
                'principal' => 24.19,
                'currency_id' => $originalCurrencyId,
            ],
            9 => [
                'lender_installment_id' => 10,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-04-19',
                'principal' => 26.55,
                'currency_id' => $originalCurrencyId,
            ],
            10 => [
                'lender_installment_id' => 11,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-05-19',
                'principal' => 29.14,
                'currency_id' => $originalCurrencyId,
            ],
            11 => [
                'lender_installment_id' => 12,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-06-19',
                'principal' => 31.98,
                'currency_id' => $originalCurrencyId,
            ],
            12 => [
                'lender_installment_id' => 13,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-07-19',
                'principal' => 35.10,
                'currency_id' => $originalCurrencyId,
            ],
            13 => [
                'lender_installment_id' => 14,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-08-19',
                'principal' => 38.53,
                'currency_id' => $originalCurrencyId,
            ],
            14 => [
                'lender_installment_id' => 15,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-09-19',
                'principal' => 42.28,
                'currency_id' => $originalCurrencyId,
            ],
            15 => [
                'lender_installment_id' => 16,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-10-19',
                'principal' => 46.41,
                'currency_id' => $originalCurrencyId,
            ],
            16 => [
                'lender_installment_id' => 17,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-11-19',
                'principal' => 50.94,
                'currency_id' => $originalCurrencyId,
            ],
            17 => [
                'lender_installment_id' => 18,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-12-19',
                'principal' => 55.99,
                'currency_id' => $originalCurrencyId,
            ],
        ];

        // confirm consistency of data
        $this->assertEquals($installmentsToImport[0]['due_date'], "2020-07-19");
        $this->assertEquals($installmentsToImport[0]['principal'], 11.48);
        $this->assertEquals($installmentsToImport[1]['due_date'], "2020-08-19");
        $this->assertEquals($installmentsToImport[1]['principal'], 12.60);
        $this->assertEquals($installmentsToImport[2]['due_date'], "2020-09-19");
        $this->assertEquals($installmentsToImport[2]['principal'], 13.84);
        $this->assertEquals($installmentsToImport[17]['due_date'], "2021-12-19");
        $this->assertEquals($installmentsToImport[17]['principal'], 55.99);

        $importData = $this->importService->prepareInstallmentsAndLoansPrepaidSchedule(
            $installmentsToImport,
            $ourLoans
        );
        $import = $importData['installments'];

        $this->assertNotEmpty($import);

        // checks on 1st installment
        $this->assertEquals($import[0]['seq_num'], 1);
        $this->assertEquals($import[0]['due_date'], "2020-07-19");
        $this->assertEquals($import[0]['original_remaining_principal'], 511.30);
        $this->assertEquals($import[0]['remaining_principal'], Calculator::toEuro(511.30));
        $this->assertEquals($import[0]['original_principal'], 11.48);
        $this->assertEquals($import[0]['principal'], Calculator::toEuro(11.48));
        $this->assertEquals($import[0]['interest'], 0);
        $this->assertEquals($import[0]['late_interest'], 0);
        $this->assertEquals($import[0]['total'], Calculator::toEuro(11.48));

        // checks on 2nd installment
        $this->assertEquals($import[1]['seq_num'], 2);
        $this->assertEquals($import[1]['due_date'], "2020-08-19");
        $this->assertEquals($import[1]['original_remaining_principal'], 499.82);
        $this->assertEquals($import[1]['remaining_principal'], Calculator::toEuro(499.82));
        $this->assertEquals($import[1]['principal'], Calculator::toEuro(12.60));
        $this->assertEquals($import[1]['interest'], Calculator::toEuro(4.44));
        $this->assertEquals($import[1]['late_interest'], 0);
        $this->assertEquals($import[1]['total'], Calculator::toEuro(17.04));

        // checks on 3rd installment
        $this->assertEquals($import[2]['seq_num'], 3);
        $this->assertEquals($import[2]['due_date'], "2020-09-19");
        $this->assertEquals($import[2]['remaining_principal'], Calculator::toEuro(487.22));
        $this->assertEquals($import[2]['principal'], Calculator::toEuro(13.84));
        $this->assertEquals($import[2]['interest'], Calculator::toEuro(6.71));
        $this->assertEquals($import[2]['late_interest'], 0);
        $this->assertEquals($import[2]['total'], Calculator::toEuro(20.55));

        // checks on 10th installment
        $this->assertEquals($import[9]['seq_num'], 10);
        $this->assertEquals($import[9]['due_date'], "2021-04-19");
        $this->assertEquals($import[9]['remaining_principal'], 182.48);
        $this->assertEquals($import[9]['principal'], Calculator::toEuro(26.55));
        $this->assertEquals($import[9]['interest'], 2.51);
        $this->assertEquals($import[9]['late_interest'], 0);
        $this->assertEquals($import[9]['total'], 16.08);

        // checks on 18th installment
        $this->assertEquals($import[17]['seq_num'], 18);
        $this->assertEquals($import[17]['due_date'], "2021-12-19");
        $this->assertEquals($import[17]['remaining_principal'], 28.61);
        $this->assertEquals($import[17]['principal'], Calculator::toEuro(55.99));
        $this->assertEquals($import[17]['interest'], Calculator::toEuro(0.75));
        $this->assertEquals($import[17]['late_interest'], 0);
        $this->assertEquals($import[17]['total'], Calculator::toEuro(56.74));

        $this->removeTestData(null, $loan);
    }

    public function testInstallmentsWithListingDateEqualToLoanIssueDate()
    {
        $loan = new Loan;
        $loan->loan_id = 1;
        $loan->original_amount = 1222.46;
        $loan->original_remaining_principal = 1222.46;
        $loan->remaining_principal = Calculator::toEuro(1222.46);
        $loan->interest_rate_percent = 14.50;
        $loan->lender_issue_date = '2020-11-02';
        $loan->created_at = '2020-11-02';

        $ourLoans = [
            '1' => $loan,
        ];

        $originalCurrencyId = Currency::ID_BGN;
        $installmentsToImport = [
            0 => [
                'lender_installment_id' => 1,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2020-12-03',
                'principal' => 60.56,
                'currency_id' => $originalCurrencyId,
            ],
            1 => [
                'lender_installment_id' => 2,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-01-03',
                'principal' => 61.37,
                'currency_id' => $originalCurrencyId,
            ],
            2 => [
                'lender_installment_id' => 3,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-02-03',
                'principal' => 62.19,
                'currency_id' => $originalCurrencyId,
            ],
            3 => [
                'lender_installment_id' => 4,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-03-03',
                'principal' => 63.01,
                'currency_id' => $originalCurrencyId,
            ],
            4 => [
                'lender_installment_id' => 5,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-04-03',
                'principal' => 63.86,
                'currency_id' => $originalCurrencyId,
            ],
            5 => [
                'lender_installment_id' => 6,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-05-03',
                'principal' => 64.71,
                'currency_id' => $originalCurrencyId,
            ],
            6 => [
                'lender_installment_id' => 7,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-06-03',
                'principal' => 65.57,
                'currency_id' => $originalCurrencyId,
            ],
            7 => [
                'lender_installment_id' => 8,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-07-03',
                'principal' => 66.44,
                'currency_id' => $originalCurrencyId,
            ],
            8 => [
                'lender_installment_id' => 9,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-08-03',
                'principal' => 67.33,
                'currency_id' => $originalCurrencyId,
            ],
            9 => [
                'lender_installment_id' => 10,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-09-03',
                'principal' => 68.22,
                'currency_id' => $originalCurrencyId,
            ],
            10 => [
                'lender_installment_id' => 11,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-10-03',
                'principal' => 69.14,
                'currency_id' => $originalCurrencyId,
            ],
            11 => [
                'lender_installment_id' => 12,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-11-03',
                'principal' => 70.06,
                'currency_id' => $originalCurrencyId,
            ],
            12 => [
                'lender_installment_id' => 13,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2021-12-03',
                'principal' => 70.99,
                'currency_id' => $originalCurrencyId,
            ],
            13 => [
                'lender_installment_id' => 14,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2022-01-03',
                'principal' => 71.94,
                'currency_id' => $originalCurrencyId,
            ],
            14 => [
                'lender_installment_id' => 15,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2022-02-03',
                'principal' => 72.89,
                'currency_id' => $originalCurrencyId,
            ],
            15 => [
                'lender_installment_id' => 16,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2022-03-03',
                'principal' => 73.86,
                'currency_id' => $originalCurrencyId,
            ],
            16 => [
                'lender_installment_id' => 17,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2022-04-03',
                'principal' => 74.86,
                'currency_id' => $originalCurrencyId,
            ],
            17 => [
                'lender_installment_id' => 18,
                'lender_id' => 1,
                'paid' => 0,
                'due_date' => '2022-05-03',
                'principal' => 75.46,
                'currency_id' => $originalCurrencyId,
            ],
        ];

        // confirm consistency of data
        $this->assertEquals($installmentsToImport[0]['due_date'], "2020-12-03");
        $this->assertEquals($installmentsToImport[0]['principal'], 60.56);
        $this->assertEquals($installmentsToImport[1]['due_date'], "2021-01-03");
        $this->assertEquals($installmentsToImport[1]['principal'], 61.37);
        $this->assertEquals($installmentsToImport[9]['due_date'], "2021-09-03");
        $this->assertEquals($installmentsToImport[9]['principal'], 68.22);
        $this->assertEquals($installmentsToImport[17]['due_date'], "2022-05-03");
        $this->assertEquals($installmentsToImport[17]['principal'], 75.46);

        $importData = $this->importService->prepareInstallmentsAndLoansPrepaidSchedule(
            $installmentsToImport,
            $ourLoans
        );
        $import = $importData['installments'];

        $this->assertNotEmpty($import);

        // checks on 1st installment
        $this->assertEquals($import[0]['seq_num'], 1);
        $this->assertEquals($import[0]['due_date'], "2020-12-03");
        $this->assertEquals($import[0]['remaining_principal'], Calculator::toEuro(1222.46));
        $this->assertEquals($import[0]['principal'], Calculator::toEuro(60.56));
        $this->assertEquals($import[0]['interest'], Calculator::toEuro(15.26));
        $this->assertEquals($import[0]['late_interest'], 0);
        $this->assertEquals($import[0]['total'], 38.76);

        // checks on 2nd installment
        $this->assertEquals($import[1]['seq_num'], 2);
        $this->assertEquals($import[1]['due_date'], "2021-01-03");
        $this->assertEquals($import[1]['remaining_principal'], Calculator::toEuro(1161.90));
        $this->assertEquals($import[1]['principal'],Calculator::toEuro(61.37));
        $this->assertEquals($import[1]['interest'], Calculator::toEuro(14.51));
        $this->assertEquals($import[1]['late_interest'], 0);
        $this->assertEquals($import[1]['total'], Calculator::toEuro(75.88));

        // checks on 10th installment
        $this->assertEquals($import[9]['seq_num'], 10);
        $this->assertEquals($import[9]['due_date'], "2021-09-03");
        $this->assertEquals($import[9]['remaining_principal'], 331.0);
        $this->assertEquals($import[9]['principal'], Calculator::toEuro(68.22));
        $this->assertEquals($import[9]['interest'],  Calculator::toEuro(8.08));
        $this->assertEquals($import[9]['late_interest'], 0);
        $this->assertEquals($import[9]['total'], Calculator::toEuro(76.30));

        // checks on 18th installment
        $this->assertEquals($import[17]['seq_num'], 18);
        $this->assertEquals($import[17]['due_date'], "2022-05-03");
        $this->assertEquals($import[17]['remaining_principal'], 38.56);
        $this->assertEquals($import[17]['principal'], Calculator::toEuro(75.46));
        $this->assertEquals($import[17]['interest'],  Calculator::toEuro(0.91));
        $this->assertEquals($import[17]['late_interest'], 0);
        $this->assertEquals($import[17]['total'], Calculator::toEuro(76.37));

        $this->removeTestData(null, $loan);
    }

    public function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });

        parent::tearDown();
    }
}
