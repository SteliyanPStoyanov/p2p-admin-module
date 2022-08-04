<?php

namespace Modules\Admin\Services;

use Illuminate\Support\Carbon;
use Modules\Admin\Repositories\AdministratorRepository;
use Modules\Common\Entities\Investor;
use \Modules\Core\Services\BaseService;

class DashboardService extends BaseService
{
    private AdministratorRepository $administratorRepository;

    public function __construct(
        AdministratorRepository $administratorRepository
    ) {
        $this->administratorRepository = $administratorRepository;
        parent::__construct();
    }

    /**
     * @param array $data
     * @param int $days
     * @return array[]
     */
    public function prepareDataForCharts(array $data, int $days)
    {
        $coordinateMerge = [];
        $dataCoordinate = [];
        $dataCoordinateEmptyDays = [];

        foreach ($data as $day) {
            $dataCoordinate[$day['type']][$day['created']] = array(
                "x" => $day['created'],
                "y" => $day['number']
            );
        }

        foreach ($dataCoordinate as $type => $cordinate) {
            if ($days > 0 && count($cordinate) < $days) {
                for ($i = 0; $i <= $days; $i++) {
                    $date = Carbon::now()->subDay($i)->format('Y-m-d');
                    $dataCoordinateEmptyDays[$type][$date] = [
                        "x" => Carbon::now()->subDay($i)->format('Y-m-d'),
                        "y" => "0"
                    ];
                }
            } else {
                $dataCoordinateEmptyDays[$type] = $dataCoordinate[$type];
            }
            if (isset($dataCoordinate[$type]) && isset($dataCoordinateEmptyDays[$type])) {
                $coordinateMerge[$type] = array_merge(
                    $dataCoordinateEmptyDays[$type],
                    $dataCoordinate[$type]
                );
            }
        }

        return $coordinateMerge;
    }

    /**
     * @param $investor
     *
     * @return array
     */
    public function earnedIncomeChartMonth($investor): array
    {
        $labels = [];
        $income = [];
        $labelsEmptyDays = [];

        $month = Investor::INVESTOR_CHART_BAR_12;

        foreach ($investor->earnedIncomeStatisticsMonth($month) as $earned) {
            $labels[$earned->created] = $earned->created;
            $income[$earned->created] = $earned->earned;
        }

        for ($i = 0; $i <= $month; $i++) {
            $labelsEmptyDays[Carbon::now()->subMonth($i)->startOfMonth()->format('Y-m-d')] =
                Carbon::now()->subMonth($i)->startOfMonth()->format('Y-m-d');
        }

        $labelsNew = array_merge($labels, $labelsEmptyDays);

        return [
            'labels' => $labelsNew,
            'income' => $income,
            'unit' => 'month'
        ];
    }

    /**
     * @param $investor
     * @param $days
     *
     * @return array
     */
    public function earnedIncomeChartWeek($investor, $days): array
    {
        $labels = [];
        $income = [];
        $labelsEmptyDays = [];

        $weeks = $days < Investor::INVESTOR_STATISTIC_DAYS[3] ? Investor::INVESTOR_CHART_BAR_12 : Investor::INVESTOR_CHART_BAR_24;

        foreach ($investor->earnedIncomeStatisticsWeek($weeks) as $earned) {
            $labels[$earned->created] = $earned->created;
            $income[$earned->created] = $earned->earned;
        }

        for ($i = 0; $i <= $weeks; $i++) {
            $labelsEmptyDays[Carbon::now()->subWeek($i)->startOfWeek()->format('Y-m-d')] =
                Carbon::now()->subWeek($i)->startOfWeek()->format('Y-m-d');
        }

        $labelsNew = array_merge($labels, $labelsEmptyDays);

        return [
            'labels' => $labelsNew,
            'income' => $income,
            'unit' => 'week'
        ];
    }

