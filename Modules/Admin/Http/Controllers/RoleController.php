<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Kris\LaravelFormBuilder\FormBuilder;
use Modules\Admin\Entities\Role;
use Modules\Admin\Forms\RoleForm;
use Modules\Admin\Http\Requests\RoleRequest;
use Modules\Admin\Http\Requests\RoleSearchRequest;
use Modules\Admin\Services\PermissionService;
use Modules\Admin\Services\RoleService;
use Modules\Core\Controllers\BaseController;

class RoleController extends BaseController
{
    protected string $pageTitle = 'Roles list';
    protected string $indexRoute = 'admin.roles.list';

    protected RoleService $roleService;
    protected PermissionService $permissionService;

    /**
     * RoleController constructor.
     *
     * @param RoleService $roleService
     * @param PermissionService $permissionService
     *
     * @throws \ReflectionException
     */
    public function __construct(
        RoleService $roleService,
        PermissionService $permissionService
    ) {
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;

        parent::__construct();
    }

    /**
     * @param RoleSearchRequest $request
     *
     * @return \Illuminate\View\View
     */
    public function list(RoleSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::role.index',
            [
                'roles' =>$this->getTableData(),
                'cacheKey' => $this->cacheKey,
            ]
        );

    }

    /**
     * @param RoleSearchRequest $request
     */
    protected function checkForRequestParams(RoleSearchRequest $request)
    {
        if ($request->exists(
            [
                'active',
                'createdAt',
                'name',
                'updatedAt',
            ]
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param RoleSearchRequest $request
     *
     * @return array|string
     *
     * @throws \Throwable
     */
    public function refresh(RoleSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::role.list-table',
            [
                'roles' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param RoleForm $formBuilder
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $permissionsByGroups = $this->permissionService->getAll();

        return view(
            'admin::role.create',
            compact('permissionsByGroups')
        );
    }

    /**
     * @param RoleRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function store(RoleRequest $request)
    {
        $this->roleService->create($request->validated());

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::roleCrud.roleCreatedSuccessfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @param RoleService $roleService
     *
     * @return \Illuminate\View\View
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function edit(
        int $id,
        RoleService $roleService
    ) {
        $role = $this->roleService->getRoleById($id);
        $roleService->canManageRole(\Auth::user(), $role);
        $permissionsByGroups = $this->permissionService->getAll();

        return view(
            'admin::role.create',
            compact('role', 'permissionsByGroups')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RoleRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function update(RoleRequest $request, int $id)
    {
        $this->roleService->edit($id, $request->validated(), \Auth::user());

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::roleCrud.roleUpdatedSuccessfully'));
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function enable(int $id)
    {
        $this->roleService->enable($id, \Auth::user());

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::roleCrud.roleActivatedSuccessfully'));
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function disable(int $id)
    {
        $this->roleService->disable($id, \Auth::user());

        return redirect()
            ->route($this->indexRoute)
            ->with(
                'success',
                __('admin::roleCrud.roleDeactivatedSuccessfully')
            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function delete(int $id)
    {
        $this->roleService->delete($id, \Auth::user());

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('admin::roleCrud.roleDeletedSuccessfully'));
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData()
    {
        $cachedData = $this->getCachedData(Role::class);

        if ($cachedData === null) {
            $cachedData = $this->roleService->getByFilters(
                parent::getTableLength(),
                session($this->cacheKey, [])
            );
            $this->setCacheData($cachedData);
        }

        return $cachedData;
    }
}
