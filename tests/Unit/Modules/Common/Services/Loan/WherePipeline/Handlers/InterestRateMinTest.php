<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\InterestRateMin;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class InterestRateMinTest extends TestCase
{
    public function testHandler(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'min_interest_rate' => 15,
            'max_interest_rate' => 18,
        ];

        $data2 = [
            'interest_rate_percent' => [
                'from' => 15,
                'to' => 18,
            ],
        ];

        $interestRateMin = new InterestRateMin();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray[] = [
            'loan.interest_rate_percent',
            '>=',
            15
        ];

        // Test first alias
        $this->assertEquals(
            $expectedArray,
            $interestRateMin->handle($dataWrapper, $next)->getWhere()
        );

        // Test second alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray,
            $interestRateMin->handle($dataWrapper, $next)->getWhere()
        );
    }
}
