<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Modules\Admin\Http\Requests\InvestStrategyHistorySearchRequest;
use Modules\Admin\Http\Requests\InvestStrategySearchRequest;
use Modules\Common\Entities\Loan;
use Modules\Common\Exports\InvestStrategyExport;
use Modules\Common\Services\InvestStrategyHistoryService;
use Modules\Common\Services\InvestStrategyService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\NotFoundException;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class InvestStrategyController extends BaseController
{
    protected InvestStrategyService $investStrategyService;
    protected InvestStrategyHistoryService $investStrategyHistoryService;

    protected string $historyKey = 'filters.investStrategy.history';

    public function __construct(
        InvestStrategyService $investStrategyService,
        InvestStrategyHistoryService $investStrategyHistoryService
    )
    {
        $this->investStrategyService = $investStrategyService;
        $this->investStrategyHistoryService = $investStrategyHistoryService;

        parent::__construct();
    }

    /**
     * @return Application|Factory|View
     */
    public function list()
    {
        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::invest-strategy.list',
            [
                'cacheKey' => $this->cacheKey,
                'investStrategies' => $this->getTableData(),
                'loanTypes' => Loan::getTypesWithLabels(),
                'loanPaymentStatuses' => Loan::getPaymentStatuses(),
            ]
        );
    }

    /**
     * @param InvestStrategySearchRequest $request
     *
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|string
     *
     * Format:
     *     #total: 50
     *     #lastPage: 5
     *     #items: Illuminate\Support\Collection
     *     #perPage: 10
     *     #currentPage: 1
     *     #path: "http://localhost:7000/profile/invest"
     *
     * @throws Throwable
     */
    public function refresh(InvestStrategySearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::invest-strategy.list-table',
            [
                'investStrategies' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * @param int $investStrategyId
     *
     * @return Application|Factory|View
     *
     * @throws NotFoundException
     */
    public function overview(int $investStrategyId)
    {
        $investStrategy = $this->investStrategyService->getById($investStrategyId);

        $this->getSessionService()->add($this->cacheKey, []);
        $this->getSessionService()->add($this->historyKey, []);

        return view(
            'admin::invest-strategy.overview',
            [
                'investStrategy' => $investStrategy,
                'loans' => $this->getLoanTableData($investStrategyId),
                'cacheKey' => $this->cacheKey,
                'investStrategyHistory' => $this->getTableHistoryData($investStrategyId),
                'loanTypes' => Loan::getTypesWithLabels(),
                'loanPaymentStatuses' => Loan::getPaymentStatuses(),
            ]
        );
    }

    /**
     * @param int $investStrategyId
     * @return array|string
     * @throws Throwable
     */
    public function refreshLoan(int $investStrategyId)
    {
        return view(
            'admin::invest-strategy.loan-list',
            [
                'loans' => $this->getLoanTableData($investStrategyId),
            ]
        )->render();
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        return $this->investStrategyService->getAllStrategy(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }

    /**
     * @param int $investStrategyId
     *
     * @return mixed
     */
    protected function getLoanTableData(int $investStrategyId)
    {
        return $this->investStrategyService->getStrategyLoans(
            $investStrategyId,
            parent::getTableLength()
        );
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableHistoryData(int $investStrategyId, int $limit = null)
    {
        $this->cacheKey = $this->historyKey;

        return $this->investStrategyHistoryService->getAllStrategy(
            $limit ?? parent::getTableLength(),
            array_merge(session($this->cacheKey, []), ['invest_strategy_id' => $investStrategyId]),

        );
    }

    /**
     * @return StreamedResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export()
    {
        $strategy = $this->investStrategyService->getAllStrategy(
            null,
            session($this->cacheKey, [])
        );
        $strategyExport = new InvestStrategyExport($strategy);
        $fileName = 'invest-strategy-export-' . date('Y-m-d-H-i-s');

        return $this->getStorageService()->download(
            $fileName,
            ['collectionClass' => $strategyExport],
            'xlsx',
        );
    }

    /**
     * @param int $investStrategyId
     * @param InvestStrategyHistorySearchRequest $request
     *
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|string
     *
     * Format:
     *     #total: 50
     *     #lastPage: 5
     *     #items: Illuminate\Support\Collection
     *     #perPage: 10
     *     #currentPage: 1
     *     #path: "http://localhost:7000/profile/invest"
     *
     * @throws Throwable
     */
    public function refreshHistory(int $investStrategyId, InvestStrategyHistorySearchRequest $request)
    {

        parent::setFiltersFromRequest($request, $this->historyKey);

        return view(
            'admin::invest-strategy.history-list',
            [
                'investStrategyHistory' => $this->getTableHistoryData($investStrategyId),
                'loanTypes' => Loan::getTypesWithLabels(),
                'loanPaymentStatuses' => Loan::getPaymentStatuses(),
            ]
        )->render();
    }
}
