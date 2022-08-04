<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\LoanType;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class LoanTypeTest extends TestCase
{
    public function testHandler(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'loan_type' => [
                'payday',
                'installments',
            ],
        ];

        $data2 = [
            'loan' => [
                'type' => 'installments'
            ],
        ];

        $loanType = new LoanType();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray1 = [
            'loan.type' => [
                'payday',
                'installments',
                'whereIn' => 1,
            ]
        ];

        $expectedArray2 = [
            'loan.type' => [
                'installments',
                'whereIn' => 1,
            ]
        ];

        // Test first alias
        $this->assertEquals(
            $expectedArray1,
            $loanType->handle($dataWrapper, $next)->getWhere()
        );

        // Test second alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray2,
            $loanType->handle($dataWrapper, $next)->getWhere()
        );
    }
}
