<?php

namespace Tests\Unit\AutoInvest;

use Artisan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\RepaidInstallment;
use Modules\Common\Entities\RepaidLoan;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Common\Services\AutoInvestService;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Modules\Common\Services\InvestStrategyService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class AutoInvestStrategyTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    protected $investService;
    protected $investStrategyService;
    protected $autoInvestService;
    protected $importService;
    protected $distributeService;

    public function setUp(): void
    {
        parent::setUp();

        $this->investService = App::make(InvestService::class);
        $this->investStrategyService = App::make(InvestStrategyService::class);
        $this->autoInvestService = App::make(AutoInvestService::class);
        $this->importService = App::make(ImportService::class);
        $this->distributeService = App::make(DistributeService::class);
    }

    public function testInvestStrategyWithReinvest()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $issueDate = Carbon::today()->format('Y-m-d');
        $listingDate = Carbon::today()->format('Y-m-d');

        $loansPrepare = [];
        for ($i = 1; $i <= 10; $i++) {
            $loan = $this->preapreLoan(
                30 * $i,
                30 * $i,
                20 * $i,
                2 * $i,
                10, // originator percent
                2 * $i, // count of periods
                $currencyId,
                $issueDate,
                $listingDate,
                Carbon::today()->addMonths($i)->addDays($i * 2)->format('Y-m-d')
            );
            $this->prepareInstallmentsWithStartDate($loan, $loan->created_at->addMonth());
            $loan->refresh();
            $loansPrepare[] = $loan;
        }


        $investorDeposit = 700;
        $investor = $this->prepareInvestor('investor_invest_strategy' . time() . '@test.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);
        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, true);

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
            $this->assertEquals($investmentLoan->type, json_decode($investStrategy->loan_type)->type[0]);
            $this->assertEquals(
                $investmentLoan->payment_status,
                json_decode($investStrategy->loan_payment_status)->payment_status[0]
            );

            $investmentsSum += $investment->amount;
        }

        $this->assertEquals($investmentsSum, $investStrategy->portfolio_size);

        $oldUninvested = $wallet->uninvested;
        $wallet->refresh();
        $this->assertEquals($investmentsSum, $wallet->invested);
        $this->assertEquals($oldUninvested - $investmentsSum, $wallet->uninvested);

        // Test repaid installment
        $investorInstallments = $investments[0]->getInvestorInstallments();
        $investorInstallment = $investorInstallments[0];

        $investorInstallment->accrued_interest = 3.44;
        $investorInstallment->interest = 4.55;
        $investorInstallment->save();
        $investorInstallment->refresh();

        $repaidInstallment = new RepaidInstallment();
        $repaidInstallment->handled = 0;
        $repaidInstallment->lender_id = $investorInstallment->loan()->lender_id;
        $repaidInstallment->lender_installment_id = $investorInstallment->installment()->lender_installment_id;
        $repaidInstallment->save();

        $repaymentDate = Carbon::today();
        $distributed = $this->distributeService->distributeInstallment(
            $repaidInstallment,
            $repaymentDate
        );
        $this->assertEquals($distributed, true);

        $oldUninvested = $wallet->uninvested;
        $oldInvested = $wallet->invested;
        $oldIncome = $wallet->income;
        $oldInterest = $wallet->interest;
        $oldTotalAmount = $wallet->total_amount;
        $wallet->refresh();
        $incomeFromInstallment = $investorInstallment->principal + $investorInstallment->accrued_interest; // Because is < than interest

        $this->assertEquals($investorInstallment->accrued_interest + $oldTotalAmount, $wallet->total_amount);
        $this->assertEquals($incomeFromInstallment + $oldUninvested, $wallet->uninvested);
        $this->assertEquals($oldInvested - $investorInstallment->principal, $wallet->invested);
        $this->assertEquals($oldIncome + $investorInstallment->accrued_interest, $wallet->income);
        $this->assertEquals($oldInterest + $investorInstallment->accrued_interest, $wallet->interest);

        $oldPortfolioSize = $investStrategy->portfolio_size;
        $oldPortfolioTotalReceived = $investStrategy->total_received;
        $investStrategy->refresh();
        $this->assertEquals($oldPortfolioSize - $investorInstallment->principal, $investStrategy->portfolio_size);
        $this->assertEquals(
            $oldPortfolioTotalReceived + $investorInstallment->principal,
            $investStrategy->total_received
        );

        // Test repaidLoan
        $investorInstallments = $investments[1]->getInvestorInstallments();
        $totalAccruedInterest = 0;
        $totalPrincipal = 0;
        foreach ($investorInstallments as $investorInstallment) {
            $accruedInterest = rand(1, 100) / 100;
            $interest = rand(101, 200) / 100;
            $investorInstallment->accrued_interest = $accruedInterest;
            $investorInstallment->interest = $interest;
            $investorInstallment->save();
            $investorInstallment->refresh();

            if (Carbon::today()->gte($investorInstallment->installment()->due_date)) {
                $totalAccruedInterest += $interest;
            } else {
                $totalAccruedInterest += $accruedInterest;
            }

            $totalPrincipal += $investorInstallment->principal;
        }

        // emulate repaid loan
        $loanForRepayment = $investments[1]->loan;
        $newRepaidLoan = $this->emulateRepaidLoan($loanForRepayment);

        // do repayment
        $repaymentDate = Carbon::today();
        $distributed = $this->distributeService->distributeLoan($newRepaidLoan, $repaymentDate);
        $this->assertEquals($distributed, true);

        $oldUninvested = $wallet->uninvested;
        $oldInvested = $wallet->invested;
        $oldIncome = $wallet->income;
        $oldInterest = $wallet->interest;
        $oldTotalAmount = $wallet->total_amount;
        $wallet->refresh();
        $incomeFromInstallments = $totalPrincipal + $totalAccruedInterest; // Because is < than interest

        $this->assertEquals($totalAccruedInterest + $oldTotalAmount, $wallet->total_amount);
        $this->assertEquals($incomeFromInstallments + $oldUninvested, $wallet->uninvested);
        $this->assertEquals($oldInvested - $totalPrincipal, $wallet->invested);
        $this->assertEquals($oldIncome + $totalAccruedInterest, $wallet->income);
        $this->assertEquals($oldInterest + $totalAccruedInterest, $wallet->interest);

        $oldPortfolioSize = $investStrategy->portfolio_size;
        $oldPortfolioTotalReceived = $investStrategy->total_received;
        $investStrategy->refresh();
        $this->assertEquals($oldPortfolioSize - $totalPrincipal, $investStrategy->portfolio_size);
        $this->assertEquals($oldPortfolioTotalReceived + $totalPrincipal, $investStrategy->total_received);


        foreach ($loansPrepare as $loanRemove) {
            $this->removeTestData($investor, $loanRemove);
        }
    }

    public function testInvestStrategyWithoutReinvest()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $issueDate = Carbon::today()->format('Y-m-d');
        $listingDate = Carbon::today()->format('Y-m-d');

        $loansPrepare = [];
        for ($i = 1; $i <= 10; $i++) {
            $loan = $this->preapreLoan(
                30 * $i,
                30 * $i,
                20 * $i,
                2 * $i,
                10, // originator percent
                2 * $i, // count of periods
                $currencyId,
                $issueDate,
                $listingDate,
                Carbon::today()->addMonths($i)->addDays($i * 2)->format('Y-m-d')
            );
            $this->prepareInstallmentsWithStartDate($loan, $loan->created_at->addMonth());
            $loan->refresh();
            $loansPrepare[] = $loan;
        }


        $investorDeposit = 700;
        $investor = $this->prepareInvestor('investor_invest_strategy' . time() . '@test.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);
        $investStrategy = $this->prepareInvestStrategy($investor, $wallet, false);

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
            $this->assertEquals($investmentLoan->type, json_decode($investStrategy->loan_type)->type[0]);
            $this->assertEquals(
                $investmentLoan->payment_status,
                json_decode($investStrategy->loan_payment_status)->payment_status[0]
            );

            $investmentsSum += $investment->amount;
        }

        $this->assertEquals($investmentsSum, $investStrategy->portfolio_size);

        $oldUninvested = $wallet->uninvested;
        $wallet->refresh();
        $this->assertEquals($investmentsSum, $wallet->invested);
        $this->assertEquals($oldUninvested - $investmentsSum, $wallet->uninvested);

        // Test repaid installment
        $investorInstallments = $investments[0]->getInvestorInstallments();
        $investorInstallment = $investorInstallments[0];

        $investorInstallment->accrued_interest = 3.44;
        $investorInstallment->interest = 4.55;
        $investorInstallment->save();
        $investorInstallment->refresh();

        $repaidInstallment = new RepaidInstallment();
        $repaidInstallment->handled = 0;
        $repaidInstallment->lender_id = $investorInstallment->loan()->lender_id;
        $repaidInstallment->lender_installment_id = $investorInstallment->installment()->lender_installment_id;
        $repaidInstallment->save();

        $repaymentDate = Carbon::today();
        $distributed = $this->distributeService->distributeInstallment(
            $repaidInstallment,
            $repaymentDate
        );
        $this->assertEquals($distributed, true);

        $oldUninvested = $wallet->uninvested;
        $oldInvested = $wallet->invested;
        $oldIncome = $wallet->income;
        $oldInterest = $wallet->interest;
        $oldTotalAmount = $wallet->total_amount;
        $wallet->refresh();
        $incomeFromInstallment = $investorInstallment->principal + $investorInstallment->accrued_interest; // Because is < than interest

        $this->assertEquals($investorInstallment->accrued_interest + $oldTotalAmount, $wallet->total_amount);
        $this->assertEquals($incomeFromInstallment + $oldUninvested, $wallet->uninvested);
        $this->assertEquals($oldInvested - $investorInstallment->principal, $wallet->invested);
        $this->assertEquals($oldIncome + $investorInstallment->accrued_interest, $wallet->income);
        $this->assertEquals($oldInterest + $investorInstallment->accrued_interest, $wallet->interest);

        $oldPortfolioSize = $investStrategy->max_portfolio_size;
        $oldPortfolioTotalReceived = $investStrategy->total_received;
        $investStrategy->refresh();

        // Portfolio size must not change!
        $this->assertEquals($oldPortfolioSize, $investStrategy->max_portfolio_size);
        $this->assertEquals(
            $oldPortfolioTotalReceived + $investorInstallment->principal,
            $investStrategy->total_received
        );

        // Test repaidLoan
        $investorInstallments = $investments[1]->getInvestorInstallments();
        $totalAccruedInterest = 0;
        $totalPrincipal = 0;
        foreach ($investorInstallments as $investorInstallment) {
            $accruedInterest = rand(1, 100) / 100;
            $interest = rand(101, 200) / 100;
            $investorInstallment->accrued_interest = $accruedInterest;
            $investorInstallment->interest = $interest;
            $investorInstallment->save();
            $investorInstallment->refresh();

            if (Carbon::today()->gte($investorInstallment->installment()->due_date)) {
                $totalAccruedInterest += $interest;
            } else {
                $totalAccruedInterest += $accruedInterest;
            }

            $totalPrincipal += $investorInstallment->principal;
        }

        // emulate repaid loan
        $loanForRepayment = $investments[1]->loan;
        $newRepaidLoan = $this->emulateRepaidLoan($loanForRepayment);

        // do repayment
        $repaymentDate = Carbon::today();
        $distributed = $this->distributeService->distributeLoan($newRepaidLoan, $repaymentDate);
        $this->assertEquals($distributed, true);

        $oldUninvested = $wallet->uninvested;
        $oldInvested = $wallet->invested;
        $oldIncome = $wallet->income;
        $oldInterest = $wallet->interest;
        $oldTotalAmount = $wallet->total_amount;
        $wallet->refresh();
        $incomeFromInstallments = $totalPrincipal + $totalAccruedInterest; // Because is < than interest

        $this->assertEquals($totalAccruedInterest + $oldTotalAmount, $wallet->total_amount);
        $this->assertEquals($incomeFromInstallments + $oldUninvested, $wallet->uninvested);
        $this->assertEquals($oldInvested - $totalPrincipal, $wallet->invested);
        $this->assertEquals($oldIncome + $totalAccruedInterest, $wallet->income);
        $this->assertEquals($oldInterest + $totalAccruedInterest, $wallet->interest);

        $oldPortfolioSize = $investStrategy->max_portfolio_size;
        $oldPortfolioTotalReceived = $investStrategy->total_received;
        $investStrategy->refresh();

        // Portfolio size must not change!
        $this->assertEquals($oldPortfolioSize, $investStrategy->max_portfolio_size);
        $this->assertEquals($oldPortfolioTotalReceived + $totalPrincipal, $investStrategy->total_received);


        foreach ($loansPrepare as $loanRemove) {
            $this->removeTestData($investor, $loanRemove);
        }
    }

    public function testInvestStrategyNotInvestingMoreThanMaxPortfolioSize()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $issueDate = Carbon::today()->format('Y-m-d');
        $listingDate = Carbon::today()->format('Y-m-d');

        $loansPrepare = [];
        for ($i = 1; $i <= 15; $i++) {
            $loan = $this->preapreLoan(
                30 * $i,
                30 * $i,
                20 * $i,
                2 * $i,
                10, // originator percent
                2 * $i, // count of periods
                $currencyId,
                $issueDate,
                $listingDate,
                Carbon::today()->addMonths($i)->addDays($i * 2)->format('Y-m-d')
            );
            $this->prepareInstallmentsWithStartDate($loan, $loan->created_at->addMonth());
            $loan->refresh();
            $loansPrepare[] = $loan;
        }


        $investorDeposit = 700;
        $investor = $this->prepareInvestor('investor_invest_strategy' . time() . '@test.com');
        $wallet = $this->prepareWallet($investor, $investorDeposit, $currencyId);
        $portfolios = $this->preparePortfolios($investor);

        $investStrategy = new InvestStrategy();
        $investStrategy->investor_id = $investor->getId();
        $investStrategy->wallet_id = $wallet->getId();
        $investStrategy->name = 'Unit test';
        $investStrategy->priority = 1;
        $investStrategy->min_amount = 10;
        $investStrategy->max_amount = 10;
        $investStrategy->min_interest_rate = 1;
        $investStrategy->max_interest_rate = 30;
        $investStrategy->min_loan_period = 1;
        $investStrategy->max_loan_period = 30;
        $investStrategy->portfolio_size = 0;
        $investStrategy->max_portfolio_size = 100;
        $investStrategy->loan_type = json_encode(['type' => ['installments']]);
        $investStrategy->loan_payment_status = json_encode(['payment_status' => ['current']]);
        $investStrategy->agreed = 1;
        $investStrategy->reinvest = 0;
        $investStrategy->save();

        $investStrategy->refresh();

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
            $this->assertEquals($investmentLoan->type, json_decode($investStrategy->loan_type)->type[0]);
            $this->assertEquals(
                $investmentLoan->payment_status,
                json_decode($investStrategy->loan_payment_status)->payment_status[0]
            );

            $investmentsSum += $investment->amount;
        }

        $this->assertEquals($investmentsSum, $investStrategy->portfolio_size);
        $this->assertEquals($investmentsSum, $investStrategy->total_invested);
        $this->assertCount(10, $investments);

        $transactions = Transaction::where(
            [
                'investor_id' => $investor->getId(),
                'type' => Transaction::TYPE_INVESTMENT,
            ]
        )->get();
        $this->assertNotEmpty($transactions);
        $this->assertCount(10, $transactions);

        \Artisan::call(
            'script:auto-invest'
            . ' ' . intval($investStrategy->priority)
            . ' ' . intval($investStrategy->investor_id)
            . ' ' . intval($investStrategy->invest_strategy_id)
            . ' ' . 'activating'
        );

        $investStrategy->refresh();

        $this->assertEquals($investmentsSum, $investStrategy->max_portfolio_size);
        $this->assertEquals($investmentsSum, $investStrategy->total_invested);
        $this->assertCount(10, $investments);

        $transactions = Transaction::where(
            [
                'investor_id' => $investor->getId(),
                'type' => Transaction::TYPE_INVESTMENT,
            ]
        )->get();
        $this->assertNotEmpty($transactions);
        $this->assertCount(10, $transactions);

        \Artisan::call(
            'script:auto-invest'
            . ' ' . intval($investStrategy->priority)
            . ' ' . intval($investStrategy->investor_id)
            . ' ' . intval($investStrategy->invest_strategy_id)
            . ' ' . 'activating'
        );

        $investStrategy->refresh();

        $this->assertEquals($investmentsSum, $investStrategy->max_portfolio_size);
        $this->assertEquals($investmentsSum, $investStrategy->total_invested);
        $this->assertCount(10, $investments);

        $transactions = Transaction::where(
            [
                'investor_id' => $investor->getId(),
                'type' => Transaction::TYPE_INVESTMENT,
            ]
        )->get();
        $this->assertNotEmpty($transactions);
        $this->assertCount(10, $transactions);

        foreach ($loansPrepare as $loanRemove) {
            $this->removeTestData($investor, $loanRemove);
        }
    }


    public function prepareInvestStrategy(
        Investor $investor,
        Wallet $wallet,
        bool $reinvest
    ) {
        $investStrategy = new InvestStrategy();
        $investStrategy->investor_id = $investor->getId();
        $investStrategy->wallet_id = $wallet->getId();
        $investStrategy->name = 'Unit test';
        $investStrategy->priority = 1;
        $investStrategy->min_amount = 10;
        $investStrategy->max_amount = 200;
        $investStrategy->min_interest_rate = 1;
        $investStrategy->max_interest_rate = 30;
        $investStrategy->min_loan_period = 1;
        $investStrategy->max_loan_period = 30;
        $investStrategy->portfolio_size = 0;
        $investStrategy->max_portfolio_size = 5000;
        $investStrategy->loan_type = json_encode(['type' => ['installments']]);
        $investStrategy->loan_payment_status = json_encode(['payment_status' => ['current']]);
        $investStrategy->agreed = 1;
        $investStrategy->reinvest = (int)$reinvest;
        $investStrategy->save();

        $investStrategy->refresh();

        return $investStrategy;
    }

    public function isScriptRunning($investor)
    {
        sleep(1);
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
