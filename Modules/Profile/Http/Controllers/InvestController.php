<?php

namespace Modules\Profile\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Admin\Services\UserAgreementService;
use Modules\Common\Entities\ContractTemplate;
use Modules\Common\Entities\FileStorage;
use Modules\Common\Entities\FileType;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Entities\Task;
use Modules\Common\Http\Requests\InvestLoanSearchRequest;
use Modules\Common\Services\InvestmentBunchService;
use Modules\Common\Services\InvestmentService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\InvestService;
use Modules\Common\Services\LoanContractService;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\TaskService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\BaseException;
use Modules\Core\Exceptions\JsonException;
use Modules\Profile\Http\Requests\InvestmentRequest;

class InvestController extends BaseController
{
    protected LoanService $loanService;
    protected InvestService $investService;
    protected InvestmentService $investmentService;
    protected LoanContractService $loanContractService;
    protected TaskService $taskService;
    protected InvestmentBunchService $investmentBunchService;
    protected InvestorService $investorService;
    protected UserAgreementService $userAgreementService;

    public function __construct(
        LoanService $loanService,
        InvestService $investService,
        InvestmentService $investmentService,
        LoanContractService $loanContractService,
        TaskService $taskService,
        InvestmentBunchService $investmentBunchService,
        InvestorService $investorService,
        UserAgreementService $userAgreementService
    ) {
        $this->loanService = $loanService;
        $this->investService = $investService;
        $this->investmentService = $investmentService;
        $this->loanContractService = $loanContractService;
        $this->taskService = $taskService;
        $this->investmentBunchService = $investmentBunchService;
        $this->investorService = $investorService;
        $this->userAgreementService = $userAgreementService;

        parent::__construct();
    }

    public function investView()
    {
        try {
            $view = view(
                'profile::invest.invest',
                [
                    'cacheKey' => $this->cacheKey,
                    'loans' => $this->getTableData(),
//                    'loans' => MarketSecondary::all(),
                ]
            );
        } catch(\Throwable $e) {
            echo $e->getMessage()." ".$e->getFile()." ".$e->getLine(); exit();
        }

        return $view;

    }

