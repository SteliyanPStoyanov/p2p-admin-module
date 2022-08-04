<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\LoanPaymentStatus;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class LoanPaymentStatusTest extends TestCase
{
    public function testHandler(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'loan_payment_status' => [
                'current',
                '1-15 days',
            ],
        ];

        $data2 = [
            'payment_status' => [
                'range1' => 'range1',
                'range2' => 'range2',
            ],
        ];

        $loanPaymentStatus = new LoanPaymentStatus();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            'loan.payment_status' => [
                'current',
                '1-15 days',
                'whereIn' => 1
            ]
        ];

        // Test first alias
        $this->assertEquals(
            $expectedArray,
            $loanPaymentStatus->handle($dataWrapper, $next)->getWhere()
        );

        // Test second alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray,
            $loanPaymentStatus->handle($dataWrapper, $next)->getWhere()
        );
    }
}
