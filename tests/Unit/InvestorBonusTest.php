<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\App;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\Task;
use Modules\Common\Repositories\InvestorRepository;
use Modules\Common\Repositories\TaskRepository;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestorBonusService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class InvestorBonusTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    protected $investorService;
    protected $importService;
    protected $investService;
    protected $investorBonusService;
    protected $investorRepository;
    protected $taskRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->importService = App::make(ImportService::class);
        $this->investService = App::make(InvestService::class);
        $this->investorBonusService = App::make(InvestorBonusService::class);
        $this->investorService = App::make(InvestorService::class);
        $this->investorRepository = App::make(InvestorRepository::class);
        $this->taskRepository = App::make(TaskRepository::class);
    }

    public function testInvestorBonuses()
    {
        // Create Loan
        $currencyId = Currency::ID_EUR;
        $loanAmount = 500000.46;
        $remainingPricipal = 500000.46;
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
        $investor = $this->getTestInvestor('investor_bonus5' . time() . '@investorBonus.com');
        $wallet = $this->getInvestorWallet($investor->investor_id, $investorDeposit, $currencyId);
        $portfolios = $this->getInvestorPortfolios($investor->investor_id);


        $referrals = [];

        for ($i = 1; $i <= 5; $i++) {
            $referrals[] = $this->getReferralsBonus(
                'investor_bonus' . $i . time() . '@investorsBonus.com',
                $investor->investor_id
            );
        }

        $totalInvested = 0;

        foreach ($referrals as $referral) {
            $walletReferrals = $this->getInvestorWallet($referral->investor_id, $investorDeposit, $currencyId);
            $portfoliosReferrals = $this->getInvestorPortfolios($referral->investor_id);

            // do invest
            $investorBuyAmountReferrals = 500;
            $totalInvested += $investorBuyAmountReferrals;
            $now = Carbon::parse($issueDate);
            $invested = $this->investService->doInvest(
                $investorBuyAmountReferrals,
                $referral,
                $walletReferrals,
                $loan,
                $installments,
                $now
            );

            $this->assertEquals(true, $invested);
        }

        // call command to create records into invest_bonus table
        \Artisan::call('script:bonus:prepare');

        // take all records from investor bonus table with param investorId
        $investorsForBonus = $this->investorBonusService->getById($investor->investor_id);
        $numberReferrals = count($investorsForBonus);
        $i = 0;

        foreach ($investorsForBonus as $investorForBonus) {
            $investorBonus = $investorForBonus;
            $this->assertEquals($investorForBonus->investor_id, $investorBonus->investor_id);
            $this->assertEquals($investorForBonus->created_at, $investorBonus->created_at);
            $bonusAmount = $investorForBonus->amount;
            $this->assertLessThan(Setting::BONUS_MAX_AMOUNT, $bonusAmount);
            $this->assertEquals(
                Carbon::parse($investorForBonus->created_at)->addDays(2)->format('Y-m-d'),
                Carbon::parse($investorForBonus->date)->format('Y-m-d')
            );
            $i++;
        }

        $bonusForInvestor = $investorsForBonus->sum('amount');
        $this->assertEquals(
            $bonusForInvestor,
            $this->investorRepository->calculateBonus(Setting::BONUS_PERCENT, $totalInvested)
        );

        $this->assertEquals($numberReferrals, $i);

        // call command to creating task for bonus
        \Artisan::call('script:bonus:handle');

        // get all tasks for bonus
        $tasksForBonus = $this->taskRepository->getTaskByInvestorId($investorsForBonus[1]->investor_id);
        $numberTasksForBonus = count($tasksForBonus);
        $y = 0;

        foreach ($tasksForBonus as $taskForBonus) {
            $this->assertEquals(Task::TASK_TYPE_BONUS_PAYMENT, $taskForBonus->task_type);
            $this->assertEquals(Task::TASK_STATUS_NEW, $taskForBonus->status);
            $this->assertLessThan(Setting::BONUS_MAX_AMOUNT, $taskForBonus->amount);
            $this->assertEquals($taskForBonus->amount, $investorBonus->amount);
            $this->assertEquals($taskForBonus->investor_id, $investor->investor_id);

            $y++;
        }

        $this->assertEquals($numberTasksForBonus, $y);

        // remove loan test data from db
        $this->removeTestData($investor, $loan);

        // remove investors referrals from db
        foreach ($referrals as $referral) {
            $this->removeTestData($referral);
        }
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

    protected function getInstallmentsAfterInsert(
        $loan,
        array $import,
        $fromDate = null
    ) {
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

    protected function getTestInvestor(
        string $email = 'investor1@test.com'
    ) {
        $obj = Investor::where('email', $email)->first();
        if (!empty($obj)) {
            return $obj;
        }

        $investor = new Investor;
        $investor->email = $email;
        $investor->first_name = 'Test Investor';
        $investor->last_name = 'Investor Test';
        $investor->comment = 'test user for unit test';
        $investor->type = 'individual';
        $investor->status = 'verified';
        $investor->deleted = '0';
        $investor->save();

        $row = new BankAccount();
        $row->investor_id = $investor->investor_id;
        $row->iban = 'BG004100410041' . time();
        $row->save();

        $investor->refresh();

        return $investor;
    }

    protected function getReferralsBonus(
        string $email = 'investor@test.com',
        int $referralId
    ) {
        $obj = Investor::where('email', $email)->first();
        if (!empty($obj)) {
            return $obj;
        }

        $investorReferral = new Investor;
        $investorReferral->email = $email;
        $investorReferral->first_name = 'Test Investor Bonus';
        $investorReferral->last_name = 'Investor Test Bonus';
        $investorReferral->comment = 'test user for unit test';
        $investorReferral->type = 'individual';
        $investorReferral->status = 'verified';
        $investorReferral->referral_id = $referralId;
        $investorReferral->deleted = '0';
        $investorReferral->created_at = Carbon::today()->subDays(Setting::BONUS_DAYS_COUNT_FOR_CHECK)->format('Y-m-d');

        $investorReferral->save();

        $row = new BankAccount();
        $row->investor_id = $investorReferral->investor_id;
        $row->iban = 'BG004100410041' . time();
        $row->save();

        $investorReferral->refresh();

        return $investorReferral;
    }

    protected function getInvestorPortfolios(
        int $investorId,
        int $currencyId = Currency::ID_EUR
    ): array {
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
}
