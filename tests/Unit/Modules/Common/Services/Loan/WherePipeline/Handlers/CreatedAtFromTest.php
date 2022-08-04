<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\CreatedAtFrom;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class CreatedAtFromTest extends TestCase
{
    public function testHandler(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'created_at' => [
                'from' => '08.03.2021'
            ],
        ];

        $createdAtFrom = new CreatedAtFrom();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            [
                'loan.created_at',
                '>=',
                dbDate('08.03.2021', '00:00:00'),
            ]
        ];

        $this->assertEquals(
            $expectedArray,
            $createdAtFrom->handle($dataWrapper, $next)->getWhere()
        );
    }
}
