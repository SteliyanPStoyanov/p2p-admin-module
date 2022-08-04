<?php

namespace Tests\Unit\Installment;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Installment;
use Modules\Common\Entities\Loan;
use Modules\Common\Libraries\Calculator\Calculator;
use Modules\Common\Services\DistributeService;
use Modules\Common\Services\ImportService;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class InstallmentRepaymentTest extends TestCase
{
    use WithoutMiddleware;
    use TestDataTrait;

    private $importService;
    protected $distributeService;

    public function setUp(): void
    {
        parent::setUp();
        $this->importService = \App::make(ImportService::class);
        $this->distributeService = \App::make(DistributeService::class);
    }

    public function testInstallmentRepaymentDueDateAfterRepaymentDate()
    {
        $loan = $this->preapreLoan();
        $this->prepareInstallments($loan);
        $loan->refresh();
        $installment = $loan->getFirstUnpaidInstallment();
        $this->assertNotEmpty($installment);

        $installmentDueDate = $installment->due_date;

        $repaidInstallment = $this->emulateRepaidInstallment($loan);

        $repaymentDate = Carbon::parse($installmentDueDate)->subDays(rand(1, 10));
        $distributed = $this->distributeService->distributeInstallment($repaidInstallment, $repaymentDate);
        $this->assertTrue($distributed);

        $installment->refresh();

        $this->assertEquals(1, $installment->paid);
        $this->assertEquals($repaymentDate, Carbon::parse($installment->paid_at));
        $this->assertEquals(Installment::STATUS_PAID, $installment->payment_status);

        $this->removeTestData(null, $loan);
    }

    public function testInstallmentRepaymentDueDateEqualRepaymentDate()
    {
        $loan = $this->preapreLoan();
        $this->prepareInstallments($loan);
        $loan->refresh();
        $installment = $loan->getFirstUnpaidInstallment();
        $this->assertNotEmpty($installment);

        $installmentDueDate = $installment->due_date;

        $repaidInstallment = $this->emulateRepaidInstallment($loan);

        $repaymentDate = Carbon::parse($installmentDueDate);
        $distributed = $this->distributeService->distributeInstallment($repaidInstallment, $repaymentDate);
        $this->assertTrue($distributed);

        $installment->refresh();

        $this->assertEquals(1, $installment->paid);
        $this->assertEquals($repaymentDate, Carbon::parse($installment->paid_at));
        $this->assertEquals(Installment::STATUS_PAID, $installment->payment_status);

        $this->removeTestData(null, $loan);
    }

    public function testInstallmentRepaymentDueDateBeforeRepaymentDate()
    {
        $loan = $this->preapreLoan();
        $this->prepareInstallments($loan);
        $loan->refresh();
        $installment = $loan->getFirstUnpaidInstallment();
        $this->assertNotEmpty($installment);

        $installmentDueDate = $installment->due_date;

        $repaidInstallment = $this->emulateRepaidInstallment($loan);

        $repaymentDate = Carbon::parse($installmentDueDate)->addDays(rand(1, 10));
        $distributed = $this->distributeService->distributeInstallment($repaidInstallment, $repaymentDate);
        $this->assertTrue($distributed);

        $installment->refresh();

        $this->assertEquals(1, $installment->paid);
        $this->assertEquals($repaymentDate, Carbon::parse($installment->paid_at));
        $this->assertEquals(Installment::STATUS_PAID_LATE, $installment->payment_status);

        $this->removeTestData(null, $loan);
    }

    public function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            DB::disconnect();
        });

        parent::tearDown();
    }
}
