<?php

namespace Modules\Admin\Http\Controllers;

use Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\Entities\Administrator;
use Modules\Admin\Http\Requests\AdministratorEditRequest;
use Modules\Admin\Http\Requests\AdministratorSearchRequest;
use Modules\Admin\Services\AdministratorService;
use Modules\Admin\Services\RoleService;
use Modules\Admin\Services\SettingService;
use Modules\Core\Controllers\BaseController;

/**
 * Class AdminController
 *
 * @package Modules\Admin\Http\Controllers
 */
class AdminController extends BaseController
{
    public string $pageTitle = 'Administrator list';
    public string $indexRoute = 'admin.administrators.list';

    protected AdministratorService $administratorService;
    protected RoleService $roleService;
    protected SettingService $settingService;

    /**
     * AdminController constructor.
     *
     * @param AdministratorService $administratorService
     * @param RoleService $roleService
     * @param SettingService $settingService
     *
     * @throws \ReflectionException
     */
    public function __construct(
        AdministratorService $administratorService,
        RoleService $roleService,
        SettingService $settingService
    ) {
        $this->administratorService = $administratorService;
        $this->roleService = $roleService;
        $this->settingService = $settingService;

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param AdministratorSearchRequest $request
     *
     * @return \Illuminate\View\View
     *
     * @throws \Throwable
     */
    public function list(AdministratorSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::admin.list',
            [
                'administrators' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @param AdministratorSearchRequest $request
     */
    protected function checkForRequestParams(AdministratorSearchRequest $request
    ) {
        if ($request->exists(
            ['name', 'phone', 'email', 'active', 'createdAt', 'updatedAt']
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param AdministratorSearchRequest $request
     *
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|string
     *
     * Format:
     *     #total: 50
     *     #lastPage: 5
     *     #items: Illuminate\Support\Collection
     *     #perPage: 10
     *     #currentPage: 1
     *     #path: "http://localhost:8000/admin/administrators"
     *
     * @throws \Throwable
     */
    public function refresh(AdministratorSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::admin.list-table',
            [
                'administrators' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function create()
    {
        $this->setPageTitle('Administrator creation');

        $defaultAvatarPath = $this->administratorService->getDefaultAvatarPath();
        $me = Auth::user();
        $roles = $this->roleService->getAll($me->getPriority());
        $groups = $this->roleService->getPermissionsByRole($roles);

        return view(
            'admin::admin.create',
            compact(
                'me',
                'roles',
                'defaultAvatarPath',
                'groups'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdministratorEditRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function store(AdministratorEditRequest $request)
    {
        $admin = $this->administratorService->create(
            $request->validated(),
            Auth::user()
        );

        if ($request->hasFile('avatar')) {
            $this->administratorService->addAvatar(
                $admin,
                $request->file('avatar')
            );
        }

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::adminCrud.adminCreatedSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function edit($id)
    {
        if (empty($id)) {
            return redirect()->route($this->indexRoute)
                ->with('fail', 'Wrong administrator ID');
        }

        $admin = $this->administratorService->getAdministratorById($id);
        if (empty($admin)) {
            return redirect()->route($this->indexRoute)
                ->with('fail', 'Not existing administrator');
        }

        $me = Auth::user();
        $this->administratorService->canControl($me, $admin);
        $roles = $this->roleService->getAll($me->getPriority());
        $groups = $this->roleService->getPermissionsByRole($roles);

        $admin->password = ''; // if not empty on update = changed pass

        return view(
            'admin::admin.edit',
            compact(
                'me',
                'admin',
                'roles',
                'groups'
            )
        );
    }

    /**
     * Update the specified resource.
     *
     * @param int $id
     *
     * @param AdministratorEditRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function update($id, AdministratorEditRequest $request)
    {
        $this->administratorService->update(
            array_merge($request->validated(), ['administrator_id' => $id]),
            \Auth::user()
        );

        if ($request->hasFile('avatar')) {
            $this->administratorService->changeAvatar(
                $id,
                $request->file('avatar')
            );
        }
        return redirect()
            ->route('admin.administrators.list')
            ->with('success', __('admin::adminCrud.adminUpdatedSuccessfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\ProblemException
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function delete($id)
    {
        $this->administratorService->delete($id, Auth::user());

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::adminCrud.adminDeletedSuccessfully'));
    }

    /**
     * Enable the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function enable($id)
    {
        $this->administratorService->enable($id, Auth::user());

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::adminCrud.adminEnabledSuccessfully'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function disable($id)
    {
        $this->administratorService->disable($id, Auth::user());

        return redirect()
            ->route($this->indexRoute)
            ->with(
                'success',
                __('admin::adminCrud.adminDisabledSuccessfully')
            );
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData()
    {
        $cachedData = $this->getCachedData(Administrator::class);

        if ($cachedData === null) {
            $cachedData = $this->administratorService->getByWhereConditions(
                parent::getTableLength(),
                session($this->cacheKey, [])
            );
            $this->setCacheData($cachedData);
        }

        return $cachedData;
    }
}
