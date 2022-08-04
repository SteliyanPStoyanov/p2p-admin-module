<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline\Handlers;

use Illuminate\Database\Query\Builder;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\Loan\WherePipeline\Handlers\LoanStatus;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider;

class LoanStatusTest extends TestCase
{
    public function testHandlerStatusActive(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'status' => 'active',
        ];

        $loanStatus = new LoanStatus();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            [
                'loan.status',
                '=',
                'active',
            ]
        ];

        $this->assertEquals(
            $expectedArray,
            $loanStatus->handle($dataWrapper, $next)->getWhere()
        );
    }

    public function testHandlerStatusNotActive()
    {
        $builder = $this->createMock(Builder::class);

        $data = [
            'status' => 'inactive',
        ];

        $loanStatus = new LoanStatus();

        list($dataWrapper, $next) = DataProvider::forHandlers($builder, $data);

        $expectedArray = [
            'loan.status' => [
                Loan::STATUS_REPAID_EARLY,
                Loan::STATUS_REPAID,
                Loan::STATUS_REBUY,
                'whereIn' => 1
            ],
        ];

        $this->assertEquals(
            $expectedArray,
            $loanStatus->handle($dataWrapper, $next)->getWhere()
        );
    }
}
