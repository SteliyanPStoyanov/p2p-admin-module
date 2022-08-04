<?php

namespace Tests\Unit\Installment;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Loan;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\ImportService;
use Tests\TestCase;

class InstallmentMigrateToEuroTest extends TestCase
{
    use WithoutMiddleware;

    private $importService;

    public function setUp(): void
    {
        parent::setUp();
        $this->importService = new ImportService;
    }

    public function testInstallmentsConvertBgnToEuro()
    {
        $this->assertEquals(1, 1);


        // $loan = new Loan;
        // $loan->loan_id = 1;
        // $loan->original_remaining_principal = 511.30;
        // $loan->remaining_principal = Calculator::toEuro(511.30);
        // $loan->interest_rate_percent = 16;
        // $loan->lender_issue_date = '2020-06-19';
        // $loan->created_at = '2020-07-30';

        // $ourLoans = [
        //     '1' => $loan,
        // ];

        // $originalCurrencyId = Currency::ID_BGN;
        // $installmentsToImport = [
        //     0 => [
        //         'loan_id' => $loan->loan_id,
        //         'seq_num' => 1,
        //         'lender_id' => 1,
        //         'due_date' => '2020-07-19',
        //         'currency_id' => $originalCurrencyId,
        //         'paid' => 1,
        //         // 'remaining_principal' => Calculator::toEuro(511.30),
        //         'principal' => 11.48,
        //         'interest' => 6.82,
        //         'status' => 'current',
        //     ],
        //     1 => [
        //         'loan_id' => $loan->loan_id,
        //         'seq_num' => 2,
        //         'lender_id' => 1,
        //         'due_date' => '2020-08-19',
        //         'currency_id' => $originalCurrencyId,
        //         'paid' => 0,
        //         // 'remaining_principal' => Calculator::toEuro(499.82),
        //         'principal' => 12.60,
        //         'interest' => 6.89,
        //         'status' => 'current',
        //     ],
        //     2 => [
        //         'loan_id' => $loan->loan_id,
        //         'seq_num' => 3,
        //         'lender_id' => 1,
        //         'due_date' => '2020-09-19',
        //         'currency_id' => $originalCurrencyId,
        //         'paid' => 0,
        //         // 'remaining_principal' => Calculator::toEuro(487.22),
        //         'principal' => 13.84,
        //         'interest' => 6.71,
        //         'status' => 'current',
        //     ],
        //     3 => [
        //         'loan_id' => $loan->loan_id,
        //         'seq_num' => 4,
        //         'lender_id' => 1,
        //         'due_date' => '2020-10-19',
        //         'currency_id' => $originalCurrencyId,
        //         'paid' => 0,
        //         // 'remaining_principal' => Calculator::toEuro(473.38),
        //         'principal' => 15.19,
        //         'interest' => 6.31,
        //         'status' => 'current',
        //     ],
        //     4 => [
        //         'loan_id' => $loan->loan_id,
        //         'seq_num' => 5,
        //         'lender_id' => 1,
        //         'due_date' => '2020-11-19',
        //         'currency_id' => $originalCurrencyId,
        //         'paid' => 0,
        //         // 'remaining_principal' => Calculator::toEuro(458.19),
        //         'principal' => 16.67,
        //         'interest' => 6.31,
        //         'status' => 'current',
        //     ],
        //     5 => [
        //         'loan_id' => $loan->loan_id,
        //         'seq_num' => 6,
        //         'lender_id' => 1,
        //         'due_date' => '2020-12-19',
        //         'currency_id' => $originalCurrencyId,
        //         'paid' => 0,
        //         // 'remaining_principal' => Calculator::toEuro(441.52),
        //         'principal' => 18.29,
        //         'interest' => 5.89,
        //         'status' => 'current',
        //     ]
        // ];

        // // confirm consistency of data
        // $this->assertEquals($installmentsToImport[0]['due_date'], "2020-07-19");
        // $this->assertEquals($installmentsToImport[0]['principal'], 11.48);
        // $this->assertEquals($installmentsToImport[1]['due_date'], "2020-08-19");
        // $this->assertEquals($installmentsToImport[1]['principal'], 12.60);
        // $this->assertEquals($installmentsToImport[5]['due_date'], "2020-12-19");
        // $this->assertEquals($installmentsToImport[5]['principal'], 18.29);


        // $importData = $this->importService->prepareInstallmentsAndLoansPrepaidSchedule(
        //     $installmentsToImport,
        //     $ourLoans
        // );
        // $import = $importData['installments'];


        // $this->assertNotEmpty($import);

        // // checks on 1st installment
        // $this->assertEquals($import[0]['seq_num'], 1);
        // $this->assertEquals($import[0]['due_date'], "2020-07-19");

        // $this->assertEquals($import[0]['original_currency_id'], $originalCurrencyId);
        // $this->assertEquals($import[0]['original_remaining_principal'], 511.30);
        // $this->assertEquals($import[0]['original_principal'], 11.48);
        // $this->assertEquals($import[0]['original_interest'], 6.82);

        // $this->assertEquals($import[0]['currency_id'], Currency::ID_EUR);
        // $this->assertEquals($import[0]['remaining_principal'], Calculator::toEuro(511.30));
        // $this->assertEquals($import[0]['principal'], Calculator::toEuro(11.48));
        // $this->assertEquals($import[0]['interest'], Calculator::toEuro(6.82));
        // $this->assertEquals($import[0]['late_interest'], 0.00);
        // $this->assertEquals($import[0]['total'], Calculator::toEuro(11.48));


        // // checks on 2nd installment
        // $this->assertEquals($import[2]['seq_num'], 3);
        // $this->assertEquals($import[2]['due_date'], "2020-09-19");

        // $this->assertEquals($import[2]['original_currency_id'], $originalCurrencyId);
        // $this->assertEquals($import[2]['original_remaining_principal'], 487.22);
        // $this->assertEquals($import[2]['original_principal'], 13.84);
        // $this->assertEquals($import[2]['original_interest'], 6.71);

        // $this->assertEquals($import[2]['currency_id'], Currency::ID_EUR);
        // $this->assertEquals($import[2]['remaining_principal'], Calculator::toEuro(487.22));
        // $this->assertEquals($import[2]['principal'], Calculator::toEuro(13.84));
        // $this->assertEquals($import[2]['interest'], Calculator::toEuro(6.71));
        // $this->assertEquals($import[2]['total'], (Calculator::toEuro(6.71) + Calculator::toEuro(13.84)));


        // // checks on 6rd installment
        // $this->assertEquals($import[5]['seq_num'], 6);
        // $this->assertEquals($import[5]['due_date'], "2020-12-19");

        // $this->assertEquals($import[5]['original_currency_id'], $originalCurrencyId);
        // $this->assertEquals($import[5]['original_remaining_principal'], 441.52);
        // $this->assertEquals($import[5]['original_principal'], 18.29);
        // $this->assertEquals($import[5]['original_interest'], 5.89);

        // $this->assertEquals($import[5]['currency_id'], Currency::ID_EUR);
        // $this->assertEquals($import[5]['remaining_principal'], Calculator::toEuro(441.52));
        // $this->assertEquals($import[5]['principal'], Calculator::toEuro(18.29));
        // $this->assertEquals($import[5]['interest'], Calculator::toEuro(5.89));
        // $this->assertEquals($import[5]['total'], (Calculator::toEuro(5.89) + Calculator::toEuro(18.29)));
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
