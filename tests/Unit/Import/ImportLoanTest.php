<?php

namespace Tests\Unit\Import;

use App;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Country;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\RepaidLoan;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use stdClass;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class ImportLoanTest extends TestCase
{
    use WithoutMiddleware;
    use TestDataTrait;

    protected $investService;
    protected $importService;

    public function setUp(): void
    {
        parent::setUp();

        $this->investService = App::make(InvestService::class);
        $this->importService = App::make(ImportService::class);
    }

    public function testLoanOne()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 12;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '8/25/2020', // listing date
            '2/25/2021', // final payment date
            6, // installments count
            0, // overdue
            750, // amount
            466.24, // remainig principal of loan
            4412010476 // egn
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );

        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = '12/1/2020';
        $loan->prepaid_schedule_payments = 3;
        $loan->save();

        $this->getNewLoans($lenderId)->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '9/25/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 79.43,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '10/25/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 93.73,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '11/25/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 110.6,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/25/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 130.51,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '1/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 154,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 181.73,
                    ],
                ];

                // import installments and update loans
                $this->importService->addInstallmentsAndUpdateLoans(
                    $installmentsToImport,
                    $newLoans
                );
            },
            'loan_id'
        );

        $loan->refresh();

        $this->assertNotEmpty($loan);
        $this->assertEquals(Currency::ID_EUR, $loan->currency_id);
        $this->assertEquals(750, $loan->original_amount);
        $this->assertEquals(383.47, $loan->amount);
