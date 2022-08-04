<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\AmountAvailableMin;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class AmountAvailableMinTest extends TestCase
{
    public function testHandler(): void
    {
        $builder = $this->createMock(Builder::class);


        $data = [
            'min_amount' => 90,
        ];

        $data2['amount_available']['from'] = 90;

        $amountAvailableMin = new AmountAvailableMin();

        // Test first alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            [
                'loan.amount_available',
                '>=',
                '90'
            ]
        ];

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMin->handle($dataWrapper, $next)->getWhere()
        );


        // Testing second alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMin->handle($dataWrapper, $next)->getWhere()
        );
    }

    public function testNoValue()
    {
        $builder = $this->createMock(Builder::class);


        $data = [
            'min_amount' => '', // Test no value
        ];

        $data2['amount_available']['from'] = ''; // Test no value

        $amountAvailableMin = new AmountAvailableMin();

        // Test first alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            [
                'loan.amount_available',
                '>=',
                AmountAvailableMin::MIN_VALUE
            ]
        ];

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMin->handle($dataWrapper, $next)->getWhere()
        );

        // Testing second alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMin->handle($dataWrapper, $next)->getWhere()
        );
    }

    public function testZeroValue()
    {
        $builder = $this->createMock(Builder::class);


        $data = [
            'min_amount' => 0, // Test zero value
        ];

        $data2['amount_available']['from'] = 0; // Test zero value

        $amountAvailableMin = new AmountAvailableMin();

        // Test first alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            [
                'loan.amount_available',
                '>=',
                AmountAvailableMin::MIN_VALUE
            ]
        ];

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMin->handle($dataWrapper, $next)->getWhere()
        );

        // Testing second alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMin->handle($dataWrapper, $next)->getWhere()
        );
    }

    public function testAdminValue()
    {
        $builder = $this->createMock(Builder::class);


        $data = [
            'min_amount' => 0, // Test zero value
        ];

        $data2['amount_available']['from'] = 0; // Test zero value

        $amountAvailableMin = new AmountAvailableMin();

        // Test first alias
        list($dataWrapper, $next) = DataProvider::forAdminHandlers($builder, $data);

        $expectedArray = [
            [
                'loan.amount_available',
                '>=',
                AmountAvailableMin::ADMIN_MIN_VALUE
            ]
        ];

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMin->handle($dataWrapper, $next)->getWhere()
        );

        // Testing second alias
        list($dataWrapper, $next) = DataProvider::forAdminHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray,
            $amountAvailableMin->handle($dataWrapper, $next)->getWhere()
        );
    }
}
