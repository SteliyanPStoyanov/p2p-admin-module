<?php

namespace Modules\Profile\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\InvestStrategyService;
use Modules\Common\Services\LoanService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\BaseException;
use Modules\Core\Exceptions\JsonException;
use Modules\Core\Exceptions\ProblemException;
use Modules\Profile\Http\Requests\AutoInvestLoanCountRequest;
use Modules\Profile\Http\Requests\AutoInvestOrderRequest;
use Modules\Profile\Http\Requests\AutoInvestRequest;
use Modules\Profile\Http\Requests\AutoInvestSearchRequest;
use Throwable;

class AutoInvestController extends BaseController
{
    private InvestStrategyService $investStrategyService;
    private LoanService $loanService;
    protected string $indexRoute = 'profile.autoInvest';

    public function __construct(
        InvestStrategyService $investStrategyService,
        LoanService $loanService
    ) {
        $this->investStrategyService = $investStrategyService;
        $this->loanService = $loanService;

        parent::__construct();
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        try {
            $investor = $this->getInvestor();

            return view(
                'profile::auto-invest.index',
                [
                    'investor' => $investor,
                    'investStrategies' => $this->getTableData(session($this->cacheKey . '.limit')),
                    'cacheKey' => $this->cacheKey,
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @return Application|Factory
     * @return View
     */
    public function create()
    {
        try {
            return view(
                'profile::auto-invest.crud',
                [
                    'types' => Loan::getTypes(),
                    'paymentStatuses' => Loan::getPaymentStatuses(),
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param AutoInvestRequest $request
     * @return RedirectResponse
     * @throws ProblemException
     */
    public function store(AutoInvestRequest $request)
    {
        try {
            $investStrategy = $this->investStrategyService->create(
                $request->validated(),
                $this->getInvestor()
            );

            return redirect()
                ->route($this->indexRoute, $investStrategy->invest_strategy_id)
                ->with('success', __('common.investStrategyCreatedSuccessfully'));
        } catch (Throwable $e) {
            throw new ProblemException(__('common.investStrategyCreationFailed'));
        }
    }

    /**
     * @param $id
     * @return Application|Factory
     * @return View
     * @throws ProblemException
     */
    public function edit($id)
    {
        try {
            $investorId = $this->getInvestor()->investor_id;
            $investStrategy = $this->investStrategyService->getById($id, $investorId);

            return view(
                'profile::auto-invest.crud',
                [
                    'types' => Loan::getTypes(),
                    'paymentStatuses' => Loan::getPaymentStatuses(),
                    'investStrategy' => $investStrategy,
                ]
            );
        } catch (Throwable $e) {
            throw new ProblemException(__('common.investStrategyEditFailed'));
        }
    }

    /**
     * @param AutoInvestRequest $request
     * @param $id
     * @return RedirectResponse
     * @throws ProblemException
     */
    public function update(AutoInvestRequest $request, $id)
    {
        try {
            $investor = $this->getInvestor();
            $investStrategy = $this->investStrategyService->getById(
                $id,
                $investor->investor_id
            );

            if ($investStrategy->hasActiveBunches()) {
                return redirect()
                    ->back()
                    ->with('fail', __('common.investStrategyBuyingAtTheMoment'));
            }

            $this->investStrategyService->update(
                $id,
                ($request->validated() + ['active' => 1]),
                ['activating' => 1]
            );

            return redirect()
                ->route($this->indexRoute)
                ->with('success', __('common.investStrategyUpdatedSuccessfully'));
        } catch (\Throwable $e) {
            throw new ProblemException(__('common.investStrategyEditFailed'));
        }
    }

    /**
     * @param $id
     * @return array|string
     * @throws JsonException
     */
    public function delete($id)
    {
        try {
            $investor = $this->getInvestor();
            $this->investStrategyService->delete($id, $investor->investor_id);

            return view(
                'profile::auto-invest.list-table',
                [
                    'investor' => $investor,
                    'investStrategies' => $this->getTableData(session($this->cacheKey . '.limit')),
                    'cacheKey' => $this->cacheKey,
                ]
            )->render();
        } catch (\Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param $id
     * @return array|string
     * @throws JsonException
     */
    public function enable($id)
    {
        try {
            $investor = $this->getInvestor();
            $this->investStrategyService->enable($id, $investor->investor_id);

            return view(
                'profile::auto-invest.list-table',
                [
                    'investor' => $investor,
                    'investStrategies' => $this->getTableData(session($this->cacheKey . '.limit')),
                    'cacheKey' => $this->cacheKey
                ]
            )->render();
        } catch (\Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param $id
     * @return array|string
     * @throws JsonException
     */
    public function disable($id)
    {
        try {
            $investor = $this->getInvestor();
            $this->investStrategyService->disable($id, $investor->investor_id);

            return view(
                'profile::auto-invest.list-table',
                [
                    'investor' => $investor,
                    'investStrategies' => $this->getTableData(session($this->cacheKey . '.limit')),
                    'cacheKey' => $this->cacheKey,
                ]
            )->render();
        } catch (\Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param AutoInvestSearchRequest $request
     *
     * @return array|LengthAwarePaginator|string
     *
     * Format:
     *     #total: 50
     *     #lastPage: 5
     *     #items: Illuminate\Support\Collection
     *     #perPage: 10
     *     #currentPage: 1
     *     #path: "http://localhost:7000/profile/invest"
     *
     * @throws Throwable
     */
    public function refresh(AutoInvestSearchRequest $request)
    {
        try {
            parent::setFiltersFromRequest($request);

            return view(
                'profile::auto-invest.list-table',
                [
                    'investor' => $this->getInvestor(),
                    'investStrategies' => $this->getTableData(session($this->cacheKey . '.limit')),
                    'cacheKey' => $this->cacheKey,
                ]
            )->render();
        } catch (Throwable $e) {
            throw new JsonException(__('common.SomethingWentWrong'), 400);
        }
    }

    /**
     * @param int|null $limit
     *
     * @return LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        $investorId = $this->getInvestor()->investor_id;
        return $this->investStrategyService->getInvestorStrategies(
            [session($this->cacheKey, []), 'investor_id' => $investorId],
            null
        );
    }

    /**
     * @param AutoInvestOrderRequest $request
     *
     * @return JsonResponse
     */
    public function priorityChange(AutoInvestOrderRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $investorId = $this->getInvestor()->investor_id;
            $currentStrategy = $this->investStrategyService->getById(
                $validated['strategyId'],
                $investorId
            );

            if ($validated['direction'] == 'down') {
                $nextStrategy = $currentStrategy->getNext();

                if (
                    $nextStrategy == null
                    || empty($nextStrategy->invest_strategy_id)
                ) {
                    return response()->json(
                        [
                            "success" => false,
                            "message" => 'Something was wrong!',
                        ]
                    );
                }

                $changed = $this->investStrategyService->changePlaces(
                    $nextStrategy,
                    $currentStrategy
                );
                if (!$changed) {
                    throw new Exception("Failed to change places");
                }
            }

            if ($validated['direction'] == 'up') {
                $prevStrategy = $currentStrategy->getPrev();

                if (
                    $prevStrategy == null
                    || empty($prevStrategy->invest_strategy_id)
                ) {
                    return response()->json(
                        [
                            "success" => false,
                            "message" => 'Something was wrong!',
                        ]
                    );
                }

                $changed = $this->investStrategyService->changePlaces(
                    $currentStrategy,
                    $prevStrategy
                );
                if (!$changed) {
                    throw new Exception("Failed to change places");
                }
            }

            return response()->json(
                [
                    "success" => true,
                    "message" => 'You\'ve changed the priority of this strategy.',
                ]
            );
        } catch (Throwable $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => 'Sorry, something failed. Please try again later.',
                ]
            );
        }
    }

    /**
     * @param AutoInvestLoanCountRequest $request
     *
     * @return JsonResponse
     */
    public function loanCount(AutoInvestLoanCountRequest $request): JsonResponse
    {
        try {
            $investor = $this->getInvestor();
            $count = $this->loanService->loansCountStrategy(
                $request->validated(),
                $investor->investor_id
            );

            return response()->json(["count" => $count]);
        } catch (Throwable $e) {
            return response()->json(['count' => 0]);
        }
    }
}
