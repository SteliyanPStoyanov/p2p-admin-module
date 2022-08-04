<?php

namespace Modules\Communication\Http\Controllers;

use Carbon\Carbon;
use Modules\Communication\Entities\Email;
use Modules\Communication\Http\Requests\EmailSendRequest;
use Modules\Communication\Http\Requests\EmailSearchRequest;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\ProblemException;
use Modules\Communication\Services\EmailService;

class EmailController extends BaseController
{
    protected EmailService $emailService;

    protected string $pageTitle = 'Email list';
    protected string $indexRoute = 'admin.email.list';
    protected string $editRoute = 'communication.email.edit';

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;

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
            'communication::email.index',
            [
                'emails' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
                'getEmailTypes' => Email::getEmailTypes()
            ]
        );
    }

    /**
     * Sending test email from Admin
     *
     * @param EmailSendRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\NotFoundException
     */
    public function sendEmail(EmailSendRequest $request)
    {
        $validated = $request->validated();

        $this->emailService->sendEmail(
            $validated['investor_id'],
            $validated['email_template_id'],
            $validated['email'],
            Carbon::now()->addMinute(1)
        );

        return redirect()
            ->route($this->indexRoute)
            ->with('success', __('common.emailSendSuccessfully'));
    }


    /**
     * @param EmailSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refresh(EmailSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);
        return view(
            'communication::email.list-table',
            [
                'emails' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * @param int $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        return $this->emailService->getByWhereConditions(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }
}
