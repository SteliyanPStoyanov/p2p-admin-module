<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\Http\Requests\AddFundsRequest;
use Modules\Admin\Http\Requests\BonusForInvestorRequest;
use Modules\Admin\Http\Requests\InvestorChangeLogs;
use Modules\Admin\Http\Requests\InvestorCommentRequest;
use Modules\Admin\Http\Requests\InvestorInvestmentsSearchRequest;
use Modules\Admin\Http\Requests\InvestorReferralModalRequest;
use Modules\Admin\Http\Requests\InvestorReferralsSearchRequest;
use Modules\Admin\Http\Requests\InvestorSearchRequest;
use Modules\Common\Entities\DocumentType;
use Modules\Admin\Http\Requests\InvestorTransactionsRequest;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Transaction;
use Modules\Common\Exports\InvestorInvestmentsExport;
use Modules\Common\Exports\WalletExport;
use Modules\Common\Observers\InvestStrategyObserver;
use Modules\Common\Services\InvestmentService;
use Modules\Common\Services\InvestorContractService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\InvestStrategyService;
use Modules\Common\Services\LoanService;
use Modules\Core\Controllers\BaseController;
use Modules\Admin\Http\Requests\InvestorDocumentUploadRequest;
use Modules\Core\Exceptions\NotFoundException;
use ReflectionException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Class InvestorController
 *
 * @package Modules\Admin\Http\Controllers
 */
class InvestorController extends BaseController
{
    protected InvestorService $investorService;
    protected InvestorContractService $investorContractService;
    protected InvestmentService $investmentService;
    protected LoanService $loanService;
    protected InvestStrategyService $investStrategyService;
    protected InvestStrategyObserver $investStrategyObserver;

    protected string $transactionKey = 'filters.investor.transaction';
    protected string $investmentKey = 'filters.investor.investments';
    protected string $changeLogKey = 'filters.investor.changeLog';

    /**
     * InvestorController constructor.
     * @param InvestorService $investorService
     * @param InvestorContractService $investorContractService
     * @param InvestmentService $investmentService
     * @param LoanService $loanService
     * @param InvestStrategyService $investStrategyService
     * @param InvestStrategyObserver $investStrategyObserver
     * @throws ReflectionException
     */
    public function __construct(
        InvestorService $investorService,
        InvestorContractService $investorContractService,
        InvestmentService $investmentService,
        LoanService $loanService,
        InvestStrategyService $investStrategyService,
        InvestStrategyObserver $investStrategyObserver
    )
    {
        $this->investorService = $investorService;
        $this->investorContractService = $investorContractService;
        $this->investmentService = $investmentService;
        $this->loanService = $loanService;
        $this->investStrategyService = $investStrategyService;
        $this->investStrategyObserver = $investStrategyObserver;
        parent::__construct();
    }

