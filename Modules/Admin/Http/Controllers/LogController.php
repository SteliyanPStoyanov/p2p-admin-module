<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Modules\Admin\Http\Requests\HistoryLogRequest;
use Modules\Common\Entities\CronLog;
use Modules\Common\Services\HistoryLogService;
use Modules\Core\Controllers\BaseController;
use Throwable;

class LogController extends BaseController
{

    protected HistoryLogService $historyLogService;

    public function __construct(HistoryLogService $historyLogService)
    {
        $this->historyLogService = $historyLogService;
        parent::__construct();
    }

    /**
     * @param HistoryLogRequest $request
     *
     * @return View
     */
    public function list(HistoryLogRequest $request): View
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::logs.list',
            [
                'cronLogs' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @param HistoryLogRequest $request
     *
     * @return array|string
     * @throws Throwable
     */
    public function refresh(HistoryLogRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::logs.list-table',
            [
                'cronLogs' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData(int $limit = 50)
    {
        $cachedData = $this->getCachedData(CronLog::class);

        if ($cachedData === null) {
            $cachedData = $this->historyLogService->getByWhereConditions(
                $limit,
                session($this->cacheKey, [])
            );
            $this->setCacheData($cachedData);
        }

        return $cachedData;
    }

    /**
     * @param HistoryLogRequest $request
     */
    protected function checkForRequestParams(HistoryLogRequest $request)
    {
        if ($request->exists(
            ['createdAt', 'command']
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }
}
