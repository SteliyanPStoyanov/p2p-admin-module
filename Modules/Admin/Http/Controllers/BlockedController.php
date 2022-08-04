<?php

namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Http\Requests\BlockedIpSearchRequest;
use Modules\Common\Entities\BlockedIp;
use Modules\Common\Entities\LoginAttempt;
use Modules\Common\Entities\RegistrationAttempt;
use Modules\Common\Services\BlockedIpService;
use Modules\Core\Controllers\BaseController;

class BlockedController extends BaseController
{

    protected BlockedIpService $blockedIpService;

    public function __construct(BlockedIpService $blockedIpService)
    {
        $this->blockedIpService = $blockedIpService;

        parent::__construct();
    }

    /**
     * @param BlockedIpSearchRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(BlockedIpSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::blocked.list',
            [
                'blockedIps' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @param BlockedIpSearchRequest $request
     */
    protected function checkForRequestParams(
        BlockedIpSearchRequest $request
    ) {
        if ($request->exists(
            ['id', 'ip', 'active', 'limit']
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
        return $this->blockedIpService->getByWhereConditions(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }

    /**
     * @param BlockedIpSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refresh(BlockedIpSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::blocked.list-table',
            [
                'blockedIps' => $this->getTableData(),
            ]
        )->render();
    }


    /**
     * @param $blockedIpId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove($blockedIpId)
    {
        $this->blockedIpService->delete($blockedIpId);

        return redirect()
            ->route('admin.blocked-ip.list')
            ->with('success', __('common.BlockedIpDeletedSuccessfully'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAll()
    {
        $this->blockedIpService->deleteAll();

        return redirect()
            ->route('admin.blocked-ip.list')
            ->with('success', __('common.allBlockedIpsDeletedSuccessfully'));
    }
}
