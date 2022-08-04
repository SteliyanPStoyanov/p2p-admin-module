<?php

namespace Tests\Unit\Invest;

use App;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Country;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Entities\RepaidLoan;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use stdClass;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class InvestingTest extends TestCase
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
        $interestRatePercent = 16.5;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '11/25/2020',
            '5/25/2021',
            6,
            0,
            Calculator::toBgn(240),
            Calculator::toBgn(240),
            4412010476
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );

        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = '11/25/2020';
        $loan->save();

        Loan::where(
            [
                'status' => Loan::STATUS_NEW,
                'lender_id' => $lenderId,
            ]
        )->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => $lenderId . 1,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/25/2020',
                        'currency_id' => Currency::ID_EUR,
                        'principal' => Calculator::toBgn(39.20),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 2,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '01/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(39.44),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 3,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '02/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(39.76),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 4,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '03/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(40.24),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 5,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '04/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(40.48),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 6,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '05/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(40.88),
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

        $investor = $this->prepareInvestor('investor_' . time() . '@investingTest.com');
        $wallet = $this->prepareWallet($investor);
        $this->preparePortfolios($investor);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $investorBuyAmount = 68.00;
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $loan->installments(),
            Carbon::parse('12/15/2020')
        );

        $this->assertTrue($invested);

        $investorInstallments = $investor->installments($loan->loan_id);
        $this->assertCount(6, $investorInstallments);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(1, $investorInstallment->installment()->seq_num);
        $this->assertEquals(28.333333333333, $investorInstallment->interest_percent);
        $this->assertEquals(11.11, $investorInstallment->principal);
        // $this->assertEquals(240.00, $investorInstallment->remaining_principal);
        $this->assertEquals(0.31, $investorInstallment->interest);
        $this->assertEquals(11.42, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(2, $investorInstallment->installment()->seq_num);
        $this->assertEquals(28.333333333333, $investorInstallment->interest_percent);
        $this->assertEquals(11.17, $investorInstallment->principal);
        // $this->assertEquals((240 - 39.20), $investorInstallment->remaining_principal);
        $this->assertEquals(0.81, $investorInstallment->interest);
        $this->assertEquals(11.98, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(3, $investorInstallment->installment()->seq_num);
        $this->assertEquals(28.333333333333, $investorInstallment->interest_percent);
        $this->assertEquals(11.27, $investorInstallment->principal);
        // $this->assertEquals((240 - (39.20 + 39.44)), $investorInstallment->remaining_principal);
        $this->assertEquals(0.65, $investorInstallment->interest);
        $this->assertEquals(11.92, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(4, $investorInstallment->installment()->seq_num);
        $this->assertEquals(28.333333333333, $investorInstallment->interest_percent);
        $this->assertEquals(11.40, $investorInstallment->principal);
        // $this->assertEquals((240 - (39.20 + 39.44 + 39.76)), $investorInstallment->remaining_principal);
        $this->assertEquals(0.44, $investorInstallment->interest);
        $this->assertEquals(11.84, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(5, $investorInstallment->installment()->seq_num);
        $this->assertEquals(28.333333333333, $investorInstallment->interest_percent);
        $this->assertEquals(11.47, $investorInstallment->principal);
        // $this->assertEquals((240 - (39.20 + 39.44 + 39.76 + 40.24)), $investorInstallment->remaining_principal);
        $this->assertEquals(0.33, $investorInstallment->interest);
        $this->assertEquals(11.80, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(6, $investorInstallment->installment()->seq_num);
        $this->assertEquals(28.333333333333, $investorInstallment->interest_percent);
        $this->assertEquals(11.58, $investorInstallment->principal);
        // $this->assertEquals((240 - (39.20 + 39.44 + 39.76 + 40.24 + 40.48)), $investorInstallment->remaining_principal);
        $this->assertEquals(0.16, $investorInstallment->interest);
        $this->assertEquals(11.74, $investorInstallment->total);

        if (get_class($this) === InvestingTest::class) {
            $this->removeTestData($investor, $loan);
        }

        return [$investor, $loan];
    }

    public function testLoanTwo()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 12.5;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '9/12/2020',
            '5/25/2021',
            8,
            0,
            Calculator::toBgn(295.73),
            Calculator::toBgn(295.73),
            4412010476
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );

        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = '12/20/2020';
        $loan->save();

        Loan::where(
            [
                'status' => Loan::STATUS_NEW,
                'lender_id' => $lenderId,
            ]
        )->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => $lenderId . 1,
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '10/25/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 0,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 2,
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '11/25/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 0,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 3,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/25/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(42.51),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 4,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '1/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(44.53),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 5,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(47.29),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 6,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '3/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(51.18),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 7,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '4/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(53.39),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 8,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '5/25/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(56.83),
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

        $investor = $this->prepareInvestor('investor2_' . time() . '@investingTest.com');
        $wallet = $this->prepareWallet($investor);
        $this->preparePortfolios($investor);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $investorBuyAmount = 111.00;
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $loan->installments(),
            Carbon::parse('12/22/2020')
        );

        $this->assertTrue($invested);

        $investorInstallments = $investor->installments($loan->loan_id);
        $this->assertCount(6, $investorInstallments);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(3, $investorInstallment->installment()->seq_num);
        $this->assertEquals(37.534237311061, $investorInstallment->interest_percent);
        $this->assertEquals(15.96, $investorInstallment->principal);
        // $this->assertEquals(295.73, $investorInstallment->remaining_principal);
        $this->assertEquals(0.12, $investorInstallment->interest);
        $this->assertEquals(16.08, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(4, $investorInstallment->installment()->seq_num);
        $this->assertEquals(37.534237311061, $investorInstallment->interest_percent);
        $this->assertEquals(16.71, $investorInstallment->principal);
        // $this->assertEquals(253.22, $investorInstallment->remaining_principal);
        $this->assertEquals(1.02, $investorInstallment->interest);
        $this->assertEquals(17.73, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(5, $investorInstallment->installment()->seq_num);
        $this->assertEquals(37.534237311061, $investorInstallment->interest_percent);
        $this->assertEquals(17.75, $investorInstallment->principal);
        // $this->assertEquals(208.69, $investorInstallment->remaining_principal);
        $this->assertEquals(0.84, $investorInstallment->interest);
        $this->assertEquals(18.59, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(6, $investorInstallment->installment()->seq_num);
        $this->assertEquals(37.534237311061, $investorInstallment->interest_percent);
        $this->assertEquals(19.21, $investorInstallment->principal);
        // $this->assertEquals(161.40, $investorInstallment->remaining_principal);
        $this->assertEquals(0.59, $investorInstallment->interest);
        $this->assertEquals(19.80, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(7, $investorInstallment->installment()->seq_num);
        $this->assertEquals(37.534237311061, $investorInstallment->interest_percent);
        $this->assertEquals(20.04, $investorInstallment->principal);
        // $this->assertEquals(110.22, $investorInstallment->remaining_principal);
        $this->assertEquals(0.45, $investorInstallment->interest);
        $this->assertEquals(20.49, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(8, $investorInstallment->installment()->seq_num);
        $this->assertEquals(37.534237311061, $investorInstallment->interest_percent);
        $this->assertEquals(21.33, $investorInstallment->principal);
        // $this->assertEquals(56.83, $investorInstallment->remaining_principal);
        $this->assertEquals(0.22, $investorInstallment->interest);
        $this->assertEquals(21.55, $investorInstallment->total);

        if (get_class($this) === InvestingTest::class) {
            $this->removeTestData($investor, $loan);
        }

        return [$investor, $loan];
    }

    public function testLoanThree()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 10;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '9/28/2020',
            '4/30/2021',
            6,
            0,
            Calculator::toBgn(1294.80),
            Calculator::toBgn(1294.80),
            4412010476
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );

        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = '11/23/2020';
        $loan->save();

        Loan::where(
            [
                'status' => Loan::STATUS_NEW,
                'lender_id' => $lenderId,
            ]
        )->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => $lenderId . 1,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '11/27/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(190.32),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 2,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/31/2020',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(200.02),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 3,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '1/31/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(210.08),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 4,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2/28/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(220.51),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 5,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '3/31/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(231.33),
                    ],
                    [
                        'lender_installment_id' => $lenderId . 6,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '4/30/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(242.54),
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

        $investor = $this->prepareInvestor('investor3_' . time() . '@investingTest.com');
        $wallet = $this->prepareWallet($investor);
        $this->preparePortfolios($investor);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $investorBuyAmount = 844.00;
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $loan->installments(),
            Carbon::parse('11/26/2020')
        );

        $this->assertTrue($invested);

        $investorInstallments = $investor->installments($loan->loan_id);
        $this->assertCount(6, $investorInstallments);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(1, $investorInstallment->installment()->seq_num);
        $this->assertEquals(65.183812171764, $investorInstallment->interest_percent);
        $this->assertEquals(124.06, $investorInstallment->principal);
        // $this->assertEquals(1294.80, $investorInstallment->remaining_principal);
        $this->assertEquals(0.23, $investorInstallment->interest);
        $this->assertEquals(124.29, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(2, $investorInstallment->installment()->seq_num);
        $this->assertEquals(65.183812171764, $investorInstallment->interest_percent);
        $this->assertEquals(130.38, $investorInstallment->principal);
        // $this->assertEquals(1104.48, $investorInstallment->remaining_principal);
        $this->assertEquals(6.80, $investorInstallment->interest);
        $this->assertEquals(137.18, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(3, $investorInstallment->installment()->seq_num);
        $this->assertEquals(65.183812171764, $investorInstallment->interest_percent);
        $this->assertEquals(136.94, $investorInstallment->principal);
        // $this->assertEquals(904.46, $investorInstallment->remaining_principal);
        $this->assertEquals(5.08, $investorInstallment->interest);
        $this->assertEquals(142.02, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(4, $investorInstallment->installment()->seq_num);
        $this->assertEquals(65.183812171764, $investorInstallment->interest_percent);
        $this->assertEquals(143.74, $investorInstallment->principal);
        // $this->assertEquals(694.38, $investorInstallment->remaining_principal);
        $this->assertEquals(3.52, $investorInstallment->interest);
        $this->assertEquals(147.26, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(5, $investorInstallment->installment()->seq_num);
        $this->assertEquals(65.183812171764, $investorInstallment->interest_percent);
        $this->assertEquals(150.79, $investorInstallment->principal);
        // $this->assertEquals(473.87, $investorInstallment->remaining_principal);
        $this->assertEquals(2.66, $investorInstallment->interest);
        $this->assertEquals(153.45, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(6, $investorInstallment->installment()->seq_num);
        $this->assertEquals(65.183812171764, $investorInstallment->interest_percent);
        $this->assertEquals(158.09, $investorInstallment->principal);
        // $this->assertEquals(242.54, $investorInstallment->remaining_principal);
        $this->assertEquals(1.32, $investorInstallment->interest);
        $this->assertEquals(159.41, $investorInstallment->total);

        if (get_class($this) === InvestingTest::class) {
            $this->removeTestData($investor, $loan);
        }

        return [$investor, $loan];
    }

    public function testLoanFour()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 14;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;

        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '12/21/2020',
            '12/21/2022',
            24,
            0,
            Calculator::toBgn(511.29),
            Calculator::toBgn(511.29),
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

        Loan::where(
            [
                'status' => Loan::STATUS_NEW,
                'lender_id' => $lenderId,
            ]
        )->chunkById(
            100,
            function ($loans) use ($lenderId) {

                $newLoans = [];
                foreach ($loans as $loan) {
                    $newLoans[$loan->lender_id] = $loan;
                }

                $installmentsToImport = [
                    [
                        'lender_installment_id' => $lenderId . 1,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '1/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 6.39,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 2,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 7.28,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 3,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '3/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 8.29,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 4,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '4/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 9.45,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 5,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '5/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 10.76,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 6,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '6/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 12.26,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 7,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '7/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 13.96,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 8,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '8/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 15.9,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 9,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '9/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 18.11,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 10,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '10/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 20.63,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 11,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '11/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 23.5,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 12,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/21/2021',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 26.77,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 13,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '1/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 30.49,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 14,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 34.73,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 15,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '3/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 39.56,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 16,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '4/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 45.06,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 17,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '5/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 51.33,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 18,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '6/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 58.46,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 19,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '7/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 66.59,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 20,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '8/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 75.85,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 21,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '9/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 86.4,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 22,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '10/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 98.42,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 23,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '11/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 112.1,
                    ],
                    [
                        'lender_installment_id' => $lenderId . 24,
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '12/21/2022',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => Calculator::toBgn(65.27),
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

        $investor = $this->prepareInvestor('investor4_' . time() . '@investingTest.com');
        $wallet = $this->prepareWallet($investor);
        $this->preparePortfolios($investor);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $investorBuyAmount = 384.00;
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $loan->installments(),
            Carbon::parse('1/5/2021')
        );

        $this->assertTrue($invested);

        $investorInstallments = $investor->installments($loan->loan_id);
        $this->assertCount(24, $investorInstallments);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(1, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(2.46, $investorInstallment->principal);
        $this->assertEquals(2.39, $investorInstallment->interest);
        $this->assertEquals(4.85, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(2, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(2.79, $investorInstallment->principal);
        $this->assertEquals(4.60, $investorInstallment->interest);
        $this->assertEquals(7.39, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(3, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(3.18, $investorInstallment->principal);
        $this->assertEquals(4.12, $investorInstallment->interest);
        $this->assertEquals(7.30, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(4, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(3.63, $investorInstallment->principal);
        $this->assertEquals(4.53, $investorInstallment->interest);
        $this->assertEquals(8.16, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(5, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(4.13, $investorInstallment->principal);
        $this->assertEquals(4.34, $investorInstallment->interest);
        $this->assertEquals(8.47, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(6, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(4.71, $investorInstallment->principal);
        $this->assertEquals(4.43, $investorInstallment->interest);
        $this->assertEquals(9.14, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(7, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(5.36, $investorInstallment->principal);
        $this->assertEquals(4.24, $investorInstallment->interest);
        $this->assertEquals(9.60, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(8, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(6.11, $investorInstallment->principal);
        $this->assertEquals(4.31, $investorInstallment->interest);
        $this->assertEquals(10.42, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(9, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(6.95, $investorInstallment->principal);
        $this->assertEquals(4.24, $investorInstallment->interest);
        $this->assertEquals(11.19, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(10, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(7.92, $investorInstallment->principal);
        $this->assertEquals(4.02, $investorInstallment->interest);
        $this->assertEquals(11.94, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(11, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(9.03, $investorInstallment->principal);
        $this->assertEquals(4.06, $investorInstallment->interest);
        $this->assertEquals(13.09, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(12, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(10.28, $investorInstallment->principal);
        $this->assertEquals(3.82, $investorInstallment->interest);
        $this->assertEquals(14.10, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(13, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(11.71, $investorInstallment->principal);
        $this->assertEquals(3.83, $investorInstallment->interest);
        $this->assertEquals(15.54, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(14, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(13.34, $investorInstallment->principal);
        $this->assertEquals(3.69, $investorInstallment->interest);
        $this->assertEquals(17.03, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(15, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(15.19, $investorInstallment->principal);
        $this->assertEquals(3.18, $investorInstallment->interest);
        $this->assertEquals(18.37, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(16, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(17.30, $investorInstallment->principal);
        $this->assertEquals(3.34, $investorInstallment->interest);
        $this->assertEquals(20.64, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(17, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(19.71, $investorInstallment->principal);
        $this->assertEquals(3.03, $investorInstallment->interest);
        $this->assertEquals(22.74, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(18, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(22.45, $investorInstallment->principal);
        $this->assertEquals(2.90, $investorInstallment->interest);
        $this->assertEquals(25.35, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(19, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(25.57, $investorInstallment->principal);
        $this->assertEquals(2.54, $investorInstallment->interest);
        $this->assertEquals(28.11, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(20, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(29.13, $investorInstallment->principal);
        $this->assertEquals(2.32, $investorInstallment->interest);
        $this->assertEquals(31.45, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(21, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(33.18, $investorInstallment->principal);
        $this->assertEquals(1.97, $investorInstallment->interest);
        $this->assertEquals(35.15, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(22, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(37.79, $investorInstallment->principal);
        $this->assertEquals(1.52, $investorInstallment->interest);
        $this->assertEquals(39.31, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(23, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(43.05, $investorInstallment->principal);
        $this->assertEquals(1.11, $investorInstallment->interest);
        $this->assertEquals(44.16, $investorInstallment->total);

        $investorInstallment = array_shift($investorInstallments);
        $this->assertEquals(24, $investorInstallment->installment()->seq_num);
        $this->assertEquals(75.104148330693, $investorInstallment->interest_percent);
        $this->assertEquals(49.03, $investorInstallment->principal);
        $this->assertEquals(0.57, $investorInstallment->interest);
        $this->assertEquals(49.60, $investorInstallment->total);

        if (get_class($this) === InvestingTest::class) {
            $this->removeTestData($investor, $loan);
        }

        return [$investor, $loan];
    }

    public function testLoanFive()
    {
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 16;

        $creditIdsAndPercents[$lenderId] = $interestRatePercent;
        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '2020-05-04',
            '2021-05-22',
            12,
            0,
            700,
            420.31,
            4412010476
        );

        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );
        $this->importService->loansMassInsert($import);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->prepaid_schedule_payments = 7;
        $loan->created_at = '2021-01-20';
        $loan->save();

        Loan::where(
            [
                'status' => Loan::STATUS_NEW,
                'lender_id' => $lenderId,
            ]
        )->chunkById(
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
                        'due_date' => '2020-06-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 26.47,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '2020-07-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 30.04,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '2020-08-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 34.10,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '2020-09-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 38.70,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '2020-10-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 43.93,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '2020-11-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 49.86,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 1,
                        'due_date' => '2020-12-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 56.59
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2021-01-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 64.23,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2021-02-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 72.90,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2021-03-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 82.74,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2021-04-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 93.91,
                    ],
                    [
                        'lender_installment_id' => rand(100, 500),
                        'lender_id' => $lenderId,
                        'paid' => 0,
                        'due_date' => '2021-05-22',
                        'currency_id' => Currency::ID_BGN,
                        'principal' => 06.53,
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
        $installments = $loan->installments();



        $investor = $this->prepareInvestor('investor5_' . time() . '@zeroInterest.kur');
        $wallet = $this->prepareWallet($investor);
        $this->preparePortfolios($investor);

        $loan = Loan::where('lender_id', $lenderId)->first();
        $investorBuyAmount = 22;
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $installments,
            Carbon::parse('2021-01-20')
        );

        $this->assertTrue($invested);

        $investorInstallments = $investor->installments($loan->loan_id);

        $this->assertEquals($investorInstallments[0]->days, 2);
        $this->assertEquals($investorInstallments[0]->principal, 3.36);
        $this->assertEquals($investorInstallments[0]->interest, 0.02);

        $this->assertEquals($investorInstallments[1]->days, 31);
        $this->assertEquals($investorInstallments[1]->principal, 3.82);
        $this->assertEquals($investorInstallments[1]->interest, 0.26);

        $this->assertEquals($investorInstallments[2]->days, 28);
        $this->assertEquals($investorInstallments[2]->principal, 4.33);
        $this->assertEquals($investorInstallments[2]->interest, 0.18);

        $this->assertEquals($investorInstallments[3]->days, 31);
        $this->assertEquals($investorInstallments[3]->principal, 4.92);
        $this->assertEquals($investorInstallments[3]->interest, 0.14);

        $this->assertEquals($investorInstallments[4]->days, 30);
        $this->assertEquals($investorInstallments[4]->principal, 5.57);
        $this->assertEquals($investorInstallments[4]->interest, 0.07);

        $this->removeTestData($investor, $loan);
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
