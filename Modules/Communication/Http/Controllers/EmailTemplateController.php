<?php

namespace Modules\Communication\Http\Controllers;

use Exception;
use Modules\Communication\Entities\Email;
use Modules\Communication\Http\Requests\EmailTemplateEditRequest;
use Modules\Communication\Http\Requests\EmailTemplateSearchRequest;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\ProblemException;
use Modules\Communication\Services\EmailTemplateService;
use Throwable;

class EmailTemplateController extends BaseController
{
    protected EmailTemplateService $emailTemplateService;

    protected string $pageTitle = 'Email template list';
    protected string $indexRoute = 'admin.emailTemplate.list';
    protected string $editRoute = 'communication.emailTemplate.edit';

    public function __construct(
        EmailTemplateService $userAgreementService
    ) {
        $this->emailTemplateService = $userAgreementService;

        parent::__construct();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'communication::email-template.index',
            [
                'emailTemplates' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
                'getEmailTypes' => Email::getEmailTypes()
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $getEmailTypes = Email::getEmailTypes();

        return view(
            'communication::email-template.crud',
            compact( 'getEmailTypes')
        );
    }

    /**
     * @param EmailTemplateEditRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function store(EmailTemplateEditRequest $request)
    {
        try {
            $template = $this->emailTemplateService->create($request->validated());
        } catch (Throwable $e) {
            throw new ProblemException(__('communication::emailTemplateCrud.emailTemplateCreationFailed'));
        }

        return redirect()
            ->route($this->editRoute, $template->email_template_id)
            ->with('success', __('communication::emailTemplateCrud.emailTemplateCreatedSuccessfully'));
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
            $emailTemplate = $this->emailTemplateService->getTemplateById($id);
        } catch (Throwable $e) {
            throw new ProblemException(__('communication::emailTemplateCrud.emailTemplateCreationFailed'));
        }

        $getEmailTypes = Email::getEmailTypes();

        return view(
            'communication::email-template.crud',
            compact( 'emailTemplate', 'getEmailTypes')
        );
    }

    /**
     * @param $id
     * @param EmailTemplateEditRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\NotFoundException
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function update($id, EmailTemplateEditRequest $request)
    {
        $this->emailTemplateService->update($id, $request->validated());
        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('communication::emailTemplateCrud.emailTemplateUpdatedSuccessfully'));
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
        $this->emailTemplateService->delete($id);

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('communication::emailTemplateCrud.emailTemplateDeletedSuccessfully'));
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
        $this->emailTemplateService->enable($id);

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('communication::emailTemplateCrud.emailTemplateEnabledSuccessfully'));
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
        $this->emailTemplateService->disable($id);

        return redirect()
            ->route($this->indexRoute)
            ->with(
                'success',
                __('communication::emailTemplateCrud.emailTemplateDisabledSuccessfully')
            );
    }


       /**
     * @param EmailTemplateSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refresh(EmailTemplateSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);
        return view(
            'communication::email-template.list-table',
            [
                'emailTemplates' => $this->getTableData(),
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
        return $this->emailTemplateService->getByWhereConditions(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }
}
