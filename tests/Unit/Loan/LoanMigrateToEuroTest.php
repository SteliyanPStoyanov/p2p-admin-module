<?php

namespace Tests\Unit\Loan;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Modules\Common\Services\InvestService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class LoanMigrateToEuroTest extends TestCase
{
    use TestDataTrait;
    use WithoutMiddleware;

    private $importService;
    private $investService;
    private $distributeService;

    public function setUp(): void
    {
        parent::setUp();
        $this->importService = new ImportService;
        $this->investService = new InvestService;
        $this->distributeService = new DistributeService;
    }

    public function testLoanConvertToEuro()
    {
        // create loan and installments
        $loan = $this->preapreLoan();
        $this->assertNotEmpty($loan->loan_id);

        $this->assertEquals($loan->original_currency_id, Currency::ID_BGN);
        $this->assertEquals($loan->original_amount, $loan->original_amount);
        $this->assertEquals($loan->original_amount_afranga, $loan->original_amount_afranga);

        $this->assertEquals($loan->currency_id, Currency::ID_EUR);
        $this->assertEquals($loan->amount, Calculator::toEuro($loan->original_amount));
        $this->assertEquals($loan->amount_afranga, Calculator::toEuro($loan->original_amount_afranga));
        $this->assertEquals($loan->amount_available, Calculator::toEuro($loan->original_amount_available));
        $this->assertEquals($loan->remaining_principal, Calculator::toEuro($loan->original_remaining_principal));

        //remove test data
        $this->removeTestData(null, $loan);
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