    /**
     * @param InvestorSearchRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(InvestorSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::investor.list',
            [
                'investors' => $this->getTableData(),
                'statuses' => $this->investorService->getStatuses(),
                'types' => Investor::getTypes(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @param InvestorSearchRequest $request
     */
    protected function checkForRequestParams(InvestorSearchRequest $request
    )
    {
        if ($request->exists(
            ['name', 'phone', 'email', 'active', 'createdAt', 'type']
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param InvestorSearchRequest $request
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
    public function refresh(InvestorSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::investor.list-table',
            [
                'investors' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * @param int $investorId
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function overview(int $investorId)
    {
        $investor = $this->investorService->getById($investorId);
        $documentTypes = DocumentType::all();

        $this->getSessionService()->add($this->cacheKey, []);
        $this->getSessionService()->add($this->investmentKey, []);

        return view(
            'admin::investor.profile',
            [
                'investor' => $investor,
                'documentTypes' => $documentTypes,
                'investorTransactions' => $this->getTableDataInvestorTransactions($investorId),
                'investorChangeLogs' => $this->getTableDataInvestorChangeLogs($investorId),
                'transactionTypes' => Transaction::getTypes(),
                'cacheKey' => $this->cacheKey,
                'investments' => $this->getTableDataInvestorInvestments($investorId),
                'loanOriginators' => $this->loanService->getLoansOriginators(),
                'loanTypes' => Loan::getTypesWithLabels(),
                'loanStatuses' => Loan::getMainStatuses(),
                'loanPaymentStatuses' => Loan::getPaymentStatuses(),
            ]
        );
    }

    /**
     * @param int $id
     * @param InvestorTransactionsRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refreshInvestorWalletTransactions(int $id, InvestorTransactionsRequest $request)
    {
        parent::setFiltersFromRequest($request, $this->transactionKey);
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::investor.components.wallet-list-table',
            [
                'investorTransactions' => $this->getTableDataInvestorTransactions($id),
            ]
        )->render();
    }

    /**
     * @param int $investorId
     * @param InvestorChangeLogs $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function refreshTableDataInvestorChangeLogs(int $investorId, InvestorChangeLogs $request)
    {
        parent::setFiltersFromRequest($request, $this->changeLogKey);
        return view(
            'admin::investor.components.change-log-list-table',
            [
                'investorChangeLogs' => $this->getTableDataInvestorChangeLogs($investorId),

            ]
        )->render();
    }

    /**
     * @param int $investorId
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getTableDataInvestorChangeLogs(int $investorId)
    {
        $this->cacheKey = $this->changeLogKey;

        return $this->investorService->getInvestorChangeLogs(
            parent::getTableLength(),
            session($this->cacheKey, []),
            $investorId
        );
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getTableDataInvestorTransactions(int $id)
    {
        $this->cacheKey = $this->transactionKey;

        return $this->investorService->getInvestorTransactions(
            parent::getTableLength(),
            $id,
            session($this->cacheKey, [])
        );
    }

    /**
     * @param int $investorId
     * @param AddFundsRequest $request
     *
     * @return string
     *
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function addFunds(int $investorId, AddFundsRequest $request)
    {
        $investorFunds = $this->investorService->prepareDataAndAddFunds($investorId, $request->validated());

        // we need to remove cached profile overview since we have changed summs
        $this->getCacheService()->remove(config('profile.profileDashboard') . $investorId);

        if (!$investorFunds) {
            return redirect()
                ->to(route('admin.investors.overview', $investorId) . '#wallet')
                ->withErrors($investorFunds)->withInput()
                ->with('fail', __('common.FailedAddedFunds'));
        }

        return redirect()
            ->to(route('admin.investors.overview', $investorId) . '#wallet')
            ->with('success', __('common.SuccessfulAddedFunds'));
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData(int $limit = null)
    {
        $cachedData = $this->getCachedData(Investor::class);
        // $cachedData = null;
        if ($cachedData === null) {
            $cachedData = $this->investorService->getByWhereConditions(
                $limit ?? parent::getTableLength(),
                session($this->cacheKey, [])
            );
            $this->setCacheData($cachedData);
        }

        return $cachedData;
    }

    /**
     * @param InvestorSearchRequest $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showReferrals(InvestorReferralsSearchRequest $request)
    {
        $this->checkForRequestParamsInvestorReferral($request);

        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::investor.referrals',
            [
                'investorReferrals' => $this->getTableDataInvestorReferrals(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @param InvestorReferralsSearchRequest $request
     *
     * @return array|string
     * @throws \Throwable
     */
    public function showReferralsRefresh(InvestorReferralsSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::investor.components.referrals-list',
            [
                'investorReferrals' => $this->getTableDataInvestorReferrals(),
            ]
        )->render();
    }

    /**
     * @param InvestorReferralsSearchRequest $request
     */
    protected function checkForRequestParamsInvestorReferral(InvestorReferralsSearchRequest $request)
    {
        if ($request->exists(
            [
                'investorNames',
                'investorEmail',
                'investorReferralSum',
                'investorReferralInvested',
                'investorReferralFrom',
                'investorReferralTo'
            ]
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param int|null $limit
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTableDataInvestorReferrals(int $limit = null)
    {
        return $this->investorService->getByWhereConditionsReferrals(
            parent::getTableLength(),
            session($this->cacheKey, [])
        );
    }

    /**
     * @param BonusForInvestorRequest $request
     *
     * @return RedirectResponse
     */
    public function referralBonus(BonusForInvestorRequest $request): RedirectResponse
    {
        $investors = $this->investorService->getByWhereConditionsReferrals(
            0,
            session($this->cacheKey, [])
        );

        $this->investorService->createTaskWithReferralBonus(
            $investors,
            floatval($request->validated()['bonusAmount'])
        );

        return redirect()->back()->with('success', __('common.SuccessfulAddedBonus'));
    }

    /**
     * @param InvestorReferralModalRequest $request
     * @return array|string
     * @throws Throwable
     */
    public function showReferral(InvestorReferralModalRequest $request)
    {

        $validated = $request->validated();
        $investor = $this->investorService->getById($validated['investor_id']);
        $referrals =$this->investorService->investorReferrals($validated['investor_id']);

         return view(
            'admin::investor.components.modal.investor-referral-list',
            [
                'investor' => $investor,
                'referrals' => $referrals
            ]
        )->render();
    }

    /**
     * @param int $investorId
     * @param InvestorDocumentUploadRequest $request
     *
     * @return RedirectResponse
     */
    public function addDocument(int $investorId, InvestorDocumentUploadRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $saveIdCard = $this->investorService->savePersonalDoc(
            $investorId,
            $validated['document_file'],
            $validated['document_type_id'],
        );

        if (!$saveIdCard) {
            return redirect()->back()->with('fail', __('common.DocumentSaveFail'));
        }

        return redirect()
            ->to(route('admin.investors.overview', $investorId) . '#documents')
            ->with('success', __('common.SuccessfulAddedBonus'));
    }


    /**
     * @param InvestorCommentRequest $request
     *
     * @return RedirectResponse
     */
    public function comment(InvestorCommentRequest $request): RedirectResponse
    {
        $this->investorService->investorComment($request->validated());

        return redirect()
            ->to(route('admin.investors.overview', $request['investor_id']))
            ->with('success', __('common.SuccessSaveComment'));
    }

    /**
     * @param $contractId
     * @return BinaryFileResponse
     * @throws NotFoundException
     */
    public function agreementDownload($contractId): BinaryFileResponse
    {
        $investorContract = $this->investorContractService->getById($contractId);

        $file = $investorContract->file;

        return response()->file(storage_path() . '/' . $file->file_name);
    }

    /**
     * @param int $investorId
     * @param InvestorTransactionsRequest $request
     *
     * @return StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportWallet(int $investorId, InvestorTransactionsRequest $request): StreamedResponse
    {
        $transactions = $this->investorService->getInvestorTransactions(
            0,
            $investorId,
            $request->validated(),
            false
        );

        $walletExport = new WalletExport($transactions);
        $fileName = 'investor-' . $investorId . '-wallet-' . date('Y-m-d-H-i-s');

        return $this->getStorageService()->download(
            $fileName,
            ['collectionClass' => $walletExport],
            'xlsx',
        );
    }

    /**
     * @param int $investorId
     * @return LengthAwarePaginator
     */
    public function getTableDataInvestorInvestments(int $investorId): LengthAwarePaginator
    {
        $this->cacheKey = $this->investmentKey;

        return $this->investmentService->getByInvestorWhereConditions(
            parent::getTableLength(),
            session($this->cacheKey, []),
            $investorId
        );
    }

    /**
     * @param int $id
     * @param InvestorInvestmentsSearchRequest $request
     *
     * @return array|string
     * @throws Throwable
     */
    public function refreshInvestorInvestments(int $id, InvestorInvestmentsSearchRequest $request)
    {
        parent::setFiltersFromRequest($request, $this->investmentKey);
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::investor.components.investment-list-table',
            [
                'investments' => $this->getTableDataInvestorInvestments($id),
                'investor' => $this->investorService->getById($id),
            ]
        )->render();
    }

    /**
     * @param $investorId
     * @return StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function investorInvestmentExport($investorId): StreamedResponse
    {
        $this->cacheKey = $this->investmentKey;

        $investment = $this->investmentService->getByInvestorWhereConditions(
            null,
            session($this->cacheKey, []),
            $investorId
        );

        $strategyExport = new InvestorInvestmentsExport($investment);
        $fileName = 'investor-investment-export-' . date('Y-m-d-H-i-s');

        return $this->getStorageService()->download(
            $fileName,
            ['collectionClass' => $strategyExport],
            'xlsx',
        );
    }
}
