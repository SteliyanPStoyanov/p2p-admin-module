<?php

namespace Tests\Unit\Loan;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\BankAccount;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\Wallet;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\AutoInvestService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class LoanAmountToBuyTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    protected $investService;
    protected $autoInvestService;

    public function setUp(): void
    {
        parent::setUp();

        $this->importService = new ImportService;
        $this->investService = new InvestService;
        $this->autoInvestService = App::make(AutoInvestService::class);
    }

    public function testAmountToBuy()
    {
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


        $investorDeposit = 1000;
        $investor = $this->prepareTestInvestor('investor2_' . time() . '@test2.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);

        $maxAmount = 200;

        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, $maxAmount);
        $investmentBunch = $this->prepareInvestmentBunch($investor, $investStrategy);

        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $amountToBuyNoStrategy = $this->investService->getAmountToBuy(
            $investmentBunch,
            $loan,
            $wallet,
            null
        );

        $this->assertEquals($amountToBuyNoStrategy, $investmentBunch->amount);

        $this->assertEquals($amountToBuy, $loan->amount_available);

        $this->removeTestData($investor, $loan);
    }

    public function testAmountToBuyNoMaxAmount()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $loanAmount = 500;
        $remainingPricipal = 500;
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


        $investorDeposit = 1000;
        $investor = $this->prepareTestInvestor('investor2_' . time() . '@test2.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);

        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, null);
        $investmentBunch = $this->prepareInvestmentBunch($investor, $investStrategy);

        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $this->assertEquals($amountToBuy, $loan->amount_available);

        $this->removeTestData($investor, $loan);
    }

    public function testAmountToBuyMinMaxAmount()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $loanAmount = 100;
        $remainingPricipal = 100;
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


        $investorDeposit = 1000;
        $investor = $this->prepareTestInvestor('investor2_' . time() . '@test2.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);

        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, 50, 10);
        $investmentBunch = $this->prepareInvestmentBunch($investor, $investStrategy);

        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $this->assertEquals($amountToBuy, $loan->amount_available);


        $investStrategyMinMax = $this->prepareInvestStrategy($investor, $wallet, 20, 10);

        $filters = $this->autoInvestService->getFiltersFromStrategy($investStrategyMinMax);

        $investmentBunchMinMax = $this->prepareInvestmentBunch($investor, $investStrategy ,$filters);

        $amountToBuyMinMax = $this->investService->getAmountToBuy(
            $investmentBunchMinMax->refresh(),
            $loan,
            $wallet,
            $investStrategyMinMax->refresh()
        );

        $this->assertEquals($amountToBuyMinMax, $investStrategyMinMax->max_amount);
        $this->assertGreaterThan($amountToBuyMinMax, $loan->amount_available);

        $this->removeTestData($investor, $loan);
    }

    public function testMin10Max50Loan45()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        // +10 Percent so 90% we can buy
        $loanAmount = Calculator::toBgn(50);
        $remainingPricipal = Calculator::toBgn(50);
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

        $investorDeposit = 1000;
        $investor = $this->prepareTestInvestor('investor2_' . time() . '@test2.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);

        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, 50, 10);
        $investmentBunch = $this->prepareInvestmentBunch(
            $investor,
            $investStrategy,
            $this->autoInvestService->getFiltersFromStrategy($investStrategy)
        );
        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $this->assertEquals($amountToBuy, $loan->amount_available);

        $investStrategy->max_portfolio_size = $investStrategy->portfolio_size;
        $investStrategy->save();
        $investStrategy->refresh();
        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $this->assertEquals(null, $amountToBuy);

        $this->removeTestData($investor, $loan);
    }

    public function testMin10Max20Loan45()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        // +10 Percent so 90% we can buy
        $loanAmount = Calculator::toBgn(50);
        $remainingPricipal = Calculator::toBgn(50);
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

        $investorDeposit = 1000;
        $investor = $this->prepareTestInvestor('investor2_' . time() . '@test2.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);

        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, 20, 10);
        $investmentBunch = $this->prepareInvestmentBunch(
            $investor,
            $investStrategy,
            $this->autoInvestService->getFiltersFromStrategy($investStrategy)
        );
        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $this->assertEquals(20, $amountToBuy);

        $investStrategy->max_portfolio_size = $investStrategy->portfolio_size;
        $investStrategy->save();
        $investStrategy->refresh();
        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $this->assertEquals(null, $amountToBuy);

        $this->removeTestData($investor, $loan);
    }

    public function testMin10Max50Loan45Wallet30()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        // +10 Percent so 90% we can buy
        $loanAmount = Calculator::toBgn(50);
        $remainingPricipal = Calculator::toBgn(50);
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

        $investorDeposit = 30;
        $investor = $this->prepareTestInvestor('investor2_' . time() . '@test2.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);

        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, 50, 10);
        $investmentBunch = $this->prepareInvestmentBunch(
            $investor,
            $investStrategy,
            $this->autoInvestService->getFiltersFromStrategy($investStrategy)
        );

        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);
        $this->assertEquals(30, $amountToBuy);

        $investStrategy->max_portfolio_size = $investStrategy->portfolio_size;
        $investStrategy->save();
        $investStrategy->refresh();
        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $this->assertEquals(null, $amountToBuy);

        $this->removeTestData($investor, $loan);
    }

    public function testMin10Max10Loan45()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        // +10 Percent so 90% we can buy
        $loanAmount = Calculator::toBgn(50);
        $remainingPricipal = Calculator::toBgn(50);
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

        $investorDeposit = 1000;
        $investor = $this->prepareTestInvestor('investor2_' . time() . '@test2.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);

        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, 10, 10);
        $investmentBunch = $this->prepareInvestmentBunch(
            $investor,
            $investStrategy,
            $this->autoInvestService->getFiltersFromStrategy($investStrategy)
        );

        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $this->assertEquals(10, $amountToBuy);

        $investStrategy->max_portfolio_size = $investStrategy->portfolio_size;
        $investStrategy->save();
        $investStrategy->refresh();
        $amountToBuy = $this->investService->getAmountToBuy($investmentBunch, $loan, $wallet, $investStrategy);

        $this->assertEquals(null, $amountToBuy);

        $this->removeTestData($investor, $loan);
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

    public function prepareInvestmentBunch(
        Investor $investor,
        InvestStrategy $investStrategy,
        $filter = null
    ) {
        $jsonFilter = $filter ? $filter : '[]';

        $investmentBunch = new InvestmentBunch;
        $investmentBunch->count = 10;
        $investmentBunch->investor_id = $investor->investor_id;
        $investmentBunch->amount = 20;
        $investmentBunch->invest_strategy_id = $investStrategy->invest_strategy_id;
        $investmentBunch->filters = json_encode($jsonFilter);
        $investmentBunch->finished = 0;

        $investmentBunch->save();

        $investmentBunch->refresh();
        return $investmentBunch;
    }


    public function prepareInvestStrategy(
        Investor $investor,
        Wallet $wallet,
        int $maxAmount = null,
        int $minAmount = null
    ) {
        $investStrategy = new InvestStrategy();
        $investStrategy->investor_id = $investor->investor_id;
        $investStrategy->wallet_id = $wallet->wallet_id;
        $investStrategy->name = 'Unit test';
        $investStrategy->priority = 1;
        $investStrategy->min_amount = $minAmount ? $minAmount : 10;
        if ($maxAmount) {
            $investStrategy->max_amount = $maxAmount;
        }
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
