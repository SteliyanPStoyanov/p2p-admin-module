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
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\UnlistedLoan;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class DailyLoansAutoRebuyTest extends TestCase
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

    public function testDailyAutoRebuy()
    {
        // Will test 3 loans auto rebuy
        // 1. Normal loan which dont have to be rebuyed
        // 2. The same normal loan when adding unlisted loan entity
        // 3. Loan with overdue > max_overdue_days setting
        // Will test multiple loans so to check is the chunk working

        $currencyId = Currency::ID_EUR;
        $loanAmount = 1222.46;
        $remainingPricipal = 1222.46;
        $interestRate = 14.50;
        $issueDate = '2020-11-02';
        $listingDate = '2020-11-02';
        $finalPaymentDate = Carbon::today()->addMonths(2);

        $maxOverdueDays = \SettingFacade::getSettingValue(Setting::MAX_ACCEPTABLE_OVERDUE_DAYS_KEY);

        // Create multiple loans
        $loansWithType = [];
        for ($i = 0; $i < 50; $i++) {
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

            $random = rand(self::NORMAL_LOAN, self::LOAN_OVERDUED);

            switch ($random) {
                case 1:
                    break;
                case 2:
                    $unlistedLoan = new UnlistedLoan();
                    $unlistedLoan->fill(
                        [
                            'lender_id' => $loan->lender_id,
                            'handled' => 0,
                            'status' => UnlistedLoan::STATUS_DEFAULT,
                        ]
                    );
                    $unlistedLoan->save();

                    break;
                case 3:
                    $loan->overdue_days = ($maxOverdueDays + rand(1, 20));
                    $loan->save();

                    break;
            }

            $loan->refresh();

            $loansWithType[] = [
                'type' => $random,
                'loan' => $loan,
            ];
        }

        Artisan::call('script:loans:auto-rebuy');
        $now = Carbon::now();

        // Now check them
        foreach ($loansWithType as $loanWithType) {
            $loan = $loanWithType['loan'];
            $loan->refresh();
            switch ($loanWithType['type']) {
                case 1:
                    $this->assertEquals(Loan::STATUS_ACTIVE, $loan->status);
                    $this->assertEquals(0, $loan->unlisted);
                    $this->assertEquals(null, $loan->unlisted_at);

                    break;
                case 2:
                    $this->assertEquals(Loan::STATUS_REBUY, $loan->status);
                    $this->assertEquals(1, $loan->unlisted);
                    $this->assertEquals(Carbon::yesterday()->endOfDay()->toDateTimeString(), Carbon::parse($loan->unlisted_at)->toDateTimeString());

                    $unlistedLoan = UnlistedLoan::where(
                        [
                            'lender_id' => $loan->lender_id,
                            'handled' => 1,
                            'status' => UnlistedLoan::STATUS_DEFAULT,
                        ]
                    )->first();
                    $this->assertNotEmpty($unlistedLoan);

                    break;
                case 3:
                    $this->assertEquals(Loan::STATUS_REBUY, $loan->status);
                    $this->assertEquals(1, $loan->unlisted);
                    $this->assertEquals(Carbon::yesterday()->endOfDay()->toDateTimeString(), Carbon::parse($loan->unlisted_at)->toDateTimeString());

                    $autoRebuyLoan = AutoRebuyLoan::where(
                        [
                            'loan_id' => $loan->loan_id,
                            'remaining_principal' => $loan->remaining_principal,
                        ]
                    )->first();

                    $this->assertNotEmpty($autoRebuyLoan);

                    break;
            }

            $this->removeTestData(null, $loan);
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
