<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Modules\Admin\Http\Requests\TaskModalRequest;
use Modules\Admin\Http\Requests\TaskSearchRequest;
use Modules\Admin\Http\Requests\VerifyRequest;
use Modules\Common\Entities\Task;
use Modules\Common\Services\TaskService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\ProblemException;
use ReflectionException;
use Throwable;

class TaskController extends BaseController
{
    protected TaskService $taskService;

    /**
     * TaskController constructor.
     * @param TaskService $taskService
     * @throws ReflectionException
     */
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;

        parent::__construct();
    }

    /**
     * @param TaskSearchRequest $request
     *
     * @return Application|Factory|View
     */
    public function list(TaskSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::task.list',
            [
                'tasks' => $this->getTableData(),
                'types' => $this->taskService->getTaskTypes(),
                'statuses' => $this->taskService->getStatuses(),
                'cacheKey' => $this->cacheKey
            ]
        );
    }

    /**
     * @param TaskModalRequest $request
     * @return array|JsonResponse|string
     * @throws ProblemException
     * @throws Throwable
     */
    public function updateProcessBy(TaskModalRequest $request)
    {
        $validated = $request->validated();
        $administratorId = \Auth::user()->administrator_id;

        $task = $this->taskService->getTaskById($validated['task_id']);

        if ($task->processing_by !== null && $task->processing_by != $administratorId) {
            throw new HttpResponseException(
                response()->json(['task_id' => ['Processing by ' . $task->processingBy->twoNames]], 403)
            );
        }

        $this->taskService->updateProcessBy($validated['task_id']);

        if ($task->task_type === Task::TASK_TYPE_VERIFICATION) {
            return response()->json(
                ['url' => route('admin.investors.overview', $task->investor_id) . '#verification'],
                200
            );
        }

        return view(
            'admin::task.' . Task::getTaskModalByType($validated['task_type']),
            [
                'task' => $task,
            ]
        )->render();
    }

    /**
     * @param int $id
     * @param Request $request
     * @return RedirectResponse
     * @throws ProblemException
     * @throws Throwable
     */
    public function withdraw(int $id, Request $request)
    {
        $this->getSessionService()->add($this->cacheKey, []);

        $validated = $request->validate(
            [
                'bank_transaction_id' => 'nullable',
            ]
        );

        $task = $this->taskService->getTaskById($id);
        $investorBunch = $this->taskService->checkInvestorBunch($task->investor_id);

        if (!empty($investorBunch)) {
            return redirect()->back()->with('fail', __('common.InvestingAtTheMoment'));
        }

        $taskWithDraw = $this->taskService->withdraw($id, $validated);

        if (!$taskWithDraw) {
            return redirect()->back()->with('fail', __('common.WithdrawFail'));
        }

        return back()->with('success', 'Success!');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return RedirectResponse
     * @throws ProblemException
     * @throws Throwable
     */
    public function addBonus(int $id, Request $request)
    {
        $validated = $request->validate(
            [
                'bank_transaction_id' => 'nullable',
            ]
        );

        $taskBonus = $this->taskService->addBonus($id, $validated);

        if (!$taskBonus) {
            return redirect()->back()->with('fail', __('common.TaskBonusFail'));
        }

        // we need to remove cached profile overview since we have changed summs
        $this->getCacheService()->remove(config('profile.profileDashboard') . $id);

        return back()->with('success', 'Success!');
    }

    /**
     * @param int $id
     * @param VerifyRequest $request
     * @return RedirectResponse
     * @throws ProblemException
     */
    public function verify(
        int $id,
        VerifyRequest $request
    ) {
        $verifyTask = $this->taskService->verify($id, $request->validated());

        if (!$verifyTask) {
            return redirect()->back()->with('fail', __('common.VerifyTaskFail'));
        }

        return back()->with('success', 'Success!');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws ProblemException
     */
    public function exitTask(int $id)
    {
        $task = $this->taskService->getTaskById($id);
        if ($task->processing_by === null) {
            throw new ProblemException('Task is not processing.');
        }

        $this->taskService->exitTask($task);

        if ($task->task_type === Task::TASK_TYPE_VERIFICATION) {
            return redirect()->to(route('admin.tasks.list'));
        }

        return redirect()->back();
    }

    /**
     * @param TaskSearchRequest $request
     */
    protected function checkForRequestParams(
        TaskSearchRequest $request
    ) {
        if ($request->exists(
            ['task_id', 'status', 'task_type', 'name', 'amount', 'createdAt', 'updatedAt']
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param TaskSearchRequest $request
     * @return array|string
     * @throws Throwable
     */
    public function refresh(
        TaskSearchRequest $request
    ) {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::task.list-table',
            [
                'tasks' => $this->getTableData(),
                'investorBunches' => $this->taskService->getInvestmentBunchesWithActiveWithdrawRequests()
            ]
        )->render();
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        return $this->taskService->getByWhereConditions(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws ProblemException
     * @throws Throwable
     */
    public function cancelTask(int $id): RedirectResponse
    {
        $task = $this->taskService->getTaskById($id);
        if ($task->processing_by === null) {
            throw new ProblemException('Task is not processing.');
        }

        $this->taskService->cancelTask($task);

        return redirect()->back();
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws ProblemException
     */
    public function delete(int $id): RedirectResponse
    {
        $this->taskService->delete($id, \Auth::user());

        return redirect()
            ->route('admin.tasks.list')
            ->with('success', __('common.taskDeletedSuccessfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function matchDeposit(int $id, Request $request): RedirectResponse
    {
        $request->validate(
            [
                'investor_id' => 'required|numeric|exists:investor,investor_id',
            ]
        );

        $matchDeposit = $this->taskService->matchDeposit($id, (int)$request->input('investor_id'));

        if ($matchDeposit === false) {
            return back()->with('fail', __('common.MatchDepositFailure'));
        }

        return back()->with('success', 'Success!');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws ProblemException|Throwable
     */
    public function firstDeposit(int $id): RedirectResponse
    {
        $this->taskService->firstDepositOrNotVerified($id);

        return back()->with('success', 'Success!');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws ProblemException
     */
    public function rejectedVerification(int $id): RedirectResponse
    {
        $this->taskService->markDone($id);

        return back()->with('success', 'Success!');
    }

    /**
     * @param int $id
     * @return RedirectResponse
     * @throws ProblemException|Throwable
     */
    public function notVerified(int $id): RedirectResponse
    {
        $this->taskService->firstDepositOrNotVerified($id);

        return back()->with('success', 'Success!');
    }
}
