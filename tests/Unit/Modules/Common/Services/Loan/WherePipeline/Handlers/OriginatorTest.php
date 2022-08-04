<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\Originator;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class OriginatorTest extends TestCase
{
    public function testHandler(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'originator' => 1,
        ];

        $originator = new Originator();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            [
                'loan.originator_id',
                '=',
                1,
            ]
        ];

        $this->assertEquals(
            $expectedArray,
            $originator->handle($dataWrapper, $next)->getWhere()
        );
    }
}
