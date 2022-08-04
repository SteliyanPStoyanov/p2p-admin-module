<?php

namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Http\Requests\LoginAttemptSearchRequest;
use Modules\Common\Services\LoginAttemptService;
use Modules\Core\Controllers\BaseController;

class LoginAttempController extends BaseController
{
    protected LoginAttemptService $loginAttemptService;

    public function __construct(LoginAttemptService $loginAttemptService)
    {
        $this->loginAttemptService = $loginAttemptService;

        parent::__construct();
    }

    /**
     * @param LoginAttemptSearchRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(LoginAttemptSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::login-attempt.list',
            [
                'loginAttempts' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        return $this->loginAttemptService->getByWhereConditions(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }

    /**
     * @param LoginAttemptSearchRequest $request
     */
    protected function checkForRequestParams(
        LoginAttemptSearchRequest $request
    ) {
        if ($request->exists(
            ['id', 'email', 'active', 'limit']
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param $loginAttemptId
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove($loginAttemptId)
    {
        $this->loginAttemptService->delete($loginAttemptId);

        return redirect()
            ->route('admin.login-attempt.list')
            ->with('success', __('common.loginAttemptDeletedSuccessfully'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAll()
    {
        $this->loginAttemptService->deleteAll();

        return redirect()
            ->route('admin.login-attempt.list')
            ->with('success', __('common.allLoginAttemptDeletedSuccessfully'));
    }

    /**
     * @param LoginAttemptSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refresh(LoginAttemptSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::login-attempt.list-table',
            [
                'loginAttempts' => $this->getTableData(),
            ]
        )->render();
    }
}
