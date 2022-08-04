<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\AmountAvailableMax;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class AmountAvailableMaxTest extends TestCase
{
    public function testHandle(): void
    {
        $builder = $this->createMock(Builder::class);


        $data = [
            'max_amount' => 90,
        ];

        $data2['amount_available']['to'] = 90;

        $amountAvailableMax = new AmountAvailableMax();

        // Test first alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            [
                'loan.amount_available',
                '<=',
                '90'
            ]
        ];

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMax->handle($dataWrapper, $next)->getWhere()
        );


        // Testing second alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMax->handle($dataWrapper, $next)->getWhere()
        );
    }
}
