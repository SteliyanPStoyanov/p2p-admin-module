<?php

namespace Tests\Unit\Calculation;

use Carbon\Carbon;
use Modules\Common\Libraries\Calculator\InstallmentCalculator as Calc;
use Tests\TestCase;

class DatesDifferenceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testDaysBetweenDates()
    {
        $data = [
            0 => [
                'from' => '2020-01-05',
                'to' => '2020-02-05',
                'days' => 31,
            ],
            1 => [
                'from' => '2020-01-05',
                'to' => '2020-02-05',
                'days' => 31,
            ],
            2 => [
                'from' => '2020-07-19',
                'to' => '2020-08-19',
                'days' => 31,
            ],
            3 => [
                'from' => '2020-10-19',
                'to' => '2020-11-19',
                'days' => 31,
            ],
            4 => [
                'from' => '2020-11-19',
                'to' => '2020-12-19',
                'days' => 30,
            ],
            5 => [
                'from' => '2020-12-03',
                'to' => '2021-01-03',
                'days' => 31,
            ],
            6 => [
                'from' => '2021-04-03',
                'to' => '2021-05-03',
                'days' => 30,
            ],
            7 =>[
                'from' => '2021-06-03',
                'to' => '2021-07-03',
                'days' => 30,
            ],
            8 =>[
                'from' => '2021-02-03',
                'to' => '2021-03-03',
                'days' => 28,
            ],
            9 =>[
                'from' => '2021-01-03',
                'to' => '2021-02-03',
                'days' => 31,
            ],
        ];

        foreach ($data as $key => $el) {
            $from = Carbon::parse($el['from']);
            $to = Carbon::parse($el['to']);


            // dump( $el['from'], $el['to'], Calc::simpleDateDiff( $from, $to ), $el['days'] );
            $this->assertEquals(Calc::simpleDateDiff($from, $to), $el['days']);
        }


        $this->assertEquals(Calc::simpleDateDiff(Carbon::parse("2020-10-19"), Carbon::parse("2020-11-19")), 31);
        $this->assertEquals(Calc::simpleDateDiff(Carbon::parse("2020-06-30"), Carbon::parse("2020-07-19")), 19);
        $this->assertEquals(Calc::simpleDateDiff(Carbon::parse("2020-07-30"), Carbon::parse("2020-08-19")), 20);
        $this->assertEquals(Calc::simpleDateDiff(Carbon::parse("2020-11-02"), Carbon::parse("2020-12-03")), 31);

    }
}
