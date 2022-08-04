<?php

namespace Tests\Unit\Installment;

use App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Country;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Originator;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Modules\Common\Services\LoanService;
use stdClass;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

/**
 * This test is used for validation of dealing with loans with prepaid installments in future
 * Means, the borrower have to pay installments next 5 month, but he do a payments
 * and close 3 installment in future, so the nearest due date will in 2 month
 * that means we should give bigger interest for saling
 */
class PrepaidInstallmentsTest extends TestCase
{
    use TestDataTrait;

    protected $investService;
    protected $importService;
    protected $loanService;

    public function setUp(): void
    {
        parent::setUp();
        $this->importService = App::make(ImportService::class);
        $this->investService = App::make(InvestService::class);
        $this->loanService = App::make(LoanService::class);
    }

    public function testImport()
    {
        // PREPARE LOAN
        $listingDate = '2021-01-20 09:55:03';
        $lenderId = rand(123456, 654321);
        $interestRatePercent = 16.50;
        $creditIdsAndPercents[$lenderId] = $interestRatePercent;
        $loansToImport[$lenderId] = $this->getLoan(
            $lenderId,
            '2020-05-11',
            '2021-05-22',
            12,
            0,
            1000,
            380.23,
            9
        );
        $import = $this->importService->prepareLoans(
            $loansToImport,
            $creditIdsAndPercents
        );
        $this->importService->loansMassInsert($import);
        $loan = Loan::where('lender_id', $lenderId)->first();
        $loan->created_at = $listingDate;
        $loan->save();

        // PREPARE INSTALLMENTS
        $newLoans = [
            $loan->lender_id => $loan,
        ];
        $installmentsToImport = [
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 1,
                'due_date' => '2020-06-25',
                'currency_id' => Currency::ID_BGN,
                'principal' => 43.15,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 1,
                'due_date' => '2020-07-11',
                'currency_id' => Currency::ID_BGN,
                'principal' => 48.04,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 1,
                'due_date' => '2020-09-07',
                'currency_id' => Currency::ID_BGN,
                'principal' => 53.48,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 1,
                'due_date' => '2020-10-20',
                'currency_id' => Currency::ID_BGN,
                'principal' => 59.54,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 1,
                'due_date' => '2020-10-20',
                'currency_id' => Currency::ID_BGN,
                'principal' => 66.29,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 1,
                'due_date' => '2020-11-27',
                'currency_id' => Currency::ID_BGN,
                'principal' => 73.80,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 1,
                'due_date' => '2020-12-22',
                'currency_id' => Currency::ID_BGN,
                'principal' => 82.17,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 1,
                'due_date' => '2021-01-22',
                'currency_id' => Currency::ID_BGN,
                'principal' => 91.48,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 1,
                'due_date' => '2021-02-22',
                'currency_id' => Currency::ID_BGN,
                'principal' => 101.85,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 0,
                'due_date' => '2021-03-22',
                'currency_id' => Currency::ID_BGN,
                'principal' => 113.39,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 0,
                'due_date' => '2021-04-22',
                'currency_id' => Currency::ID_BGN,
                'principal' => 126.24,
            ],
            [
                'lender_installment_id' => rand(100, 50000),
                'lender_id' => $lenderId,
                'paid' => 0,
                'due_date' => '2021-05-22',
                'currency_id' => Currency::ID_BGN,
                'principal' => 140.57,
            ],
        ];
        $this->importService->addInstallmentsAndUpdateLoans(
            $installmentsToImport,
            $newLoans
        );

        // this rate should on import
        $remainingPrincipal = 194.41;

        // CHECK REMAINING PRINCIPAL OF LOAN
        $loan->refresh();
        $this->assertEquals($remainingPrincipal, $loan->amount_afranga);
        $this->assertEquals(Calculator::getAvailableAmount($remainingPrincipal, 10), $loan->amount_available);
        $this->assertEquals($remainingPrincipal, $loan->remaining_principal);

        // CHECK ISNTALLMENT DAYS AND INTEREST
        $installment = $loan->getFirstUnpaidInstallment();
        $this->assertEquals($remainingPrincipal, $installment->remaining_principal);
        $this->assertEquals(57.98, $installment->principal);
        $this->assertEquals(5.44, $installment->interest);

