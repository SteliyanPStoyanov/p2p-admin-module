<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\Http\Requests\ReBuyingLoanSearchRequest;
use Modules\Admin\Http\Requests\UploadDocumentRequest;
use Modules\Common\Entities\FileType;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\CountryService;
use Modules\Common\Services\LoanImportService;
use Modules\Common\Services\LoanService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Services\StorageService;

class ReBuyingLoanController extends BaseController
{
    public string $indexUploadLoanFiles = 'admin.re-buying-loans.upload.list';
    protected LoanImportService $loanImportService;
    protected CountryService $countryService;
    protected LoanService $loanService;

    public function __construct(
        LoanImportService $loanImportService,
        CountryService $countryService,
        StorageService $storageService,
        LoanService $loanService
    ) {
        $this->loanImportService = $loanImportService;
        $this->countryService = $countryService;
        $this->storageService = $storageService;
        $this->loanService = $loanService;

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
            'admin::re-buying-loans.list',
            [
                'cacheKey' => $this->cacheKey,
                'loans' => $this->getTableData(),
                'countries' => $this->countryService->getAll(),
                'loanTypes' => Loan::getTypesWithLabels(),
                'loanStatuses' => Loan::getFinalStatuses(),
                'loanPaymentStatuses' => Loan::getPaymentStatuses(),
            ]
        );
    }

    /**
     * @param ReBuyingLoanSearchRequest $request
     *
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|string
     *
     * Format:
     *     #total: 50
     *     #lastPage: 5
     *     #items: Illuminate\Support\Collection
     *     #perPage: 10
     *     #currentPage: 1
     *     #path: "http://localhost:7000/profile/invest"
     *
     * @throws \Throwable
     */
    public function refresh(ReBuyingLoanSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::re-buying-loans.list-table',
            [
                'loans' => $this->getTableData(),
            ]
        )->render();
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator
     */
    protected function getTableData()
    {
        $cachedData = $this->getCachedData(Loan::class);

        if ($cachedData === null) {
            $cachedData = $this->loanService->getLoansForAdmin(
                parent::getTableLength(),
                session($this->cacheKey, []),
                true
            );
            $this->setCacheData($cachedData);
        }

        return $cachedData;
    }

    /**
     * @return \Illuminate\View\View
     *
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function getFiles()
    {
        $files = $this->storageService->getImportUnlistedLoanFiles();

        $allFiles = $this->loanImportService->getFilesByNames($files);

        return view('admin::loans.upload-re-buying-loans', compact('allFiles'));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function storeFile(UploadDocumentRequest $request)
    {
        $file = $request->import_file;

        if (!$this->loanImportService->isValidRebuyingLoanFile($file)) {
            return redirect()
                ->route($this->indexUploadLoanFiles)
                ->with('fail', __('common.RebuyingLoansFileNotValid'));
        }

        $successfullImport = $this->loanImportService->importFile(
            $file,
            FileType::UNLISTED_LOANS_ID,
            StorageService::UNLISTED_LOANS_DIR,
            StorageService::IMPORTED_UNLISTED_LOANS_NAME
        );

        if (!$successfullImport) {
            return redirect()
                ->route($this->indexUploadLoanFiles)
                ->with(
                    'fail',
                    __('common.UnsuccessfullyImportedLoanDocument')
                );
        }

        return redirect()
            ->route($this->indexUploadLoanFiles)
            ->with(
                'successfullyImported',
                __('common.SuccessfullyImportedLoanDocument')
            );
    }

    /**
     * @param int $loanDocId
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function deleteLoanDocument(int $loanDocId)
    {
        if (empty($loanDocId)) {
            return redirect()
                ->route($this->indexUploadLoanFiles)
                ->with(
                    'fail',
                    __('common.DocumentNotFound')
                );
        }

        $this->loanImportService->deleteDocumentWithNewLoans(
            $loanDocId,
            StorageService::IMPORTED_UNLISTED_LOANS_DIR
        );

        return redirect()
            ->route($this->indexUploadLoanFiles)
            ->with(
                'successRemoveDocument',
                __('common.SuccessDeleteDocument')
            );
    }

    /**
     * @param int $fileId
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     * @throws \Modules\Core\Exceptions\ProblemException
     */
    public function downloadLoanDocument(int $fileId)
    {
        if (empty($fileId)) {
            return redirect()
                ->route($this->indexUploadLoanFiles)
                ->with(
                    'fail',
                    __('common.DocumentNotFound')
                );
        }

        return $this->loanImportService->downloadFile($fileId);
    }
}