    /**
     * @param $investor
     * @param $days
     *
     * @return array
     */
    public function earnedIncomeChartDays($investor, $days): array
    {
        $labels = [];
        $income = [];
        $labelsEmptyDays = [];

        foreach ($investor->earnedIncomeStatisticsDays($days) as $earned) {
            $labels[$earned->created] = $earned->created;
            $income[$earned->created] = $earned->earned;
        }

        if ($days > 0 && count($labels) < $days) {
            for ($i = 0; $i <= $days; $i++) {
                $labelsEmptyDays[Carbon::now()->subDay($i)->format('Y-m-d')] = Carbon::now()->subDay($i)->format(
                    'Y-m-d'
                );
            }
        }
        if ($days == 0) {
            $date = Carbon::parse($investor->firstTransactionDate()['created_at']);
            $now = Carbon::now();

            $diff = $date->diffInDays($now);

            for ($i = 1; $i <= $diff; $i++) {
                $labelsEmptyDays[Carbon::now()->subDay($i)->format('Y-m-d')] = Carbon::now()->subDay($i)->format(
                    'Y-m-d'
                );
            }
        }

        $labelsNew = array_merge($labels, $labelsEmptyDays);

        return [
            'labels' => $labelsNew,
            'income' => $income,
            'unit' => 'day'
        ];
    }

    /**
     * @param $investor
     * @param $days
     *
     * @return array
     */
    public function outstandingBalanceChartDays($investor, $days): array
    {
        $labels = [];
        $income = [];
        $labelsEmptyDays = [];

        $rows = $investor->investedStatisticsDays($days);


        foreach ($rows as $earned) {
            $labels[$earned->created] = $earned->created;
            $income[$earned->created] = $earned->invested;
        }

        if ($days > 0 && count($labels) < $days) {
            for ($i = 0; $i <= $days; $i++) {
                $labelsEmptyDays[Carbon::now()->subDay($i)->format('Y-m-d')] = Carbon::now()->subDay($i)->format(
                    'Y-m-d'
                );
            }
        }
        if ($days == 0) {
            $date = Carbon::parse($investor->firstTransactionDate()['created_at']);
            $now = Carbon::now();

            $diff = $date->diffInDays($now);

            for ($i = 1; $i <= $diff; $i++) {
                $labelsEmptyDays[Carbon::now()->subDay($i)->format('Y-m-d')] = Carbon::now()->subDay($i)->format(
                    'Y-m-d'
                );
            }
        }

        $labelsNew = array_merge($labels, $labelsEmptyDays);

        return [
            'labels' => $labelsNew,
            'income' => $income,
            'unit' => 'day'
        ];
    }

    /**
     * @param $investor
     * @param $days
     *
     * @return array
     */
    public function outstandingBalanceChartWeek($investor, $days): array
    {
        $labels = [];
        $income = [];
        $labelsEmptyDays = [];

        $week = $days < Investor::INVESTOR_STATISTIC_DAYS[3] ? Investor::INVESTOR_CHART_BAR_12 : Investor::INVESTOR_CHART_BAR_24;

        $rows = $investor->investedStatisticsWeek($week);

        foreach ($rows as $earned) {
            $labels[$earned->created] = $earned->created;
            $income[$earned->created] = $earned->invested;
        }


        for ($i = 0; $i <= $week; $i++) {
            $labelsEmptyDays[Carbon::now()->subWeek($i)->endOfWeek()->format('Y-m-d')] = Carbon::now()->subWeek(
                $i
            )->endOfWeek()->format(
                'Y-m-d'
            );
        }

        $labelsNew = array_merge($labels, $labelsEmptyDays);

        return [
            'labels' => $labelsNew,
            'income' => $income,
            'unit' => 'week'
        ];
    }

    /**
     * @param $investor
     *
     * @return array
     */
    public function outstandingBalanceChartMonth($investor): array
    {
        $labels = [];
        $income = [];
        $labelsEmptyDays = [];
        $month = Investor::INVESTOR_CHART_BAR_12;

        $rows = $investor->investedStatisticsMonth($month);

        foreach ($rows as $earned) {
            $labels[$earned->created] = $earned->created;
            $income[$earned->created] = $earned->invested;
        }


        for ($i = 0; $i <= $month; $i++) {
            $labelsEmptyDays[Carbon::now()->subMonth($i)->endOfMonth()->format('Y-m-d')] =
                Carbon::now()->subMonth($i)->endOfMonth()->format('Y-m-d');
        }

        $labelsNew = array_merge($labels, $labelsEmptyDays);

        return [
            'labels' => $labelsNew,
            'income' => $income,
            'unit' => 'month'
        ];
    }

    public function updateDaysStatistic(int $administratorId, int $days)
    {
        $this->administratorRepository->administratorUpdate($administratorId, ['statistic_days' => $days]);
    }
}