//        $this->assertEquals(238.39, $loan->remaining_principal);

        $installments = $loan->installments();

        $this->assertCount(6, $installments);
        $this->assertCount(3, $loan->getUnpaidInstallments());

        // PAID INSTALLMENTS CHECK
        $installment = array_shift($installments);
        $this->assertEquals(1, $installment->seq_num);
        $this->assertEquals('2020-09-25', $installment->due_date);
        $this->assertEquals(0, $installment->interest);
        $this->assertEquals(1, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(2, $installment->seq_num);
        $this->assertEquals('2020-10-25', $installment->due_date);
        $this->assertEquals(0, $installment->interest);
        $this->assertEquals(1, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(3, $installment->seq_num);
        $this->assertEquals('2020-11-25', $installment->due_date);
        $this->assertEquals(0, $installment->interest);
        $this->assertEquals(1, $installment->paid);

        // UNPAID INSTALLMENTS CHECK
        $installment = array_shift($installments);
        $this->assertEquals(4, $installment->seq_num);
        $this->assertEquals('2020-12-25', $installment->due_date);
        $this->assertEquals(66.73, $installment->principal);
        $this->assertEquals(1.91, $installment->interest);
        $this->assertEquals(68.64, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(5, $installment->seq_num);
        $this->assertEquals('2021-01-25', $installment->due_date);
        $this->assertEquals(78.74, $installment->principal);
        $this->assertEquals(1.77, $installment->interest);
        $this->assertEquals(80.51, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(6, $installment->seq_num);
        $this->assertEquals('2021-02-25', $installment->due_date);
        $this->assertEquals(92.92, $installment->principal);
        $this->assertEquals(0.96, $installment->interest);
        $this->assertEquals(93.88, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $this->removeTestData(null, $loan);
    }

    public function testLoanTwo()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 12;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '12/23/2020',
            '6/23/2021',
            6,
            0,
            500,
            500,
            4412010476
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );

        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = '12/23/2020';
        $loan->save();

        $this->getNewLoans($lenderId)->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '1/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 52.96,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 62.49,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '3/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 73.74,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '4/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 87.01,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '5/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 102.68,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '6/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 121.12,
                    ],

                ];

                // import installments and update loans
                $this->importService->addInstallmentsAndUpdateLoans(
                    $installmentsToImport,
                    $newLoans
                );
            },
            'loan_id'
        );

        $loan->refresh();

        $this->assertNotEmpty($loan);
        $this->assertEquals(Currency::ID_EUR, $loan->currency_id);
        $this->assertEquals(255.65, $loan->amount);
        $this->assertEquals(255.65, $loan->remaining_principal);

        $installments = $loan->installments();

        $this->assertCount(6, $installments);
        $this->assertCount(6, $loan->getUnpaidInstallments());

        $installment = array_shift($installments);
        $this->assertEquals(1, $installment->seq_num);
        $this->assertEquals('2021-01-23', $installment->due_date);
        $this->assertEquals(27.08, $installment->principal);
        $this->assertEquals(2.64, $installment->interest);
        $this->assertEquals(29.72, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(2, $installment->seq_num);
        $this->assertEquals('2021-02-23', $installment->due_date);
        $this->assertEquals(31.95, $installment->principal);
        $this->assertEquals(2.36, $installment->interest);
        $this->assertEquals(34.31, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(3, $installment->seq_num);
        $this->assertEquals('2021-03-23', $installment->due_date);
        $this->assertEquals(37.7, $installment->principal);
        $this->assertEquals(1.84, $installment->interest);
        $this->assertEquals(39.54, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(4, $installment->seq_num);
        $this->assertEquals('2021-04-23', $installment->due_date);
        $this->assertEquals(44.49, $installment->principal);
        $this->assertEquals(1.64, $installment->interest);
        $this->assertEquals(46.13, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(5, $installment->seq_num);
        $this->assertEquals('2021-05-23', $installment->due_date);
        $this->assertEquals(52.5, $installment->principal);
        $this->assertEquals(1.14, $installment->interest);
        $this->assertEquals(53.64, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(6, $installment->seq_num);
        $this->assertEquals('2021-06-23', $installment->due_date);
        $this->assertEquals(61.93, $installment->principal);
        $this->assertEquals(0.64, $installment->interest);
        $this->assertEquals(62.57, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $this->removeTestData(null, $loan);
    }

    public function testLoanThree()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 8;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '12/23/2020',
            '6/23/2021',
            6,
            0,
            500,
            500,
            4412010476
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );

        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = '12/30/2020';
        $loan->save();

        $this->getNewLoans($lenderId)->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '1/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 52.96,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 62.49,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '3/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 73.74,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '4/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 87.01,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '5/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 102.68,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '6/23/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 121.12,
                    ],

                ];

                // import installments and update loans
                $this->importService->addInstallmentsAndUpdateLoans(
                    $installmentsToImport,
                    $newLoans
                );
            },
            'loan_id'
        );

        $loan->refresh();

        $this->assertNotEmpty($loan);
        $this->assertEquals(Currency::ID_EUR, $loan->currency_id);
        $this->assertEquals(255.65, $loan->amount);
        $this->assertEquals(255.65, $loan->remaining_principal);

        $installments = $loan->installments();

        $this->assertCount(6, $installments);
        $this->assertCount(6, $loan->getUnpaidInstallments());

        $installment = array_shift($installments);
        $this->assertEquals(1, $installment->seq_num);
        $this->assertEquals('2021-01-23', $installment->due_date);
        $this->assertEquals(27.08, $installment->principal);
        $this->assertEquals(1.36, $installment->interest);
        $this->assertEquals(28.44, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(2, $installment->seq_num);
        $this->assertEquals('2021-02-23', $installment->due_date);
        $this->assertEquals(31.95, $installment->principal);
        $this->assertEquals(1.57, $installment->interest);
        $this->assertEquals(33.52, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(3, $installment->seq_num);
        $this->assertEquals('2021-03-23', $installment->due_date);
        $this->assertEquals(37.7, $installment->principal);
        $this->assertEquals(1.22, $installment->interest);
        $this->assertEquals(38.92, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(4, $installment->seq_num);
        $this->assertEquals('2021-04-23', $installment->due_date);
        $this->assertEquals(44.49, $installment->principal);
        $this->assertEquals(1.09, $installment->interest);
        $this->assertEquals(45.58, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(5, $installment->seq_num);
        $this->assertEquals('2021-05-23', $installment->due_date);
        $this->assertEquals(52.5, $installment->principal);
        $this->assertEquals(0.76, $installment->interest);
        $this->assertEquals(53.26, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(6, $installment->seq_num);
        $this->assertEquals('2021-06-23', $installment->due_date);
        $this->assertEquals(61.93, $installment->principal);
        $this->assertEquals(0.43, $installment->interest);
        $this->assertEquals(62.36, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $this->removeTestData(null, $loan);
    }

    public function testLoanFour()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 11.5;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '11/28/2020',
            '12/28/2020',
            1,
            0,
            100,
            100,
            4412010476,
            Loan::TYPE_PAYDAY
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );

        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = '11/28/2020';
        $loan->save();

        $this->getNewLoans($lenderId)->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/28/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 100,
                    ],
                ];

                // import installments and update loans
                $this->importService->addInstallmentsAndUpdateLoans(
                    $installmentsToImport,
                    $newLoans
                );
            },
            'loan_id'
        );

        $loan->refresh();

        $this->assertNotEmpty($loan);
        $this->assertEquals(Currency::ID_EUR, $loan->currency_id);
        $this->assertEquals(51.13, $loan->amount);
        $this->assertEquals(51.13, $loan->remaining_principal);

        $installments = $loan->installments();

        $this->assertCount(1, $installments);
        $this->assertCount(1, $loan->getUnpaidInstallments());

        $installment = array_shift($installments);
        $this->assertEquals(1, $installment->seq_num);
        $this->assertEquals('2020-12-28', $installment->due_date);
        $this->assertEquals(51.13, $installment->principal);
        $this->assertEquals(0.49, $installment->interest);
        $this->assertEquals(51.62, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $this->removeTestData(null, $loan);
    }

    public function testLoanFive()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 11.5;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '11/28/2020',
            '12/28/2020',
            1,
            0,
            100,
            100,
            4412010476,
            Loan::TYPE_PAYDAY
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );

        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = '12/15/2020';
        $loan->save();

        $this->getNewLoans($lenderId)->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/28/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 100,
                    ],
                ];

                // import installments and update loans
                $this->importService->addInstallmentsAndUpdateLoans(
                    $installmentsToImport,
                    $newLoans
                );
            },
            'loan_id'
        );

        $loan->refresh();

        $this->assertNotEmpty($loan);
        $this->assertEquals(Currency::ID_EUR, $loan->currency_id);
        $this->assertEquals(51.13, $loan->amount);
        $this->assertEquals(51.13, $loan->remaining_principal);

        $installments = $loan->installments();

        $this->assertCount(1, $installments);
        $this->assertCount(1, $loan->getUnpaidInstallments());

        $installment = array_shift($installments);
        $this->assertEquals(1, $installment->seq_num);
        $this->assertEquals('2020-12-28', $installment->due_date);
        $this->assertEquals(51.13, $installment->principal);
        $this->assertEquals(0.21, $installment->interest);
        $this->assertEquals(51.34, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $this->removeTestData(null, $loan);
    }

    public function testLoanSix()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 14;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '12/21/2020',
            '12/28/2020',
            24,
            0,
            1000,
            1000,
            4412010476,
            Loan::TYPE_PAYDAY
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );

        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = '1/14/2021';
        $loan->save();

        $this->getNewLoans($lenderId)->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '1/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 6.39,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 7.28,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '3/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 8.29,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '4/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 9.45,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '5/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 10.76,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '6/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 12.26,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '7/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 13.96,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '8/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 15.9,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '9/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 18.11,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '10/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 20.63,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '11/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 23.5,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 26.77,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '1/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 30.49,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 34.73,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '3/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 39.56,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '4/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 45.06,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '5/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 51.33,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '6/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 58.46,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '7/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 66.59,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '8/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 75.85,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '9/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 86.4,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '10/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 98.42,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '11/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 112.1,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 127.71,
                    ],
                ];

                // import installments and update loans
                $this->importService->addInstallmentsAndUpdateLoans(
                    $installmentsToImport,
                    $newLoans
                );
            },
            'loan_id'
        );

        $loan->refresh();

        $this->assertNotEmpty($loan);
        $this->assertEquals(Currency::ID_EUR, $loan->currency_id);
        $this->assertEquals(511.29, $loan->amount);
        $this->assertEquals(511.29, $loan->remaining_principal);

        $installments = $loan->installments();

        $this->assertCount(24, $installments);
        $this->assertCount(24, $loan->getUnpaidInstallments());

        $installment = array_shift($installments);
        $this->assertEquals(1, $installment->seq_num);
        $this->assertEquals('2021-01-21', $installment->due_date);
        $this->assertEquals(3.27, $installment->principal);
        $this->assertEquals(1.39, $installment->interest);
        $this->assertEquals(4.66, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(2, $installment->seq_num);
        $this->assertEquals('2021-02-21', $installment->due_date);
        $this->assertEquals(3.72, $installment->principal);
        $this->assertEquals(6.12, $installment->interest);
        $this->assertEquals(9.84, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(3, $installment->seq_num);
        $this->assertEquals('2021-03-21', $installment->due_date);
        $this->assertEquals(4.24, $installment->principal);
        $this->assertEquals(5.49, $installment->interest);
        $this->assertEquals(9.73, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(4, $installment->seq_num);
        $this->assertEquals('2021-04-21', $installment->due_date);
        $this->assertEquals(4.83, $installment->principal);
        $this->assertEquals(6.03, $installment->interest);
        $this->assertEquals(10.86, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(5, $installment->seq_num);
        $this->assertEquals('2021-05-21', $installment->due_date);
        $this->assertEquals(5.50, $installment->principal);
        $this->assertEquals(5.78, $installment->interest);
        $this->assertEquals(11.28, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(6, $installment->seq_num);
        $this->assertEquals('2021-06-21', $installment->due_date);
        $this->assertEquals(6.27, $installment->principal);
        $this->assertEquals(5.90, $installment->interest);
        $this->assertEquals(12.17, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(7, $installment->seq_num);
        $this->assertEquals('2021-07-21', $installment->due_date);
        $this->assertEquals(7.14, $installment->principal);
        $this->assertEquals(5.64, $installment->interest);
        $this->assertEquals(12.78, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(8, $installment->seq_num);
        $this->assertEquals('2021-08-21', $installment->due_date);
        $this->assertEquals(8.13, $installment->principal);
        $this->assertEquals(5.74, $installment->interest);
        $this->assertEquals(13.87, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(9, $installment->seq_num);
        $this->assertEquals('2021-09-21', $installment->due_date);
        $this->assertEquals(9.26, $installment->principal);
        $this->assertEquals(5.64, $installment->interest);
        $this->assertEquals(14.90, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(10, $installment->seq_num);
        $this->assertEquals('2021-10-21', $installment->due_date);
        $this->assertEquals(10.55, $installment->principal);
        $this->assertEquals(5.35, $installment->interest);
        $this->assertEquals(15.90, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(11, $installment->seq_num);
        $this->assertEquals('2021-11-21', $installment->due_date);
        $this->assertEquals(12.02, $installment->principal);
        $this->assertEquals(5.41, $installment->interest);
        $this->assertEquals(17.43, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(12, $installment->seq_num);
        $this->assertEquals('2021-12-21', $installment->due_date);
        $this->assertEquals(13.69, $installment->principal);
        $this->assertEquals(5.09, $installment->interest);
        $this->assertEquals(18.78, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(13, $installment->seq_num);
        $this->assertEquals('2022-01-21', $installment->due_date);
        $this->assertEquals(15.59, $installment->principal);
        $this->assertEquals(5.10, $installment->interest);
        $this->assertEquals(20.69, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(14, $installment->seq_num);
        $this->assertEquals('2022-02-21', $installment->due_date);
        $this->assertEquals(17.76, $installment->principal);
        $this->assertEquals(4.91, $installment->interest);
        $this->assertEquals(22.67, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(15, $installment->seq_num);
        $this->assertEquals('2022-03-21', $installment->due_date);
        $this->assertEquals(20.23, $installment->principal);
        $this->assertEquals(4.24, $installment->interest);
        $this->assertEquals(24.47, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(16, $installment->seq_num);
        $this->assertEquals('2022-04-21', $installment->due_date);
        $this->assertEquals(23.04, $installment->principal);
        $this->assertEquals(4.45, $installment->interest);
        $this->assertEquals(27.49, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(17, $installment->seq_num);
        $this->assertEquals('2022-05-21', $installment->due_date);
        $this->assertEquals(26.24, $installment->principal);
        $this->assertEquals(4.04, $installment->interest);
        $this->assertEquals(30.28, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(18, $installment->seq_num);
        $this->assertEquals('2022-06-21', $installment->due_date);
        $this->assertEquals(29.89, $installment->principal);
        $this->assertEquals(3.86, $installment->interest);
        $this->assertEquals(33.75, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(19, $installment->seq_num);
        $this->assertEquals('2022-07-21', $installment->due_date);
        $this->assertEquals(34.05, $installment->principal);
        $this->assertEquals(3.38, $installment->interest);
        $this->assertEquals(37.43, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(20, $installment->seq_num);
        $this->assertEquals('2022-08-21', $installment->due_date);
        $this->assertEquals(38.78, $installment->principal);
        $this->assertEquals(3.08, $installment->interest);
        $this->assertEquals(41.86, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(21, $installment->seq_num);
        $this->assertEquals('2022-09-21', $installment->due_date);
        $this->assertEquals(44.18, $installment->principal);
        $this->assertEquals(2.62, $installment->interest);
        $this->assertEquals(46.80, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(22, $installment->seq_num);
        $this->assertEquals('2022-10-21', $installment->due_date);
        $this->assertEquals(50.32, $installment->principal);
        $this->assertEquals(2.02, $installment->interest);
        $this->assertEquals(52.34, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(23, $installment->seq_num);
        $this->assertEquals('2022-11-21', $installment->due_date);
        $this->assertEquals(57.32, $installment->principal);
        $this->assertEquals(1.48, $installment->interest);
        $this->assertEquals(58.80, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $installment = array_shift($installments);
        $this->assertEquals(24, $installment->seq_num);
        $this->assertEquals('2022-12-21', $installment->due_date);
        $this->assertEquals(65.30, $installment->principal);
        $this->assertEquals(0.76, $installment->interest);
        $this->assertEquals(66.06, $installment->total);
        $this->assertEquals(0, $installment->paid);

        $this->removeTestData(null, $loan);
    }

    public function getLoan(
        int $lenderId,
        string $lenderIssueDate,
        string $finalPaymentDate,
        int $period,
        int $overdueDays,
        float $amount,
        float $amountAfranga,
        string $pin,
        string $type = Loan::TYPE_INSTALLMENTS
    ): stdClass
    {
        $loan = new stdClass();

        $loan->originator_id = Originator::ID_ORIG_STIKCREDIT;
        $loan->lender_id = $lenderId;
        $loan->contract_id = rand(10, 100);
        $loan->type = $type;
        $loan->from_office = 0;
        $loan->country_id = Country::ID_BG;
        $loan->currency_id = Country::ID_BG;
        $loan->lender_issue_date = $lenderIssueDate;
        $loan->final_payment_date = $finalPaymentDate;
        $loan->prepaid_schedule_payments = 0;
        $loan->period = $period;
        $loan->overdue_days = $overdueDays;
        $loan->amount = $amount;
        $loan->amount_afranga = $amountAfranga;
        $loan->status = Loan::STATUS_NEW;
        $loan->pin = $pin;

        return $loan;
    }

    protected function getNewLoans($lenderId)
    {
        return Loan::where(
            [
                'status' => Loan::STATUS_NEW,
                'lender_id' => $lenderId,
            ]
        );
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