    public function list()
    {
        try {
            // on 1st load of list page, we remove previous session

            $this->getSessionService()->add($this->cacheKey, ['market' => 'primary']);

            return view(
                'profile::invest.list',
                [
                    'investor' => $this->getInvestor(),
                    'cacheKey' => $this->cacheKey,
                    'loans' => $this->getTableData(),
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param int $loanId
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function view(int $loanId)
    {
        try {
            $loan = $this->loanService->getById($loanId);

            if (empty($loan)) {
                return redirect()->route('profile.invest');
            }

            return view(
                'profile::invest.view',
                [
                    'investor' => $this->getInvestor(),
                    'loan' => $loan,
                    'installments' => $loan->installments(),
                    'myLoanShare' => $this->investorService->myLoanShare($this->getInvestor()->investor_id, $loan),
                    'investorsShare' => $this->investorService->investorsLoanShare(
                        $this->getInvestor()->investor_id,
                        $loan
                    ),
                ]
            );
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param InvestmentRequest $request
     * @param int $loanId
     * @return array|\Illuminate\Contracts\Foundation\Application
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function invest(InvestmentRequest $request, int $loanId)
    {
        $validated = $request->validated();
        try {
            $loan = $this->loanService->getById($loanId);
            $investor = $this->getInvestor();


            if (isset($investor->buy_now) && 1 == $investor->buy_now) {
                return [
                    'success' => false,
                    'data' => [
                        'message' => __('common.Buying at the moment, wait a bit.'),
                    ]
                ];
            }


            if ($investor->wallet()->hasUninvestedAmount($validated['amount']) == false) {
                if ($request->ajax() == true) {
                    return [
                        'success' => false,
                        'data' => [
                            'message' => __('common.UninvestedAmountIsLower'),
                        ]
                    ];
                }

                return redirect()->back()->with('fail', __('common.UninvestedAmountIsLower'));
            }

            if ($loan->isAvailableAmount($validated['amount']) == false) {
                if ($request->ajax() == true) {
                    return [
                        'success' => false,
                        'data' => [
                            'message' => __('common.LoanAmountIsLower'),
                        ]
                    ];
                }

                return redirect()->back()->with('fail', __('common.LoanAmountIsLower'));
            }


            // check if have active invest bunches
            $bunch = $investor->getInvestmentBunch();
            if (!empty($bunch->investment_bunch_id) && 0 == $bunch->finished) {
                if ($request->ajax() == true) {
                    return [
                        'success' => false,
                        'data' => [
                            'message' => __('common.MultippleBuyingAtTheMoment'),
                        ]
                    ];
                }
                return redirect()->back()->with('fail', __('common.MultippleBuyingAtTheMoment'));
            }
        } catch (\Throwable $e) {
            return view('errors.generic');
        }


        $investor->buy_now = 1;
        $investor->save();

        try {
            $invested = $this->investService->invest(
                $investor->investor_id,
                $loan->loan_id,
                $validated['amount']
            );
        } catch (\Throwable $e) {
            Log::channel('loan_investing_error')->error(
                'Failed to save change log. ' . $e->getMessage()
            );
        }

        $investor->buy_now = 0;
        $investor->save();

        if ($invested) {
            // we need to remove cached profile overview since we have changed summs
            $this->getCacheService()->remove(config('profile.profileDashboard') . $investor->investor_id);

            if ($request->ajax() == true) {
                return [
                    'success' => true,
                    'data' => [
                        'message' => __('common.InvestmentSuccess', ['amount' => $validated['amount']]),
                        'wallet' => $this->getInvestor()->wallet(),
                        'loan' => $loan->refresh(),
                    ]
                ];
            }

            return redirect()->back()->with(
                'success',
                __('common.InvestmentSuccess', ['amount' => $validated['amount']])
            );
        }


        if ($request->ajax() == true) {
            return [
                'success' => false,
                'data' => [
                    'message' => __('common.InvestmentFail'),
                ]
            ];
        }

        return redirect()->back()->with('fail', __('common.InvestmentFail'));
    }

    /**
     * @param InvestmentRequest $request
     *
     * @return array|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function investAll(InvestmentRequest $request)
    {
        $validated = $request->validated();

        try {
            $amount = floatval($validated['amount']);

            $investor = $this->getInvestor();
            $wallet = $investor->wallet();

            if ($wallet->hasUninvestedAmount($amount) == false) {
                return redirect()->back()->with('fail', __('common.UninvestedAmountIsLower'));
            }

            // check if have active invest bunches
            $bunch = $investor->getInvestmentBunch();
            if (!empty($bunch->investment_bunch_id) && 0 == $bunch->finished) {
                if ($request->ajax() == true) {
                    return [
                        'success' => false,
                        'data' => [
                            'message' => __('common.MultippleBuyingAtTheMoment'),
                        ]
                    ];
                }
            }

            $possibleCountToBuy = round(($wallet->uninvested / $amount)) + 1;
            $this->investmentService->massInvestByAmountAndFilters(
                $investor->investor_id,
                $amount,
                session($this->cacheKey, []),
                $possibleCountToBuy
            );

            if ($request->ajax() == true) {
                return [
                    'success' => true,
                    'data' => [
                        'message' => __('common.InvestmentSuccess', ['amount' => $validated['amount']]),
                        'wallet' => $this->getInvestor()->wallet(),
                        'investorBunch' => $this->taskService->checkInvestorBunch(($this->getInvestor()->investor_id)),
                    ]
                ];
            }
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    public function ajaxCheckBunch(Request $request)
    {
        try {
            if ($request->ajax() == true) {
                return [
                    'success' => true,
                    'data' => [
                        'bunch' => $this->investmentBunchService->getById(intval($request->bunchId)),
                    ]
                ];
            }
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'data' => [
                    'bunch' => null,
                ]
            ];
        }
    }

    /**
     * @param InvestLoanSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refresh(InvestLoanSearchRequest $request)
    {
        try {
            parent::setFiltersFromRequest($request);

            return view(
                'profile::invest.list-table',
                [
                    'investor' => $this->getInvestor(),
                    'loans' => $this->getTableData(session($this->cacheKey . '.limit')),
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
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        if ($limit != session($this->cacheKey . '.limit')) {
            $data = $this->getSessionService()->get($this->cacheKey);
            $this->getSessionService()->remove($this->cacheKey);
            $data['limit'] = $limit;
            $this->getSessionService()->add($this->cacheKey, $data);
        }

        $investorId = Auth::guard('investor')->user()->investor_id ?? 0;

        return $this->loanService->getLoansForSite(
            $limit ?? parent::getTableLength(),
            session($this->cacheKey, []),
            $investorId
        );
    }

    public function downloadAssignmentAgreement($contractId)
    {
        try {
            $loanContract = $this->loanContractService->getById($contractId);

            if ($loanContract->investor->investor_id != Auth::guard('investor')->user()->investor_id) {
                abort(403);
            }

            $file = $loanContract->file;

            return response()->file(storage_path() . '/' . $file->file_name);
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }

    /**
     * @param $loanId
     *
     * @return mixed
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function assignmentAgreementTemplate($loanId)
    {
        try {
            $assignmentAgreement = $this->userAgreementService->getCurrentContractTemplate(ContractTemplate::TYPE_LOAN);

            $loan = $this->loanService->getById($loanId);
            $investor = $this->getInvestor();

            $filePath = $this->investService->generateAgreementNoInvestment(
                $loan,
                $investor,
                $assignmentAgreement
            );

            return response()->file(storage_path() . '/' . $filePath);
        } catch (\Throwable $e) {
            return view('errors.generic');
        }
    }


    /**
     * @return array
     */
    public function investorHasActiveBunch(): array
    {
        try {
            $investor = $this->getInvestor();

            // check if have active invest bunches
            $bunch = $investor->getInvestmentBunch();
            if (!empty($bunch->investment_bunch_id) && 0 == $bunch->finished) {
                return [
                    'success' => false,
                    'data' => [
                        'message' => __('common.MultippleBuyingAtTheMoment'),
                    ]
                ];
            }
            return [
                'success' => true,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
            ];
        }
    }

}
