<?php

namespace Tests\Unit;

use App;
use Artisan;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Log;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Wallet;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\AutoInvestService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestmentService;
use Modules\Common\Services\InvestService;
use Modules\Common\Services\InvestStrategyService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;
use Throwable;

class LoanInvestFlowTest extends TestCase
{
    public const LOAN_PREPARE_NUMBER = 20;
    public const INVEST_STRATEGY_RUNS = 5;

    use TestDataTrait;
    use WithoutMiddleware;

    protected $importService;
    protected $investService;
    protected $investmentService;
    protected $investStrategyService;
    protected $autoInvestService;
    protected $investorIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importService = App::make(ImportService::class);
        $this->investService = App::make(InvestService::class);
        $this->investmentService = App::make(InvestmentService::class);
        $this->investStrategyService = App::make(InvestStrategyService::class);
        $this->autoInvestService = App::make(AutoInvestService::class);
    }

    /**
     * @throws Throwable
     * test Loan Invest Flow Single Buy
     */
    public function testLoanInvestFlowSingleBuy(): array
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $wallet = $this->prepareWallet($investor, 100000);
        $portfolios = $this->preparePortfolios($investor);

        // prepare test data
        $currencyId = Currency::ID_EUR;
        $issueDate = Carbon::today()->format('Y-m-d');
        $listingDate = Carbon::today()->format('Y-m-d');

        for ($i = 1; $i <= self::LOAN_PREPARE_NUMBER; $i++) {
            $rand = rand(100.00, 200.00) * $i;
            $loan = $this->preapreLoan(
                $rand,
                $rand,
                $rand,
                2 * $i,
                10, // originator percent
                2 * $i, // count of periods
                $currencyId,
                $issueDate,
                $listingDate,
                Carbon::today()->addMonths($i)->addDays($i * 2)->format('Y-m-d')
            );
            $this->prepareInstallments($loan);
            $loansPrepare[] = $loan->refresh();
        }

        foreach ($loansPrepare as $loan) {
            $rand = rand(10.00, 50.00);

            self::invest($rand, $loan, $investor, $wallet);

            $loan->refresh();

            $investmentAmount[$loan->loan_id] = 0;
            $investmentPercent[$loan->loan_id] = 0;

            foreach ($loan->investments as $investment) {
                $investmentAmount[$investment->loan_id] += $investment->amount;
                $investmentPercent[$investment->loan_id] += $investment->percent;
            }

            Log::channel('unit_test')->info(
                'Single Buy amount_invested #' . $investmentAmount[$loan->loan_id]
                . ' loan id # ' . $loan->loan_id
                . ' amount_available #' . $loan->amount_available
                . ' amount_precent #' . $investmentPercent[$loan->loan_id]
            );

            $this->assertEquals(
                (Calculator::round(
                        $loan->amount_afranga - ($loan->amount_afranga * 0.1)
                    ) - $investmentAmount[$loan->loan_id]),
                $loan->amount_available
            );
        }
        $data['investors'][] = $investor;
        $data['loans'][] = $loansPrepare;
        return $data;
    }

    /**
     * @depends testLoanInvestFlowSingleBuy
     *
     * @param $data
     *
     * test Loan Invest Flow Mass Invest
     * @throws Throwable
     */
    public function testLoanInvestFlowMassInvest($data): array
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $wallet = $this->prepareWallet($investor, 1000000);
        $portfolios = $this->preparePortfolios($investor);

        // prepare test data
        $currencyId = Currency::ID_EUR;
        $issueDate = Carbon::today()->format('Y-m-d');
        $listingDate = Carbon::today()->format('Y-m-d');

        for ($i = 1; $i <= self::LOAN_PREPARE_NUMBER; $i++) {
            $loan = $this->preapreLoan(
                100 * $i,
                100 * $i,
                100 * $i,
                2 * $i,
                10, // originator percent
                2 * $i, // count of periods
                $currencyId,
                $issueDate,
                $listingDate,
                Carbon::today()->addMonths($i)->addDays($i * 2)->format('Y-m-d')
            );
            $this->prepareInstallments($loan);
            $loansPrepare[] = $loan->refresh();;
        }

        self::massInvest(0, rand(10.00, 30.00), $investor->investor_id, 20);

        $investmentAmount = [];

        foreach ($loansPrepare as $loan) {
            $loan->refresh();

            Log::channel('unit_test')->info(
                'Mass Invest loan id # ' . $loan->loan_id
                . ' amount_available #' . $loan->amount_available
            );

            $investmentAmount[$loan->loan_id] = 0;
            $investmentPercent[$loan->loan_id] = 0;

            foreach ($loan->investments as $investment) {
                $investmentAmount[$investment->loan_id] += $investment->amount;
                $investmentPercent[$investment->loan_id] += $investment->percent;
            }

            Log::channel('unit_test')->info(
                'Mass Invest Investment #' . $investmentAmount[$loan->loan_id]
                . ' loan id # ' . $loan->loan_id
                . ' amount_available #' . $loan->amount_available
                . ' percent #' . $investmentPercent[$loan->loan_id]
            );

            $this->assertEquals(
                (Calculator::round(
                        $loan->amount_afranga - ($loan->amount_afranga * 0.1)
                    ) - $investmentAmount[$loan->loan_id]),
                $loan->amount_available
            );
        }

        $data['investors'][] = $investor;
        $data['loans'][] = $loansPrepare;
        return $data;
    }

    /**
     * @depends testLoanInvestFlowMassInvest
     *
     * @param $data
     *
     * test Loan Invest Flow Mass Invest Single Buy
     * @throws Throwable
     */
    public function testLoanInvestFlowMassInvestSingleBuy($data): array
    {
        $investor = $this->prepareInvestor('investor_email' . time() . '@test.bg');
        $wallet = $this->prepareWallet($investor, 1000000);
        $portfolios = $this->preparePortfolios($investor);

        // prepare test data
        $currencyId = Currency::ID_EUR;
        $issueDate = Carbon::today()->format('Y-m-d');
        $listingDate = Carbon::today()->format('Y-m-d');

        for ($i = 1; $i <= self::LOAN_PREPARE_NUMBER; $i++) {
            $loan = $this->preapreLoan(
                100 * $i,
                100 * $i,
                100 * $i,
                2 * $i,
                10, // originator percent
                2 * $i, // count of periods
                $currencyId,
                $issueDate,
                $listingDate,
                Carbon::today()->addMonths($i)->addDays($i * 2)->format('Y-m-d')
            );
            $this->prepareInstallments($loan);
            $loansPrepare[] = $loan->refresh();
        }

        self::massInvest(0, rand(10.00, 30.00), $investor->investor_id, 30);

        $investmentAmount = [];

        foreach ($loansPrepare as $loan) {
            $loan->refresh();

            $rand = rand(10.00, 50);

            self::invest($rand, $loan, $investor, $wallet);

            Log::channel('unit_test')->info(
                'Mass Invest loan id # ' . $loan->loan_id
                . ' amount_available #' . $loan->amount_available
            );

            $investmentAmount[$loan->loan_id] = 0;
            $investmentPercent[$loan->loan_id] = 0;

            foreach ($loan->investments as $investment) {
                $investmentAmount[$investment->loan_id] += $investment->amount;
                $investmentPercent[$investment->loan_id] += $investment->percent;
            }

            Log::channel('unit_test')->info(
                'Mass Invest Investment #' . $investmentAmount[$loan->loan_id]
                . ' loan id # ' . $loan->loan_id
                . ' amount_available #' . $loan->amount_available
                . ' percent #' . $investmentPercent[$loan->loan_id]
            );

            $this->assertEquals(
                (Calculator::round(
                        $loan->amount_afranga - ($loan->amount_afranga * 0.1)
                    ) - $investmentAmount[$loan->loan_id]),
                $loan->amount_available
            );
        }

        $data['investors'][] = $investor;
        $data['loans'][] = $loansPrepare;
        return $data;
    }

    /**
     * @depends testLoanInvestFlowMassInvestSingleBuy
     *
     * @param $data
     *
     * test Loan Invest Flow Mass Invest Single Buy
     * @throws Throwable
     */
    public function testLoanInvestFlowAutoInvestStrategy($data)
    {
        $investor = $this->prepareInvestor('investor_emails' . time() . '@test.bg');
        $wallet = $this->prepareWallet($investor, 1000000);
        $portfolios = $this->preparePortfolios($investor);

        // prepare test data
        $currencyId = Currency::ID_EUR;
        $issueDate = Carbon::today()->format('Y-m-d');
        $listingDate = Carbon::today()->format('Y-m-d');

        for ($i = 1; $i <= self::LOAN_PREPARE_NUMBER; $i++) {
            $rand = rand(100.00, 200.00) * $i;
            $loan = $this->preapreLoan(
                $rand,
                $rand,
                $rand,
                2 * $i,
                10, // originator percent
                2 * $i, // count of periods
                $currencyId,
                $issueDate,
                $listingDate,
                Carbon::today()->addMonths($i)->addDays($i * 2)->format('Y-m-d')
            );
            $this->prepareInstallments($loan);
            $loansPrepare[] = $loan->refresh();
        }

        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, true);

        $this->isScriptRunning($investor);

        $investments = Investment::where('investor_id', $investor->getId())->get();

        $this->assertNotEmpty($investments);

        $investmentsSum = 0;
        $investStrategy->refresh();
        foreach ($investments as $investment) {
            $investmentLoan = $investment->loan;

            $period = Carbon::today()->diffInMonths($investmentLoan->final_payment_date);

            $this->assertGreaterThanOrEqual($investStrategy->min_loan_period, $period);
            $this->assertLessThanOrEqual($investStrategy->max_loan_period, $period);
            $this->assertGreaterThanOrEqual($investStrategy->min_amount, $investment->amount);
            $this->assertLessThanOrEqual($investStrategy->max_amount, $investment->amount);
            $this->assertGreaterThanOrEqual($investStrategy->min_interest_rate, $investmentLoan->interest_rate_percent);
            $this->assertLessThanOrEqual($investStrategy->max_interest_rate, $investmentLoan->interest_rate_percent);

            $investmentsSum += $investment->amount;
        }

        $this->assertEquals($investmentsSum, $investStrategy->portfolio_size);

        for ($i = 1; $i <= self::INVEST_STRATEGY_RUNS; $i++) {
            $this->prepareInvestStrategy($investor, $wallet, true);
            $this->isScriptRunning($investor);
        }

        $data['investors'][] = $investor;
        $data['loans'][] = $loansPrepare;
        $this->removeData($data);
    }

    /**
     * @param $data
     */
    public function removeData($data)
    {
        Artisan::call('script:loans:bad-invested-amount');

        foreach ($data['investors'] as $investor) {
            $this->removeTestData($investor);
        }

        foreach ($data['loans'] as $loansPrepare) {
            foreach ($loansPrepare as $loan) {
                $this->removeTestData(null, $loan);
            }
        }
    }

    /**
     * @param $amount
     * @param $loan
     * @param $investor
     * @param $wallet
     * @return mixed
     * @throws Throwable
     */
    public function invest($amount, $loan, $investor, $wallet)
    {
        $this->investService->doInvest(
            $amount,
            $investor,
            $wallet,
            $loan,
            $loan->installments(),
            Carbon::now()
        );

        $loan->refresh();

        if ($loan->amount_available > 0 && $amount < $loan->amount_available) {
            return $this->invest(rand(10, 100), $loan, $investor, $wallet);
        }

        if ($loan->amount_available > 0 && $loan->amount_available > 10.00 && $amount > $loan->amount_available) {
            return $this->invest($loan->amount_available, $loan, $investor, $wallet);
        }
    }

    /**
     * @param $count
     * @param $amount
     * @param $investorId
     * @param $countToBy
     * @return mixed
     */
    public function massInvest($count, $amount, $investorId, $countToBy)
    {
        $this->investmentService->massInvestByAmountAndFilters($investorId, $amount, [], $countToBy);
        sleep(5);
        if ($count < 10) {
            Log::channel('unit_test')->info(
                'Mass Invest Investment count#' . $count
            );
            $count++;
            return $this->massInvest($count, rand(10.00, 100.00), $investorId, $countToBy);
        }
    }


    /**
     * @param Investor $investor
     * @param Wallet $wallet
     * @param bool $reinvest
     * @return InvestStrategy
     */
    public function prepareInvestStrategy(
        Investor $investor,
        Wallet $wallet,
        bool $reinvest
    ): InvestStrategy {
        $investStrategy = new InvestStrategy();
        $investStrategy->investor_id = $investor->getId();
        $investStrategy->wallet_id = $wallet->getId();
        $investStrategy->name = 'Unit test';
        $investStrategy->priority = 1;
        $investStrategy->min_amount = 10;
        $investStrategy->max_amount = 200;
        $investStrategy->min_interest_rate = 1;
        $investStrategy->max_interest_rate = 100;
        $investStrategy->min_loan_period = 1;
        $investStrategy->max_loan_period = 100;
        $investStrategy->portfolio_size = 0;
        $investStrategy->max_portfolio_size = 500000;
        $investStrategy->loan_type = json_encode(['type' => ['installments']]);
        $investStrategy->loan_payment_status = json_encode(['payment_status' => ['current']]);
        $investStrategy->agreed = 1;
        $investStrategy->reinvest = (int)$reinvest;
        $investStrategy->save();

        $investStrategy->refresh();

        return $investStrategy;
    }

    /**
     * @param $investor
     * @return mixed
     */
    public function isScriptRunning($investor)
    {
        sleep(5);
        $bunch = $investor->getInvestmentBunch();
        if (!empty($bunch->investment_bunch_id) && 0 == $bunch->finished) {
            return self::isScriptRunning($investor);
        }
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
