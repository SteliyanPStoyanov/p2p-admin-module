<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\Handlers\IncludeInvested;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class IncludeInvestedTest extends TestCase
{
    public function testHandler(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'include_invested' => '0',
        ];

        $data2 = [
            'my_investment' => 'exclude',
        ];

        $includeInvested = new IncludeInvested();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            'include_invested' => [
                0 => "not exists(select i.loan_id FROM investment as i WHERE loan.loan_id = i.loan_id AND i.investor_id = ?)",
                'whereRaw' => 1,
            ],
        ];

        // Test first alias
        $this->assertEquals(
            $expectedArray,
            $includeInvested->handle($dataWrapper, $next)->getWhere()
        );

        // Test second alias
        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data2);

        $this->assertEquals(
            $expectedArray,
            $includeInvested->handle($dataWrapper, $next)->getWhere()
        );

    }
}
