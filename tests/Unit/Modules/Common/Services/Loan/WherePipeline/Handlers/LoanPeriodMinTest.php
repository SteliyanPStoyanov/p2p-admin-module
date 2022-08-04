<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\LoanPeriodMin;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class LoanPeriodMinTest extends TestCase
{
    public function testHandler(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'min_loan_period' => 1,
            'max_loan_period' => 10,
        ];

        $data2 = [
            'period' => [
                'from' => 1,
                'to' => 10,
            ],
        ];

        $loanPeriodMin = new LoanPeriodMin();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray[] = [
            'loan.final_payment_date',
            '>=',
            dbDate(Carbon::now()->addMonths(1)),
        ];

        // Test first alias
        $this->assertEquals(
            $expectedArray,
            $loanPeriodMin->handle($dataWrapper, $next)->getWhere()
        );

        // Test second alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray,
            $loanPeriodMin->handle($dataWrapper, $next)->getWhere()
        );
    }
}
