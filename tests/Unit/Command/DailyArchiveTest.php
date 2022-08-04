<?php

namespace Tests\Unit\Command;

use App;
use Artisan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\BlockedIp;
use Modules\Common\Entities\BlockedIpHistory;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\InvestorInstallmentHistory;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\PortfolioHistory;
use Modules\Common\Entities\RegistrationAttempt;
use Modules\Common\Entities\RegistrationAttemptHistory;
use Modules\Common\Entities\UnlistedLoan;
use Modules\Common\Entities\WalletHistory;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class DailyArchiveTest extends TestCase
{
    use WithoutMiddleware;
    use TestDataTrait;

    protected $importService;
    protected $investService;

    public function setUp(): void
    {
        parent::setUp();

        $this->importService = App::make(ImportService::class);
        $this->investService = App::make(InvestService::class);
    }

    public function testWalletArchive()
    {
        $investor = $this->prepareInvestor('investor_' . time() . '@walletArchiveTest.com');
        $wallet = $this->prepareWallet($investor);

        $now = Carbon::now();
        Artisan::call('script:daily-archive');

        $walletHistory = WalletHistory::where('wallet_id', $wallet->wallet_id)->get();

        $this->assertNotEmpty($walletHistory);
        $this->assertEquals(1, $walletHistory->count());
        $walletHistory = $walletHistory->first();

        $this->assertEquals($wallet->investor_id, $walletHistory->investor_id);
        $this->assertEquals($wallet->currency_id, $walletHistory->currency_id);
        $this->assertEquals($wallet->date, $walletHistory->date);
        $this->assertEquals($wallet->total_amount, $walletHistory->total_amount);
        $this->assertEquals($wallet->invested, $walletHistory->invested);
        $this->assertEquals($wallet->uninvested, $walletHistory->uninvested);
        $this->assertEquals($wallet->deposit, $walletHistory->deposit);
        $this->assertEquals($wallet->withdraw, $walletHistory->withdraw);
        $this->assertEquals($wallet->income, $walletHistory->income);
        $this->assertEquals($wallet->interest, $walletHistory->interest);
        $this->assertEquals($wallet->late_interest, $walletHistory->late_interest);
        $this->assertEquals($wallet->bonus, $walletHistory->bonus);
        $this->assertEquals($wallet->active, $walletHistory->active);
        $this->assertEquals($wallet->deleted, $walletHistory->deleted);
        $this->assertEquals($wallet->created_at, $walletHistory->created_at);
        $this->assertEquals($wallet->created_by, $walletHistory->created_by);
        $this->assertEquals($wallet->updated_at, $walletHistory->updated_at);
        $this->assertEquals($wallet->updated_by, $walletHistory->updated_by);
        $this->assertEquals($wallet->deleted_at, $walletHistory->deleted_at);
        $this->assertEquals($wallet->deleted_by, $walletHistory->deleted_by);
        $this->assertEquals($wallet->enabled_at, $walletHistory->enabled_at);
        $this->assertEquals($wallet->enabled_by, $walletHistory->enabled_by);
        $this->assertEquals($wallet->disabled_at, $walletHistory->disabled_at);
        $this->assertEquals($wallet->disabled_by, $walletHistory->disabled_by);
        $this->assertEqualsWithDelta(Carbon::parse($walletHistory->archived_at)->timestamp, $now->timestamp, 5);

        //Check is date updated
        $wallet->refresh();

        $this->assertNotEmpty($wallet->date);
        $this->assertEquals($now->toDateString(), $wallet->date);

        //Ok lets execute the script again. Have to stay 1 archived entity
        Artisan::call('script:daily-archive');
        $walletHistory = WalletHistory::where('wallet_id', $wallet->wallet_id)->get();
        $this->assertNotEmpty($walletHistory);
        $this->assertEquals(1, $walletHistory->count());
        $walletHistory = $walletHistory->first();

        WalletHistory::where('wallet_history_id', $walletHistory->wallet_history_id)->delete();
        $this->removeTestData($investor);
    }

    public function testPortfolioArchive()
    {
        $investor = $this->prepareInvestor('investor_' . time() . '@portfolioArchiveTest.net');
        $portfolios = $this->preparePortfolios($investor);
        $qualityPortfolio = $portfolios['quality'];
        $maturityPortfolio = $portfolios['maturity'];
        $now = Carbon::now();

        // Must be null or before today date field to be archived
        $qualityPortfolio->date = Carbon::yesterday();
        $maturityPortfolio->date = Carbon::yesterday();
        $qualityPortfolio->save();
        $maturityPortfolio->save();
        $qualityPortfolio->refresh();
        $maturityPortfolio->refresh();

        Artisan::call('script:daily-archive');

        $qualityPortfolioHistory = PortfolioHistory::where('portfolio_id', $qualityPortfolio->portfolio_id);
        $maturityPortfolioHistory = PortfolioHistory::where('portfolio_id', $maturityPortfolio->portfolio_id);

        $this->assertNotEmpty($qualityPortfolioHistory);
        $this->assertNotEmpty($maturityPortfolioHistory);
        $this->assertEquals(1, $qualityPortfolioHistory->count());
        $this->assertEquals(1, $maturityPortfolioHistory->count());

        $qualityPortfolioHistory = $qualityPortfolioHistory->first();
        $maturityPortfolioHistory = $maturityPortfolioHistory->first();

        // Quality portfolio
        $this->assertEquals($qualityPortfolio->portfolio_id, $qualityPortfolioHistory->portfolio_id);
        $this->assertEquals($qualityPortfolio->investor_id, $qualityPortfolioHistory->investor_id);
        $this->assertEquals($qualityPortfolio->currency_id, $qualityPortfolioHistory->currency_id);
        $this->assertEquals($qualityPortfolio->type, $qualityPortfolioHistory->type);
        $this->assertEquals($qualityPortfolio->date, $qualityPortfolioHistory->date);
        $this->assertEquals($qualityPortfolio->range1, $qualityPortfolioHistory->range1);
        $this->assertEquals($qualityPortfolio->range2, $qualityPortfolioHistory->range2);
        $this->assertEquals($qualityPortfolio->range3, $qualityPortfolioHistory->range3);
        $this->assertEquals($qualityPortfolio->range4, $qualityPortfolioHistory->range4);
        $this->assertEquals($qualityPortfolio->range5, $qualityPortfolioHistory->range5);
        $this->assertEquals($qualityPortfolio->ranges_updated_at, $qualityPortfolioHistory->ranges_updated_at);
        $this->assertEquals($qualityPortfolio->active, $qualityPortfolioHistory->active);
        $this->assertEquals($qualityPortfolio->deleted, $qualityPortfolioHistory->deleted);
        $this->assertEquals($qualityPortfolio->created_at, $qualityPortfolioHistory->created_at);
        $this->assertEquals($qualityPortfolio->created_by, $qualityPortfolioHistory->created_by);
        $this->assertEquals($qualityPortfolio->updated_at, $qualityPortfolioHistory->updated_at);
        $this->assertEquals($qualityPortfolio->updated_by, $qualityPortfolioHistory->updated_by);
        $this->assertEquals($qualityPortfolio->deleted_at, $qualityPortfolioHistory->deleted_at);
        $this->assertEquals($qualityPortfolio->deleted_by, $qualityPortfolioHistory->deleted_by);
        $this->assertEquals($qualityPortfolio->enabled_at, $qualityPortfolioHistory->enabled_at);
        $this->assertEquals($qualityPortfolio->enabled_by, $qualityPortfolioHistory->enabled_by);
        $this->assertEquals($qualityPortfolio->disabled_at, $qualityPortfolioHistory->disabled_at);
        $this->assertEquals($qualityPortfolio->disabled_by, $qualityPortfolioHistory->disabled_by);
        $this->assertEqualsWithDelta(
            Carbon::parse($qualityPortfolioHistory->archived_at)->timestamp,
            $now->timestamp,
            5
        );

        // Maturity portfolio
        $this->assertEquals($maturityPortfolio->portfolio_id, $maturityPortfolioHistory->portfolio_id);
        $this->assertEquals($maturityPortfolio->investor_id, $maturityPortfolioHistory->investor_id);
        $this->assertEquals($maturityPortfolio->currency_id, $maturityPortfolioHistory->currency_id);
        $this->assertEquals($maturityPortfolio->type, $maturityPortfolioHistory->type);
        $this->assertEquals($maturityPortfolio->date, $maturityPortfolioHistory->date);
        $this->assertEquals($maturityPortfolio->range1, $maturityPortfolioHistory->range1);
        $this->assertEquals($maturityPortfolio->range2, $maturityPortfolioHistory->range2);
        $this->assertEquals($maturityPortfolio->range3, $maturityPortfolioHistory->range3);
        $this->assertEquals($maturityPortfolio->range4, $maturityPortfolioHistory->range4);
        $this->assertEquals($maturityPortfolio->range5, $maturityPortfolioHistory->range5);
        $this->assertEquals($maturityPortfolio->ranges_updated_at, $maturityPortfolioHistory->ranges_updated_at);
        $this->assertEquals($maturityPortfolio->active, $maturityPortfolioHistory->active);
        $this->assertEquals($maturityPortfolio->deleted, $maturityPortfolioHistory->deleted);
        $this->assertEquals($maturityPortfolio->created_at, $maturityPortfolioHistory->created_at);
        $this->assertEquals($maturityPortfolio->created_by, $maturityPortfolioHistory->created_by);
        $this->assertEquals($maturityPortfolio->updated_at, $maturityPortfolioHistory->updated_at);
        $this->assertEquals($maturityPortfolio->updated_by, $maturityPortfolioHistory->updated_by);
        $this->assertEquals($maturityPortfolio->deleted_at, $maturityPortfolioHistory->deleted_at);
        $this->assertEquals($maturityPortfolio->deleted_by, $maturityPortfolioHistory->deleted_by);
        $this->assertEquals($maturityPortfolio->enabled_at, $maturityPortfolioHistory->enabled_at);
        $this->assertEquals($maturityPortfolio->enabled_by, $maturityPortfolioHistory->enabled_by);
        $this->assertEquals($maturityPortfolio->disabled_at, $maturityPortfolioHistory->disabled_at);
        $this->assertEquals($maturityPortfolio->disabled_by, $maturityPortfolioHistory->disabled_by);
        $this->assertEqualsWithDelta(
            Carbon::parse($maturityPortfolioHistory->archived_at)->timestamp,
            $now->timestamp,
            5
        );

        $qualityPortfolio->refresh();
        $maturityPortfolio->refresh();

        $this->assertNotEmpty($qualityPortfolio->date);
        $this->assertEquals($now->toDateString(), $qualityPortfolio->date);
        $this->assertNotEmpty($maturityPortfolio->date);
        $this->assertEquals($now->toDateString(), $maturityPortfolio->date);


        //Ok lets execute the script again. Have to stay 1 archived entity
        Artisan::call('script:daily-archive');
        $qualityPortfolioHistory = PortfolioHistory::where('portfolio_id', $qualityPortfolioHistory->portfolio_id)->get(
        );
        $maturityPortfolioHistory = PortfolioHistory::where(
            'portfolio_id',
            $maturityPortfolioHistory->portfolio_id
        )->get();

        $this->assertNotEmpty($qualityPortfolioHistory);
        $this->assertEquals(1, $qualityPortfolioHistory->count());
        $this->assertNotEmpty($maturityPortfolioHistory);
        $this->assertEquals(1, $maturityPortfolioHistory->count());

        $this->removeTestData($investor);
    }

    public function testInvestorInstallmentArchive()
    {
        // prepare test data
        $currencyId = Currency::ID_EUR;
        $loanAmount = 1222.46;
        $remainingPricipal = 1222.46;
        $interestRate = 14.50;
        $issueDate = '2020-11-02';
        $listingDate = '2020-11-02';
        $finalPaymentDate = Carbon::today()->addMonths(2);

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
        $installments = $this->createLoanInstallments($loan, $issueDate);

        $investor = $this->prepareInvestor('investor_' . time() . '@investorInstallmentArchiveTest.net');
        $portfolios = $this->preparePortfolios($investor);
        $wallet = $this->prepareWallet($investor);


        // do invest
        $investorBuyAmount = 200;
        $now = Carbon::parse($issueDate);
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $installments,
            $now
        );

        $this->assertEquals(true, $invested);

        Artisan::call('script:daily-archive');
        // Check, must not be archived
        $count = InvestorInstallmentHistory::where('loan_id', $loan->loan_id)->count();
        $this->assertEquals(0, $count);

        $investorInstallments = $investor->installments($loan->loan_id);

        // Lets make loan status != active and archive it
        $loan->repaid(true);
        $now = Carbon::now();
        Artisan::call('script:daily-archive');


        $this->assertEquals(0, count($investor->installments($loan->loan_id)));

        foreach ($investorInstallments as $investorInstallment) {

            $historyEntity = InvestorInstallmentHistory::where(
                'investor_installment_id',
                $investorInstallment->investor_installment_id
            )->get();

            $this->assertEquals(1, $historyEntity->count());
            $historyEntity = $historyEntity->first();

            $this->assertEquals($investorInstallment->loan_id, $historyEntity->loan_id);
            $this->assertEquals($investorInstallment->investment_id, $historyEntity->investment_id);
            $this->assertEquals($investorInstallment->investor_id, $historyEntity->investor_id);
            $this->assertEquals($investorInstallment->installment_id, $historyEntity->installment_id);
            $this->assertEquals($investorInstallment->currency_id, $historyEntity->currency_id);
            $this->assertEquals($investorInstallment->days, $historyEntity->days);
            $this->assertEquals($investorInstallment->remaining_principal, $historyEntity->remaining_principal);
            $this->assertEquals($investorInstallment->principal, $historyEntity->principal);
            $this->assertEquals($investorInstallment->accrued_interest, $historyEntity->accrued_interest);
            $this->assertEquals($investorInstallment->interest, $historyEntity->interest);
            $this->assertEquals($investorInstallment->late_interest, $historyEntity->late_interest);
            $this->assertEquals($investorInstallment->interest_percent, $historyEntity->interest_percent);
            $this->assertEquals($investorInstallment->total, $historyEntity->total);

            $this->assertEquals(0, $investorInstallment->paid);
            $this->assertNull($investorInstallment->paid_at);
            $this->assertEquals(1, $historyEntity->paid);
            $this->assertNotNull($historyEntity->paid_at);

            $this->assertEquals($investorInstallment->active, $historyEntity->active);
            $this->assertEquals($investorInstallment->deleted, $historyEntity->deleted);
            $this->assertEquals($investorInstallment->created_at, $historyEntity->created_at);
            $this->assertEquals($investorInstallment->created_by, $historyEntity->created_by);
            $this->assertEquals($investorInstallment->deleted_at, $historyEntity->deleted_at);
            $this->assertEquals($investorInstallment->deleted_by, $historyEntity->deleted_by);
            $this->assertEquals($investorInstallment->enabled_at, $historyEntity->enabled_at);
            $this->assertEquals($investorInstallment->enabled_by, $historyEntity->enabled_by);
            $this->assertEquals($investorInstallment->disabled_at, $historyEntity->disabled_at);
            $this->assertEquals($investorInstallment->disabled_by, $historyEntity->disabled_by);
            $this->assertEqualsWithDelta($now, Carbon::parse($historyEntity->archived_at), 5);
        }

        $this->removeTestData($investor, $loan);
    }

    public function testRegistrationAttemptsArchive()
    {
        $registrationAttempt = new RegistrationAttempt();
        $registrationAttempt->fill(
            [
                'datetime' => Carbon::now(),
                'email' => 'test@test.bg',
                'ip' => '127.0.0.1',
                'device' => 'Opera'
            ]
        );
        $registrationAttempt->save();

        Artisan::call('script:daily-archive');
        // Must be not archived
        $count = RegistrationAttempt::where('id', $registrationAttempt->id)->count();
        $this->assertEquals(1, $count);

        $registrationAttempt->refresh();
        $registrationAttempt->datetime = Carbon::yesterday();
        $registrationAttempt->save();
        $registrationAttempt->refresh();

        Artisan::call('script:daily-archive');
        // Must be archived
        $count = RegistrationAttempt::where('id', $registrationAttempt->id)->count();
        $this->assertEquals(0, $count);

        $historyEntity = RegistrationAttemptHistory::where('id', $registrationAttempt->id)->get();
        $this->assertEquals(1, $historyEntity->count());
        $historyEntity = $historyEntity->first();

        $this->assertEquals($registrationAttempt->datetime, $historyEntity->datetime);
        $this->assertEquals($registrationAttempt->email, $historyEntity->email);
        $this->assertEquals($registrationAttempt->ip, $historyEntity->ip);
        $this->assertEquals($registrationAttempt->device, $historyEntity->device);
        $this->assertEqualsWithDelta(Carbon::now(), Carbon::parse($historyEntity->archived_at), 5);

        // Cleanup
        RegistrationAttemptHistory::where('history_id', $historyEntity->history_id)->delete();
    }

    public function testBlockedIpsArchive()
    {
        $blockedIp = new BlockedIp();
        $blockedIp->fill(
            [
                'blocked_till' => Carbon::now()->addMinutes(5),
                'ip' => '127.0.0.1',
            ]
        );
        $blockedIp->save();

        Artisan::call('script:daily-archive');
        // Must be not archived
        $count = BlockedIp::where('id', $blockedIp->id)->count();
        $this->assertEquals(1, $count);

        $blockedIp->refresh();
        $blockedIp->blocked_till = Carbon::now()->subMinutes(2);
        $blockedIp->save();
        $blockedIp->refresh();

        Artisan::call('script:daily-archive');
        // Must be archived
        $count = BlockedIp::where('id', $blockedIp->id)->count();
        $this->assertEquals(0, $count);

        $historyEntity = BlockedIpHistory::where('id', $blockedIp->id)->get();
        $this->assertEquals(1, $historyEntity->count());
        $historyEntity = $historyEntity->first();

        $this->assertEquals($blockedIp->blocked_till, $historyEntity->blocked_till);
        $this->assertEquals($blockedIp->ip, $historyEntity->ip);
        $this->assertEquals($blockedIp->created_at, Carbon::parse($historyEntity->created_at));
        $this->assertEquals($blockedIp->created_by, $historyEntity->created_by);
        $this->assertEqualsWithDelta(Carbon::now(), Carbon::parse($historyEntity->archived_at), 5);

        // Cleanup
        BlockedIpHistory::where('history_id', $historyEntity->history_id)->delete();
    }

    public function testUnlistedLoanArchive()
    {
        // We need 3 unlisted loan entities so we check if in 2 and 3 set them handled and 1 skip them
        // 1 with loan = active
        // 2 with loan != active
        // 3 without existing loan

        // prepare test data
        $currencyId = Currency::ID_EUR;
        $loanAmount = 1222.46;
        $remainingPricipal = 1222.46;
        $interestRate = 14.50;
        $issueDate = '2020-11-02';
        $listingDate = '2020-11-02';
        $finalPaymentDate = Carbon::today()->addMonths(2);

        $loanActive = $this->preapreLoan(
            $loanAmount,
            $loanAmount,
            $remainingPricipal,
            $interestRate,
            10, // originator percent
            5, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            $finalPaymentDate
        );

        $loanNotActive = $this->preapreLoan(
            $loanAmount,
            $loanAmount,
            $remainingPricipal,
            $interestRate,
            10, // originator percent
            5, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            $finalPaymentDate
        );
        $loanActive->refresh();
        $loanNotActive->refresh();
        $loanNotActive->unlisted_at = Carbon::now();
        $loanNotActive->status = Loan::STATUS_REBUY;
        $loanNotActive->save();
        $loanNotActive->refresh();

        // Check is everything okay
        $this->assertNotEmpty($loanActive);
        $this->assertNotEmpty($loanNotActive);
        $this->assertEquals(Loan::STATUS_ACTIVE, $loanActive->status);
        $this->assertEquals(Loan::STATUS_REBUY, $loanNotActive->status);

        //Now create unlisted loan entities
        $unlistedLoanActive = new UnlistedLoan();
        $unlistedLoanActive->fill(
            [
                'lender_id' => $loanActive->lender_id,
                'handled' => 0,
                'status' => UnlistedLoan::STATUS_DEFAULT,
            ]
        );
        $unlistedLoanActive->save();

        $unlistedLoanNotActive = new UnlistedLoan();
        $unlistedLoanNotActive->fill(
            [
                'lender_id' => $loanNotActive->lender_id,
                'handled' => 0,
                'status' => UnlistedLoan::STATUS_DEFAULT,
            ]
        );
        $unlistedLoanNotActive->save();

        $unlistedLoanNotExisting = new UnlistedLoan();
        $unlistedLoanNotExisting->fill(
            [
                'lender_id' => rand(10001, 99999),
                'handled' => 0,
                'status' => UnlistedLoan::STATUS_DEFAULT,
            ]
        );
        $unlistedLoanNotExisting->save();

        Artisan::call('script:daily-archive');

        //Fresh data
        $unlistedLoanActive->refresh();
        $unlistedLoanNotActive->refresh();
        $unlistedLoanNotExisting->refresh();
        // Now check
        $this->assertEquals(0, $unlistedLoanActive->handled);
        $this->assertEquals(1, $unlistedLoanNotActive->handled);
        $this->assertEquals(1, $unlistedLoanNotExisting->handled);

        $this->assertEquals(UnlistedLoan::STATUS_DEFAULT, $unlistedLoanActive->status);
        $this->assertEquals(UnlistedLoan::STATUS_ALREADY_UNLISTED, $unlistedLoanNotActive->status);
        $this->assertEquals(UnlistedLoan::STATUS_NOT_EXISTS, $unlistedLoanNotExisting->status);

        UnlistedLoan::where('unlisted_loan_id', $unlistedLoanActive->unlisted_loan_id)->delete();
        UnlistedLoan::where('unlisted_loan_id', $unlistedLoanNotActive->unlisted_loan_id)->delete();
        UnlistedLoan::where('unlisted_loan_id', $unlistedLoanNotExisting->unlisted_loan_id)->delete();
        $this->removeTestData(null, $loanActive);
        $this->removeTestData(null, $loanNotActive);
    }

    protected function createLoanInstallments(Loan $loan, $fromDate = null)
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

    protected function getInstallmentsAfterInsert($loan, array $import, $fromDate)
    {
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
