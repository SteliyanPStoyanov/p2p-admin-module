<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\Http\Requests\MongoLogSearchRequest;
use Modules\Common\Entities\CronLog;
use Modules\Common\Services\MongoLogService;
use Modules\Core\Controllers\BaseController;

class MongoLogController extends BaseController
{
    protected MongoLogService $mongoLogService;

    public function __construct(MongoLogService $mongoLogService)
    {
        $this->mongoLogService = $mongoLogService;

        parent::__construct();
    }

    /**
     * @param $adapterKey
     * @param MongoLogSearchRequest $request
     *
     * @return \Illuminate\View\View
     */
    public function list($adapterKey, MongoLogSearchRequest $request)
    {
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::mongo-logs.list',
            [
                'mongoLogs' => $this->getTableData($adapterKey),
                'cacheKey' => $this->cacheKey,
                'adapterKey' => $adapterKey,
            ]
        );
    }

    public function delete($adapterKey, $id)
    {
        $this->mongoLogService->delete($adapterKey, $id);

        return redirect()->back()->with('success', 'Success!');
    }

    /**
     * @param $adapterKey
     * @param MongoLogSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refresh($adapterKey, MongoLogSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::mongo-logs.list-table',
            [
                'mongoLogs' => $this->getTableData($adapterKey),
                'adapterKey' => $adapterKey,
            ]
        )->render();
    }

    /**
     * @param string $adapterKey
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData(string $adapterKey, int $limit = 10)
    {
        return $this->mongoLogService->getByWhereConditions(
            $limit ?? 10,
            session($this->cacheKey, []),
            $adapterKey
        );
    }
}
