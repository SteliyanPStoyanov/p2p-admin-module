<?php

namespace Tests\Unit\AutoInvest;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Modules\Common\Console\AutoInvest;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\Wallet;
use Modules\Common\Services\AutoInvestService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Modules\Common\Services\InvestStrategyService;
use Modules\Common\Services\LoanService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class InvestStrategyTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    protected $investService;
    protected $investStrategyService;
    protected $importService;
    protected $autoInvestService;

    public function setUp(): void
    {
        parent::setUp();

        $this->importService = new ImportService;
        $this->investService = new InvestService;
        $this->investStrategyService = App::make(InvestStrategyService::class);
        $this->autoInvestService = App::make(AutoInvestService::class);
    }

    public function testInvestStrategy()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $issueDate = '2020-11-02';
        $listingDate = '2020-11-02';
        $lengthLimit = 10;

        $loansPrepare = [];
        for ($i = 1; $i <= 10; $i++) {
            $loansPrepare[] = $this->preapreLoan(
                30 * $i,
                30 * $i,
                20 * $i,
                2 * $i,
                10, // originator percent
                2 * $i, // count of periods
                $currencyId,
                $issueDate,
                $listingDate,
                Carbon::parse('2021-4-20')->addDays($i * 2)->format('Y-m-d')
            );
        }


        $investorDeposit = 1000;
        $investor = $this->prepareTestInvestor('investor2_' . time() . '@test2.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);
        $investStrategy = $this->prepareInvestStrategy($investor, $wallet);

        $filters = $this->autoInvestService->getFiltersFromStrategy($investStrategy);

        $payment_status['payment_status'][0] = Portfolio::getQualityMapping($filters['payment_status'][0], false);

        $this->assertEquals($filters['amount_available']['from'], $investStrategy->min_amount);
        $this->assertEquals($filters['amount_available']['to'], $investStrategy->max_amount);
        $this->assertEquals($filters['interest_rate_percent']['from'], $investStrategy->min_interest_rate);
        $this->assertEquals($filters['interest_rate_percent']['to'], $investStrategy->max_interest_rate);
        $this->assertEquals($filters['period']['from'], $investStrategy->min_loan_period);
        $this->assertEquals($filters['period']['to'], $investStrategy->max_loan_period);
        $this->assertEquals($filters['loan'], json_decode($investStrategy->loan_type, true));
        $this->assertEquals($payment_status, json_decode($investStrategy->loan_payment_status, true));

        $loans = (App::make(LoanService::class))->getLoansForSite($lengthLimit,$filters);

        $this->assertEquals($payment_status, json_decode($investStrategy->loan_payment_status, true));

        $periodFrom =  Carbon::now()->addMonths($filters['period']['from'])->format('Y-m-d');
        $periodTo =  Carbon::now()->addMonths($filters['period']['to'])->format('Y-m-d');

        foreach ($loans as $loan) {
            $this->assertGreaterThan($filters['amount_available']['from'], $loan->amount_available);
            $this->assertLessThan($filters['amount_available']['to'], $loan->amount_available);
            $this->assertGreaterThan($filters['interest_rate_percent']['from'], $loan->interest_rate_percent);
            $this->assertLessThanOrEqual($filters['interest_rate_percent']['to'], $loan->interest_rate_percent);
            $this->assertGreaterThan($periodFrom, $loan->final_payment_date);
            $this->assertLessThan($periodTo,$loan->final_payment_date);
            $this->assertContains(
                Portfolio::getQualityMapping($loan->payment_status, true),
                $filters['payment_status']
            );
            $this->assertContains($loan->type, $filters['loan']['type']);
        }

        foreach ($loansPrepare as $loanRemove) {
            $this->removeTestData($investor, $loanRemove);
        }
    }

    protected function prepareTestInvestor(string $email = 'investor1@test.com')
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

    public function prepareInvestStrategy(
        Investor $investor,
        Wallet $wallet
    ) {
        $investStrategy = new InvestStrategy();
        $investStrategy->investor_id = $investor->investor_id;
        $investStrategy->wallet_id = $wallet->wallet_id;
        $investStrategy->name = 'Unit test';
        $investStrategy->priority = 1;
        $investStrategy->min_amount = 10;
        $investStrategy->max_amount = 200;
        $investStrategy->min_interest_rate = 1;
        $investStrategy->max_interest_rate = 30;
        $investStrategy->min_loan_period = 1;
        $investStrategy->max_loan_period = 30;
        $investStrategy->portfolio_size = 5000;
        $investStrategy->loan_type = json_encode(['type' => ['installments']]);
        $investStrategy->loan_payment_status = json_encode(['payment_status' => ['current']]);
        $investStrategy->agreed = 1;
        $investStrategy->save();

        $investStrategy->refresh();
        return $investStrategy;
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
