<?php

namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Http\Requests\InvestorLoginLogSearchRequest;
use Modules\Common\Services\InvestorLoginLogService;
use Modules\Core\Controllers\BaseController;

class InvestorLoginLogController extends BaseController
{
    protected InvestorLoginLogService $investorLoginLogService;

    public function __construct(InvestorLoginLogService $investorLoginLogService)
    {
        $this->investorLoginLogService = $investorLoginLogService;

        parent::__construct();
    }

    /**
     * @param InvestorLoginLogSearchRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(InvestorLoginLogSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::investor-login-log.list',
            [
                'investorLoginLogs' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @param InvestorLoginLogSearchRequest $request
     */
    protected function checkForRequestParams(
        InvestorLoginLogSearchRequest $request
    ) {
        if ($request->exists(
            ['id', 'email', 'active', 'limit']
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        return $this->investorLoginLogService->getByWhereConditions(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }

    /**
     * @param InvestorLoginLogSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refresh(InvestorLoginLogSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::investor-login-log.list-table',
            [
                'investorLoginLogs' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * @param $investorLoginLogId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove($investorLoginLogId)
    {
        $this->investorLoginLogService->delete($investorLoginLogId);

        return redirect()
            ->route('admin.investor-login-log.list')
            ->with('success', __('common.investorLoginLogDeletedSuccessfully'));
    }
}
