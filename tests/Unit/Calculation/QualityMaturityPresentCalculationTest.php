<?php

namespace Tests\Unit\Calculation;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Libraries\Calculator\Calculator;
use Tests\TestCase;
use Tests\Traits\TestDataTrait;

class QualityMaturityPresentCalculationTest extends TestCase
{
    use WithoutMiddleware;
    use TestDataTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testPresentCalculationsQualityMaturity()
    {
        // clear, to use again same investor
        $investor = $this->getInvestor();
        $this->removeTestData($investor);

        // new investor
        $investor = $this->prepareInvestor();

        $portfolioQuality = new Portfolio();
        $portfolioQuality->investor_id = $investor->investor_id;
        $portfolioQuality->currency_id = Currency::ID_EUR;
        $portfolioQuality->type = Portfolio::PORTFOLIO_TYPE_QUALITY;
        $portfolioQuality->date = Carbon::now();
        $portfolioQuality->range1 = 3;
        $portfolioQuality->range2 = 2;
        $portfolioQuality->range3 = 1;
        $portfolioQuality->range4 = 3;
        $portfolioQuality->range5 = 1;

        $portfolioQuality->save();


        $portfolioMaturity = new Portfolio();
        $portfolioMaturity->investor_id = $investor->investor_id;
        $portfolioMaturity->currency_id = Currency::ID_EUR;
        $portfolioMaturity->type = Portfolio::PORTFOLIO_TYPE_MATURITY;
        $portfolioMaturity->date = Carbon::now();
        $portfolioMaturity->range1 = 5;
        $portfolioMaturity->range2 = 4;
        $portfolioMaturity->range3 = 3;
        $portfolioMaturity->range4 = 6;
        $portfolioMaturity->range5 = 2;

        $portfolioMaturity->save();

        // validate portfolios quality
        $portfolioQuality->refresh();
        $investorQuality = $investor->quality();

        $this->assertEquals('quality', $portfolioQuality->type);
        $this->assertEquals($investorQuality->type, $portfolioQuality->type);
        $this->assertEquals(3, $portfolioQuality->range1);
        $this->assertEquals(2, $portfolioQuality->range2);
        $this->assertEquals(1, $portfolioQuality->range3);
        $this->assertEquals($investorQuality->range3, $portfolioQuality->range3);
        $this->assertEquals(3, $portfolioQuality->range4);
        $this->assertEquals(1, $portfolioQuality->range5);

        $qualityRangeArray = [
            'range1' => $portfolioQuality->range1,
            'range2' => $portfolioQuality->range2,
            'range3' => $portfolioQuality->range3,
            'range4' => $portfolioQuality->range4,
            'range5' => $portfolioQuality->range5,
        ];

        $this->assertEquals(30, Calculator::getPortfolioRangesPresent($qualityRangeArray, $portfolioQuality->range1));
        $this->assertEquals(20, Calculator::getPortfolioRangesPresent($qualityRangeArray, $portfolioQuality->range2));
        $this->assertEquals(10, Calculator::getPortfolioRangesPresent($qualityRangeArray, $portfolioQuality->range3));
        $this->assertEquals(30, Calculator::getPortfolioRangesPresent($qualityRangeArray, $portfolioQuality->range4));
        $this->assertEquals(10, Calculator::getPortfolioRangesPresent($qualityRangeArray, $portfolioQuality->range5));

        $portfolioMaturity->refresh();
        $investorMaturity = $investor->maturity();

        $this->assertEquals('maturity', $portfolioMaturity->type);
        $this->assertEquals($investorMaturity->type, $portfolioMaturity->type);
        $this->assertEquals(5, $portfolioMaturity->range1);
        $this->assertEquals(4, $portfolioMaturity->range2);
        $this->assertEquals(3, $portfolioMaturity->range3);
        $this->assertEquals($investorMaturity->range3, $portfolioMaturity->range3);
        $this->assertEquals(6, $portfolioMaturity->range4);
        $this->assertEquals(2, $portfolioMaturity->range5);

        $maturityRangeArray = [
            'range1' => $portfolioMaturity->range1,
            'range2' => $portfolioMaturity->range2,
            'range3' => $portfolioMaturity->range3,
            'range4' => $portfolioMaturity->range4,
            'range5' => $portfolioMaturity->range5,
        ];

        $this->assertEquals(25, Calculator::getPortfolioRangesPresent($maturityRangeArray, $portfolioMaturity->range1));
        $this->assertEquals(20, Calculator::getPortfolioRangesPresent($maturityRangeArray, $portfolioMaturity->range2));
        $this->assertEquals(15, Calculator::getPortfolioRangesPresent($maturityRangeArray, $portfolioMaturity->range3));
        $this->assertEquals(30, Calculator::getPortfolioRangesPresent($maturityRangeArray, $portfolioMaturity->range4));
        $this->assertEquals(10, Calculator::getPortfolioRangesPresent($maturityRangeArray, $portfolioMaturity->range5));

        // remove test data
        $this->removeTestData($investor);
    }

    public function tearDown(): void
    {
        $this->beforeApplicationDestroyed(
            function () {
                DB::disconnect();
            }
        );
        parent::tearDown();
    }
}
