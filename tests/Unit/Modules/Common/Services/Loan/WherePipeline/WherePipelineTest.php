<?php

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;
use Modules\Common\Services\Loan\WherePipeline\WherePipeline;
use PHPUnit\Framework\TestCase;

class WherePipelineTest extends TestCase
{
    public function testRunAutoInvest(): void
    {
        $data = DataProvider::getDataVariantAutoInvest();

        $expectedArray = [
            [
                'loan.amount_available',
                '<=',
                '100'
            ],
            [
                'loan.amount_available',
                '>=',
                '10'
            ],
            'include_invested' => [
                'not exists(select i.loan_id FROM investment as i WHERE loan.loan_id = i.loan_id AND i.investor_id = ?)',
                'whereRaw' => 1,
            ],
            [
                'loan.interest_rate_percent',
                '>=',
                '15'
            ],
            [
                'loan.interest_rate_percent',
                '<=',
                '18'
            ],
            'loan.payment_status' => [
                'current',
                '1-15 days',
                'whereIn' => 1,
            ],
            [
                'loan.final_payment_date',
                '>=',
                dbDate(Carbon::now()->addMonths(1)),
            ],
            [
                'loan.final_payment_date',
                '<=',
                dbDate(Carbon::now()->addMonths(10)),
            ],

            'loan.type' => [
                'payday',
                'installments',
                'whereIn' => 1
            ],
        ];

        sort($expectedArray);

        $builder = $this->createMock(Builder::class);

        $result = WherePipeline::run($builder, $data, 0);

        $this->assertInstanceOf(DataWrapper::class, $result);

        $where = $result->getWhere();
        sort($where);

        $this->assertEquals($expectedArray, $where);


    }

    public function testRunInvest(): void
    {
        $data = DataProvider::getDataVariantInvestments();

        $expectedArray = [
            [
                'loan.amount_available',
                '<=',
                '101'
            ],
            [
                'loan.amount_available',
                '>=',
                '11'
            ],
            'include_invested' => [
                'not exists(select i.loan_id FROM investment as i WHERE loan.loan_id = i.loan_id AND i.investor_id = ?)',
                'whereRaw' => 1,
            ],
            [
                'loan.interest_rate_percent',
                '>=',
                '15'
            ],
            [
                'loan.interest_rate_percent',
                '<=',
                '18'
            ],
            'loan.payment_status' => [
                'current',
                '1-15 days',
                'whereIn' => 1,
            ],
            [
                'loan.final_payment_date',
                '>=',
                dbDate(Carbon::now()->addMonths(1)),
            ],
            [
                'loan.final_payment_date',
                '<=',
                dbDate(Carbon::now()->addMonths(10)),
            ],

            'loan.type' => [
                'installments',
                'whereIn' => 1
            ],
            [
                'loan.created_at',
                '>=',
                '2021-03-08 00:00:00'
            ],
            [
                'loan.created_at',
                '<=',
                '2021-03-09 00:00:00'
            ],
        ];

        sort($expectedArray);

        $builder = $this->createMock(Builder::class);

        $result = WherePipeline::run($builder, $data, 0);

        $this->assertInstanceOf(DataWrapper::class, $result);

        $where = $result->getWhere();
        sort($where);

        $this->assertEquals($expectedArray, $where);
    }

    public function testRunAdmin(): void
    {
        $data = DataProvider::getDataVariantInvestments();

        $expectedArray = [
            [
                'loan.amount_available',
                '<=',
                '101'
            ],
            [
                'loan.amount_available',
                '>=',
                '11'
            ],
            'include_invested' => [
                'not exists(select i.loan_id FROM investment as i WHERE loan.loan_id = i.loan_id AND i.investor_id = ?)',
                'whereRaw' => 1,
            ],
            [
                'loan.interest_rate_percent',
                '>=',
                '15'
            ],
            [
                'loan.interest_rate_percent',
                '<=',
                '18'
            ],
            'loan.payment_status' => [
                'current',
                '1-15 days',
                'whereIn' => 1,
            ],
            [
                'loan.final_payment_date',
                '>=',
                dbDate(Carbon::now()->addMonths(1)),
            ],
            [
                'loan.final_payment_date',
                '<=',
                dbDate(Carbon::now()->addMonths(10)),
            ],

            'loan.type' => [
                'installments',
                'whereIn' => 1
            ],
            [
                'loan.created_at',
                '>=',
                '2021-03-08 00:00:00'
            ],
            [
                'loan.created_at',
                '<=',
                '2021-03-09 00:00:00'
            ],
        ];

        sort($expectedArray);

        $builder = $this->createMock(Builder::class);

        $result = WherePipeline::run($builder, $data, 0);

        $this->assertInstanceOf(DataWrapper::class, $result);

        $where = $result->getWhere();
        sort($where);

        $this->assertEquals($expectedArray, $where);
    }
}
