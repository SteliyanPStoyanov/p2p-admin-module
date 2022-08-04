<?php

namespace Modules\Admin\Http\Controllers;

use Exception;
use Modules\Admin\Http\Requests\UserAgreementCrudRequest;
use Modules\Admin\Http\Requests\UserAgreementSearchRequest;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\ContractTemplate;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\StorageService;
use Throwable;

class UserAgreementController extends BaseController
{
    protected UserAgreementService $userAgreementService;

    protected string $indexRoute = 'admin.user-agreement.list';
    protected string $editRoute = 'admin.user-agreement.edit';

    public function __construct(
        UserAgreementService $userAgreementService
    ) {
        $this->userAgreementService = $userAgreementService;

        parent::__construct();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::user-agreement.index',
            [
                'templates' => $this->getTableData(),
                'types' => ContractTemplate::getTypes(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view(
            'admin::user-agreement.crud',
            [
                'types' => ContractTemplate::getTypes(),
            ]
        );
    }

    /**
     * @param UserAgreementCrudRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function store(UserAgreementCrudRequest $request)
    {
        try {
            $this->userAgreementService->create($request->validated());
        } catch (Throwable $e) {
            throw new ProblemException(__('common.UserAgreementCreationFailure'));
        }

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('common.UserAgreementCreationSuccessful'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\View\View
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function edit($id)
    {
        try {
            $template = $this->userAgreementService->getTemplateById($id);
            $types = ContractTemplate::getTypes();
        } catch (Throwable $e) {
            throw new ProblemException(__('common.UserAgreementEditFailure'));
        }

        return view(
            'admin::user-agreement.crud',
            compact( 'template', 'types')
        );
    }

    /**
     * @param $id
     * @param UserAgreementCrudRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function update($id, UserAgreementCrudRequest $request)
    {
        $this->userAgreementService->update($id, $request->validated());

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('common.UserAgreementEditSuccessful'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function delete($id)
    {
        $this->userAgreementService->delete($id);

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('common.UserAgreementDeleteSuccessful'));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function enable($id)
    {
        $this->userAgreementService->enable($id);

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('common.UserAgreementEnableSuccessful'));
    }

    /**
     * @param UserAgreementSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refresh(UserAgreementSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::user-agreement.list-table',
            [
                'templates' => $this->getTableData(),
                'types' => ContractTemplate::getTypes(),
            ]
        )->render();
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        return $this->userAgreementService->getByWhereConditions(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }
}
