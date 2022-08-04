<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\Entities\Setting;
use Modules\Admin\Http\Requests\SettingEditRequest;
use Modules\Admin\Http\Requests\SettingSearchRequest;
use Modules\Admin\Services\SettingService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use Throwable;

class SettingController extends BaseController
{
    protected string $pageTitle = 'Setting list';
    protected string $indexRoute = 'admin.settings.list';
    protected SettingService $settingService;

    /**
     * SettingController constructor.
     *
     * @param SettingService $settingService
     *
     * @throws \ReflectionException
     */
    public function __construct(
        SettingService $settingService
    ) {
        $this->settingService = $settingService;
        parent::__construct();
    }

    /**
     * @param SettingSearchRequest $request
     *
     * @return \Illuminate\View\View
     */
    public function list(SettingSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::settings.list',
            [
                'settings' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @param SettingSearchRequest $request
     */
    protected function checkForRequestParams(SettingSearchRequest $request)
    {
        if ($request->exists(
            [
                'name',
                'description',
                'default_value',
                'active',
                'createdAt',
                'updatedAt',
            ]
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param SettingSearchRequest $request
     *
     * @return array|string
     *
     * @throws Throwable
     */
    public function refresh(SettingSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::settings.list-table',
            [
                'settings' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view(
            'admin::settings.create'
        );
    }

    /**
     * @param SettingEditRequest $request
     *
     * @return RedirectResponse
     *
     * @throws ProblemException
     */
    public function store(SettingEditRequest $request)
    {
        $this->settingService->create($request->validated());

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::settingCrud.CreatedSuccessfully'));
    }

    /**
     * @param string $settingKey
     *
     * @return \Illuminate\View\View
     *
     * @throws NotFoundException
     */
    public function edit(
        string $settingKey
    ) {
        $setting = $this->settingService->getSettingById($settingKey);

        return view(
            'admin::settings.create',
            compact('setting')
        );
    }

    /**
     * @param string $settingKey
     * @param SettingEditRequest $request
     *
     * @return RedirectResponse
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function update(
        string $settingKey,
        SettingEditRequest $request
    ) {
        $this->settingService->edit($settingKey, $request->validated());

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::settingCrud.UpdatedSuccessfully'));
    }

    /**
     * @param string $settingKey
     *
     * @return RedirectResponse
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function enable(string $settingKey)
    {
        $this->settingService->enable($settingKey);

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::settingCrud.ActivatedSuccessfully'));
    }

    /**
     * @param string $settingKey
     *
     * @return RedirectResponse
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function disable(string $settingKey)
    {
        $this->settingService->disable($settingKey);

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::settingCrud.DeactivatedSuccessfully'));
    }

    /**
     * @param string $settingKey
     *
     * @return RedirectResponse
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function delete(string $settingKey)
    {
        $this->settingService->delete($settingKey);

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::settingCrud.DeletedSuccessfully'));
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData()
    {
        return $this->settingService->getByFilters(
            parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }
}
