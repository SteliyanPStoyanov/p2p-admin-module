<?php

namespace Modules\Admin\Http\Controllers;

use Modules\Admin\Http\Requests\RegistrationAttemptSearchRequest;
use Modules\Common\Services\RegistrationAttemptService;
use Modules\Core\Controllers\BaseController;

class RegistrationAttemptController extends BaseController
{

    protected RegistrationAttemptService $registrationAttemptService;

    public function __construct(RegistrationAttemptService $registrationAttemptService)
    {
        $this->registrationAttemptService = $registrationAttemptService;
        parent::__construct();
    }

    public function list(RegistrationAttemptSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::registration-attempt.list',
            [
                'registrationAttempts' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    public function remove($registrationAttemptId)
    {
        $this->registrationAttemptService->delete($registrationAttemptId);

        return redirect()
            ->route('admin.registration-attempt.list')
            ->with('success', __('common.registrationAttemptDeletedSuccessfully'));
    }

    public function refresh(RegistrationAttemptSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::registration-attempt.list-table',
            [
                'registrationAttempts' => $this->getTableData(),
            ]
        )->render();
    }

    public function deleteAll()
    {
        $this->registrationAttemptService->deleteAll();

        return redirect()
            ->route('admin.registration-attempt.list')
            ->with('success', __('common.allRegistrationAttemptDeletedSuccessfully'));
    }

    protected function checkForRequestParams(
        RegistrationAttemptSearchRequest $request
    ) {
        if ($request->exists(
            ['id', 'email', 'active', 'limit']
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    protected function getTableData(int $limit = null)
    {
        return $this->registrationAttemptService->getByWhereConditions(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }
}
