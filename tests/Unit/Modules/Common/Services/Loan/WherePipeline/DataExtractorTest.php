<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline;

use Modules\Common\Services\Loan\WherePipeline\DataExtractor;
use PHPUnit\Framework\TestCase;

class DataExtractorTest extends TestCase
{
    /**
     * @param array $data
     * @dataProvider \Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider::getData()
     */
    public function testExtractString(array $data): void
    {
        // Test special case for include invested
        $this->assertTrue('0' === DataExtractor::extractString($data, 'include_invested'));

        $this->assertEquals('15', DataExtractor::extractString($data, 'min_interest_rate'));
    }

    /**
     * @param array $data
     * @dataProvider \Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider::getData()
     */
    public function testExtractStringFromSubArray(array $data): void
    {
        $this->assertEquals('08.03.2021', DataExtractor::extractStringFromSubArray($data, ['created_at' => 'from']));

        $this->assertEquals('1', DataExtractor::extractStringFromSubArray($data, ['period' => 'from']));
    }

    /**
     * @param array $data
     * @dataProvider \Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider::getData()
     */
    public function testExtractArraySimple(array $data): void
    {
        $expectedArray1 = [
            'to' => 101,
            'from' => 11,
        ];

        $expectedArray2 = [
            'from' => 1,
            'to' => 10,
        ];

        $this->assertEquals($expectedArray1, DataExtractor::extractArraySimple($data, 'amount_available'));

        $this->assertEquals($expectedArray2, DataExtractor::extractArraySimple($data, 'period'));
    }

    /**
     * @param array $data
     * @dataProvider \Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider::getData()
     */
    public function testExtractArrayFromSubArray(array $data): void
    {
        $expectedArray = [
            'sub' => [
                'array' => 1
            ],
        ];

        $this->assertEquals($expectedArray, DataExtractor::extractArrayFromSubArray($data, ['test' => 'sub']));
    }

    /**
     * @param array $data
     * @dataProvider \Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider::getData()
     */
    public function testExtract(array $data): void
    {
        $this->assertTrue('0' === DataExtractor::extract(['include_invested'], $data));
        $this->assertEquals('exclude', DataExtractor::extract(['my_investment'], $data));

        $aliases = [
            'min_interest_rate',
            ['interest_rate_percent' => 'from']
        ];

        $this->assertEquals('15', DataExtractor::extract($aliases, $data));
    }

    /**
     * @param array $data
     *
     * @dataProvider \Tests\Unit\Modules\Common\Services\Loan\WherePipeline\DataProvider::getData()
     */
    public function testExtractArray(array $data): void
    {
        $expectedArray1 = [
            'current',
            '1-15 days'
        ];

        $this->assertEquals($expectedArray1, DataExtractor::extractArray(['loan_payment_status'], $data));

        $expectedArray2 = [
            'sub' => [
                'array' => 1
            ]
        ];

        $alias = [
            ['test' => 'sub'],
        ];

        $this->assertEquals($expectedArray2, DataExtractor::extractArray($alias, $data));
    }
}
