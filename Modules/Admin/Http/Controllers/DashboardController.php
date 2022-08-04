<?php

namespace Modules\Admin\Http\Controllers;

use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Modules\Admin\Services\DashboardService;
use Modules\Common\Entities\Currency;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Transaction;
use Modules\Common\Entities\Wallet;
use Modules\Core\Controllers\BaseController;
use Modules\Profile\Http\Requests\StatisticPeriodRequest;

/**
 * Class DashboardController
 *
 * @package Modules\Admin\Http\Controllers
 */
class DashboardController extends BaseController
{
    protected DashboardService $dashboardService;

    public function __construct(
        DashboardService $dashboardService
    ) {
        $this->dashboardService = $dashboardService;
        parent::__construct();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $currencyId = Currency::ID_EUR;
        if ($this->getCacheService()->get('dashboard') == null) {
            $this->getCacheService()->set(
                'dashboard',
                [
                    'investorCountByStatus' => Investor::getInvestorsByStatus(
                        [
                            Investor::INVESTOR_STATUS_VERIFIED
                        ]
                    ),
                    'walletSum' => Wallet::sumWallet($currencyId),
                    'investorsWithDeposit' => Investor::getInvestorsWithDeposit(),
                    'allRegisteredInvestors' => Investor::getRegisteredInvestors(),
                    'administrator' => \Auth::user()
                ],
                600
            );
        }

        $dashboard = $this->getCacheService()->get('dashboard');

        return view(
            'admin::dashboard.index',
            json_decode(json_encode($dashboard), true)
        );
    }

    /**
     * @param StatisticPeriodRequest $request
     * @return JsonResponse
     */
    public function registeredPerDay(StatisticPeriodRequest $request): JsonResponse
    {
        $days = $request->validated();

        $registeredPerDay = $this->dashboardService->prepareDataForCharts(
            Investor::getInvestorsActivityPerDay($days['days']),
            $days['days']
        );

        return Response::json(
            [
                array_values($registeredPerDay['registered'] ?? ['null' => '']),
                array_values($registeredPerDay['verified'] ?? ['null' => '']),
                array_values($registeredPerDay['deposit'] ?? ['null' => '']),
            ]
        );
    }

    /**
     * @param StatisticPeriodRequest $request
     * @return JsonResponse
     */
    public function transactionPerDay(StatisticPeriodRequest $request): JsonResponse
    {
        $days = $request->validated();
        $this->getCacheService()->set('dashboard', [], 1);

        $this->dashboardService->updateDaysStatistic(\Auth::user()->administrator_id, $days['days']);

        $transactionPerDay = $this->dashboardService->prepareDataForCharts(
            Transaction::getTransactionsPerDay($days['days']),
            $days['days']
        );

        return Response::json(
            [
                array_values($transactionPerDay['investment'] ?? ['null' => '']),
                array_values($transactionPerDay['deposit'] ?? ['null' => '']),
                array_values($transactionPerDay['withdraw'] ?? ['null' => '']),
            ]
        );
    }
}
