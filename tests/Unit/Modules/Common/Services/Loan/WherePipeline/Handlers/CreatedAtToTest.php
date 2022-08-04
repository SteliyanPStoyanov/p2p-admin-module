<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\CreatedAtTo;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class CreatedAtToTest extends TestCase
{
    public function testHandler(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'created_at' => [
                'to' => '09.03.2021'
            ],
        ];

        $createdAtTo = new CreatedAtTo();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            [
                'loan.created_at',
                '<=',
                dbDate('09.03.2021', '00:00:00'),
            ]
        ];

        $this->assertEquals(
            $expectedArray,
            $createdAtTo->handle($dataWrapper, $next)->getWhere()
        );
    }
}