        // VALIDATE DAYS TILL DUE DATE, AND INTEREST CALCULATIONS
        // Prev installment is paid
        $days = InstallmentCalculator::getInstallmentDaysCountGlobal(
            Carbon::parse($listingDate),
            Carbon::parse($installment->due_date),
            Carbon::parse($installment->getPreviousInstallmentDueDate(true)),
            true
        ); // 61 days to due_date
        $this->assertEquals(61, $days);

        $interest = InstallmentCalculator::round($remainingPrincipal * $days * $interestRatePercent / 360 / 100);
        $this->assertEquals($interest, $installment->interest);

        return $loan;
    }

    /**
     * @depends testImport
     */
    public function testInvest($loan)
    {
        $investor = $this->prepareInvestor('investor_' . time() . '@prepaidInstallments.com');
        $wallet = $this->prepareWallet($investor);
        $this->preparePortfolios($investor);


        $investorBuyAmount = 50;
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $loan->installments(),
            Carbon::parse('2021-01-22')
        );
        $this->assertTrue($invested);


        $investorInstallments = $investor->installments($loan->loan_id);
        $this->assertCount(3, $investorInstallments);


        $investorInstallment = (object) $investorInstallments[0]->toArray();
        $this->assertEquals(50.00, $investorInstallment->remaining_principal);
        $this->assertEquals(14.91, $investorInstallment->principal);
        $this->assertEquals(1.35, $investorInstallment->interest);
        $this->assertEquals(59, $investorInstallment->days);

        $investorInstallment = (object) $investorInstallments[1]->toArray();
        $this->assertEquals(35.09, $investorInstallment->remaining_principal);
        $this->assertEquals(16.60, $investorInstallment->principal);
        $this->assertEquals(0.50, $investorInstallment->interest);
        $this->assertEquals(31, $investorInstallment->days);

        $investorInstallment = (object) $investorInstallments[2]->toArray();
        $this->assertEquals(18.49, $investorInstallment->remaining_principal);
        $this->assertEquals(18.49, $investorInstallment->principal);
        $this->assertEquals(0.25, $investorInstallment->interest);
        $this->assertEquals(30, $investorInstallment->days);

        return [$loan, $investor];
    }

    /**
     * @depends testInvest
     */
    public function testDailyInterestChanges($data)
    {
        list($loan, $investor) = $data;

        $items = new Collection;
        $items->push($loan);

        $datesAccruedInterest = [
            '2021-01-30' => '0.18',
            '2021-02-10' => '0.43',
            '2021-02-25' => '0.78',
            '2021-03-20' => '1.30',
            '2021-03-22' => '1.35',
        ];
        foreach ($datesAccruedInterest as $dueDate => $accruedInterest) {
            $updatedLoans = $this->loanService->recalcInterest(
                $items,
                Carbon::parse($dueDate)
            );

            $investorInstallment = $loan->investorInstallments();
            $this->assertEquals(3, $investorInstallment->count());

            $firstInvestorInstallment = $investorInstallment->first();
            $this->assertEquals($accruedInterest, $firstInvestorInstallment->accrued_interest);
            $this->assertEquals(0, $firstInvestorInstallment->late_interest);
        }

        $datesLateInterest = [
            '2021-03-25' => '0.02',
            '2021-03-30' => '0.05',
            '2021-04-10' => '0.13',
            '2021-04-20' => '0.20',
        ];
        foreach ($datesLateInterest as $dueDate => $lateInterest) {
            $updatedLoans = $this->loanService->recalcInterest(
                $items,
                Carbon::parse($dueDate)
            );

            $investorInstallment = $loan->investorInstallments();
            $this->assertEquals(3, $investorInstallment->count());

            $firstInvestorInstallment = $investorInstallment->first();
            $this->assertEquals($lateInterest, $firstInvestorInstallment->late_interest);
        }

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
        int $prepaidInstallments = 0,
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
        $loan->prepaid_schedule_payments = $prepaidInstallments;
        $loan->period = $period;
        $loan->overdue_days = $overdueDays;
        $loan->amount = $amount;
        $loan->amount_afranga = $amountAfranga;
        $loan->status = Loan::STATUS_NEW;
        $loan->pin = 4412010476;

        return $loan;
    }

    public function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });

        parent::tearDown();
    }
}
