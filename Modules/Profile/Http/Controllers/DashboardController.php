<?php

namespace Modules\Profile\Http\Controllers;

use Carbon\Carbon;
use Modules\Admin\Services\DashboardService;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Core\Controllers\BaseController;
use Modules\Profile\Http\Requests\StatisticPeriodRequest;
use Modules\Profile\Http\Requests\StatisticTypeRequest;

class DashboardController extends BaseController
{
    private DashboardService $dashboardService;

    public function __construct(
        DashboardService $dashboardService
    ) {
        $this->dashboardService = $dashboardService;
        parent::__construct();
    }

    public function index()
    {
        try {
            $investor = $this->getInvestor();
            $cacheKey = config('profile.profileDashboard') . $investor->investor_id;
            $cacheTime = ($investor->status == config('profile.statusVerified') ? 60 : 5);

            if ($this->getCacheService()->get($cacheKey) == null) {
                $this->getCacheService()->set(
                    $cacheKey,
                    [
                        'investor' => $investor,
                        'wallet' => $investor->wallet(),
                        'portfolios' => $investor->portfolio(),
                        'remainingInvestments' => $investor->investorRemainingInvestment(),
                    ],
                    $cacheTime
                );
            }

            return view(
                'profile::dashboard.index',
                (array)$this->getCacheService()->get($cacheKey)
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param StatisticPeriodRequest $request
     *
     * @return false|string
     */
    public function earnedIncomeChart(StatisticPeriodRequest $request)
    {
        // TODO: change json_encode with response()-
        $days = $request->validated();
        try {
            $investor = $this->getInvestor();

            $this->investorRepository->investorUpdate($investor->investor_id, ['statistic_days' => $days['days']]);

            $cacheKey = config('profile.profileDashboard') . $investor->investor_id;
            $this->getCacheService()->set($cacheKey, [], 1);

            if ($days['days'] <= Investor::INVESTOR_STATISTIC_DAYS[1]) {
                $data = $this->dashboardService->earnedIncomeChartDays($investor, $days['days']);
            }

            if ($days['days'] >= Investor::INVESTOR_STATISTIC_DAYS[2] && $days['days'] <= Investor::INVESTOR_STATISTIC_DAYS[3]) {
                $data = $this->dashboardService->earnedIncomeChartWeek($investor, $days['days']);
            }

            if ($days['days'] > Investor::INVESTOR_STATISTIC_DAYS[3] && $days['days'] <= Investor::INVESTOR_STATISTIC_DAYS[4]) {
                $data = $this->dashboardService->earnedIncomeChartMonth($investor);
            }

            return json_encode(
                [
                    "labels" => [array_values($data['labels'])],
                    "data" => [
                        "income" => [array_values($data['income'])]
                    ],
                    'format' => $data['unit']
                ]
            );
        } catch (\Throwable $e) {
            return json_encode(
                [
                    "labels" => [],
                    "data" => [
                        "income" => [],
                    ],
                    'format' => null,
                ]
            );
        }
    }


    /**
     * @param StatisticPeriodRequest $request
     *
     * @return false|string
     */
    public function outstandingBalanceChart(StatisticPeriodRequest $request)
    {
        $days = $request->validated();
        try {
            $investor = $this->getInvestor();

            if ($days['days'] <= Investor::INVESTOR_STATISTIC_DAYS[1]) {
                $data = $this->dashboardService->outstandingBalanceChartDays($investor, $days['days']);
            }

            if ($days['days'] >= Investor::INVESTOR_STATISTIC_DAYS[2] && $days['days'] <= Investor::INVESTOR_STATISTIC_DAYS[3]) {
                $data = $this->dashboardService->outstandingBalanceChartWeek($investor, $days['days']);
            }

            if ($days['days'] > Investor::INVESTOR_STATISTIC_DAYS[3] && $days['days'] <= Investor::INVESTOR_STATISTIC_DAYS[4]) {
                $data = $this->dashboardService->outstandingBalanceChartMonth($investor);
            }

            return json_encode(
                [
                    "labels" => [array_values($data['labels'])],
                    "data" => [
                        "income" => [array_values($data['income'])]
                    ],
                    'format' => $data['unit']
                ]
            );
        } catch (\Throwable $e) {
            return json_encode(
                [
                    "labels" => [],
                    "data" => [
                        "income" => [],
                    ],
                    'format' => null,
                ]
            );
        }
    }

    /**
     * @param StatisticTypeRequest $request
     *
     * @return false|string
     */
    public function loanByAmount(StatisticTypeRequest $request)
    {
        $chartType = $request->validated();
        try {

            $investor = $this->getInvestor();

            $labels = [];
            $income = [];
            $total = 0;

            if ($chartType['type'] == 'by_amount') {
                $total = array_sum($investor->investorRemainingInvestment());

                foreach (Loan::getPaymentStatuses() as $status) {
                    $labels[] = payStatusCharts($status);
                    if (isset($investor->investorRemainingInvestment()[$status])) {
                        $income[] = ($investor->investorRemainingInvestment()[$status] / $total) * 100;
                    } else {
                        $income[] = 0;
                    }
                }
            }

            if ($chartType['type'] == 'by_number') {
                $total = array_sum($investor->investorRemainingInvestmentLoans());

                foreach (Loan::getPaymentStatuses() as $status) {
                    $labels[] = payStatusCharts($status);
                    if (isset($investor->investorRemainingInvestmentLoans()[$status])) {
                        $income[] = ($investor->investorRemainingInvestmentLoans()[$status] / $total) * 100;
                    } else {
                        $income[] = 0;
                    }
                }
            }

            return json_encode(
                [
                    "labels" => [$labels],
                    "data" => [
                        "income" => [$income]
                    ],
                    "total" => $total,
                    "chartType" => $chartType['type']
                ]
            );
        } catch (\Throwable $e) {
            return json_encode(
                [
                    "labels" => [],
                    "data" => [
                        "income" => []
                    ],
                    "total" => null,
                    "chartType" => null,
                ]
            );
        }
    }

    /**
     * @param StatisticTypeRequest $request
     *
     * @return false|string
     */
    public function loanByAmountTerm(StatisticTypeRequest $request)
    {
        $chartType = $request->validated();

        try {

            $investor = $this->getInvestor();

            $labels = [];
            $income = [];
            $ranges = [];

            $total = 0;


            if ($chartType['type'] == 'by_amount') {
                foreach (Portfolio::getMaturityStatuses() as $status) {
                    if (isset($investor->investorRemainingInvestmentTerm()[$status])) {
                        $total += array_sum($investor->investorRemainingInvestmentTerm()[$status]);
                        $ranges[$status] = array_sum($investor->investorRemainingInvestmentTerm()[$status]);
                    } else {
                        $ranges[$status] = 0;
                    }
                }
                foreach ($ranges as $range => $value) {
                    $labels[] = Portfolio::getMaturityChartsMapping($range, false);
                    if ($value > 0) {
                        $income[] = number_format(($value / $total) * 100, 1);
                    } else {
                        $income[] = 0;
                    }
                }
            }

            if ($chartType['type'] == 'by_number') {
                foreach (Portfolio::getMaturityStatuses() as $status) {
                    $labels[] = Portfolio::getMaturityChartsMapping($status, false);
                    if (isset($investor->investorRemainingInvestmentTermNumber()[$status])) {
                        $total += count($investor->investorRemainingInvestmentTermNumber()[$status]);
                        $income[] = count($investor->investorRemainingInvestmentTermNumber()[$status]);
                    } else {
                        $income[] = 0;
                    }
                }
            }

            return json_encode(
                [
                    "labels" => [$labels],
                    "data" => [
                        "income" => [$income]
                    ],
                    "total" => $total,
                    "chartType" => $chartType['type']
                ]
            );
        } catch (\Throwable $e) {
            return json_encode(
                [
                    "labels" => [],
                    "data" => [
                        "income" => []
                    ],
                    "total" => null,
                    "chartType" => null,
                ]
            );
        }
    }
}
