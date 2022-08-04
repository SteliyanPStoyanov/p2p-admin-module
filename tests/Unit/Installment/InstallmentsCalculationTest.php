<?php

namespace Tests\Unit\Installment;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Libraries\Calculator\InstallmentCalculator;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class InstallmentsCalculationTest extends TestCase
{
    use WithoutMiddleware;
    use TestDataTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCommonInstallment()
    {
        $oRemainingPrincipal = 384.07;
        $oPrincipal = 158.14;
        $interestPercent = 8;
        $listingDate = Carbon::parse('2021-01-04');
        $installmentDueDate = Carbon::parse('2022-09-04');
        $prevInstallmentDueDate = Carbon::parse('2022-08-04');

        $installment = InstallmentCalculator::calcInstallmentAmounts(
            $oRemainingPrincipal,
            $oPrincipal,
            $interestPercent,
            $listingDate,
            $installmentDueDate,
            $prevInstallmentDueDate
        );

        $this->assertEquals(2.65, $installment['interest']);
    }
}
