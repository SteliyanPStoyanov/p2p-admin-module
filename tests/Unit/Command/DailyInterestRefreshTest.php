<?php

namespace Tests\Unit\Command;

use App;
use Artisan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Setting;
use Modules\Common\Entities\AutoRebuyLoan;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\UnlistedLoan;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class DailyInterestRefreshTest extends TestCase
{
    protected const NORMAL_LOAN = 1;
    protected const LOAN_WITH_UNLISTED_ENTITY = 2;
    protected const LOAN_OVERDUED = 3;

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

    public function testDailyInterestRefresh()
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
            5, // count of periods
            $currencyId,
            $issueDate,
            $listingDate,
            $finalPaymentDate
        );
        $this->prepareInstallmentsWithStartDate(
            $loan,
            Carbon::today()->subMonth()->toDateString(),
            5
        );

        $investorDeposit = 1000;
        $investor = $this->prepareInvestor('investor_interest_refresh_' . time() . '@test2.com');
        $wallet = $this->getInvestorWallet($investor->investor_id, $investorDeposit, $currencyId);
        $this->preparePortfolios($investor);

        $loan->refresh();

        // do invest
        $investorBuyAmount = 200;
        $now = Carbon::parse($issueDate);
        $invested = $this->investService->doInvest(
            $investorBuyAmount,
            $investor,
            $wallet,
            $loan,
            $loan->installments(),
            $now
        );

        $this->assertEquals(true, $invested);

        Artisan::call('script:daily-interest-refresh');

        $investorInstallments = InvestorInstallment::where(
            [
                'loan_id' => $loan->loan_id,
                'investor_id' => $investor->investor_id
            ]
        )->orderBy('investor_installment_id', 'ASC')->get();

        $this->assertNotEmpty($investorInstallments);

        $today = Carbon::today();

        // This flag is needed so we can know is the installment from investor installment current
        $isNextInstallmentChecked = false;

        foreach ($investorInstallments as $investorInstallment) {
            if (Carbon::parse($investorInstallment->installment()->due_date) < $today) {
                $lateInterest = InstallmentCalculator::calcLateInterest(
                    $today,
                    Carbon::parse($investorInstallment->installment()->due_date),
                    $investorInstallment->principal,
                    $investorInstallment->loan()->interest_rate_percent
                );

                $this->assertEquals($investorInstallment->interest, $investorInstallment->accrued_interest);
                $this->assertEquals($lateInterest, $investorInstallment->late_interest);
            } else {
                // Check the installments that arent current accrued_interest = 0
                if ($isNextInstallmentChecked) {
                    $this->assertEquals(0.00, $investorInstallment->accrued_interest);

                    continue;
                }

                // This check is only for the current installment!
                $interest = InstallmentCalculator::calcAccruedInterest(
                    $today,
                    Carbon::parse($investorInstallment->installment()->due_date),
                    Carbon::parse($investorInstallment->installment()->getPreviousInstallmentDueDate()),
                    $investorInstallment->interest
                );

                $this->assertEquals($interest, $investorInstallment->accrued_interest);

                if (Carbon::parse($investorInstallment->installment()->due_date) == $today) {
                    $this->assertEquals($investorInstallment->interest, $investorInstallment->accrued_interest);
                }

                $isNextInstallmentChecked = true;
            }
        }

        $this->removeTestData($investor, $loan);
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
