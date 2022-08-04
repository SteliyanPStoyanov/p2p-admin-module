<?php
declare(strict_types=1);

namespace Tests\Unit\Modules\Common\Services\Loan\WherePipeline;

use Illuminate\Database\Query\Builder;
use Modules\Common\Services\Loan\WherePipeline\DataWrapper;
use PHPUnit\Framework\TestCase;

class DataWrapperTest extends TestCase
{
    public function testBuilder(): void
    {
        $builder = $this->createMock(Builder::class);

        $dataWrapper = new DataWrapper($builder, [], 0);

        $this->assertInstanceOf(Builder::class, $dataWrapper->getBuilder());
    }

    public function testData(): void
    {
        $builder = $this->createMock(Builder::class);

        $data = DataProvider::getDataVariantInvestments();

        $dataWrapper = new DataWrapper($builder, $data, 0);

        $this->assertEquals($data, $dataWrapper->getData());
    }

    public function testCompile(): void
    {
        $builder = $this->createMock(Builder::class);

        $dataWrapper = new DataWrapper($builder, [], 0);

        $this->assertInstanceOf(Builder::class, $dataWrapper->compile());
    }
}
