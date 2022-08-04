<?php

namespace Modules\Admin\Http\Controllers;

use App;
use Artisan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\Http\Requests\LoanSearchRequest;
use Modules\Admin\Http\Requests\UploadDocumentRequest;
use Modules\Common\Console\DailyPaymentStatusRefresh;
use Modules\Common\Console\ImportInstallments;
use Modules\Common\Console\ImportLoans;
use Modules\Common\Entities\FileType;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\CountryService;
use Modules\Common\Services\FileService;
use Modules\Common\Services\InvestorService;
use Modules\Common\Services\LoanImportService;
use Modules\Common\Services\LoanService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\JsonException;
use Modules\Core\Services\StorageService;
use Throwable;

class NewLoansController extends BaseController
{
    protected const COMMANDS_IMPORT_LOANS = [
        ImportLoans::class,
        ImportInstallments::class,
        DailyPaymentStatusRefresh::class,
    ];

    public string $indexUploadLoanFiles = 'admin.loans.upload.list';
    protected LoanService $loanService;
    protected InvestorService $investorService;
    protected CountryService $countryService;
    protected LoanImportService $loanImportService;
    protected FileService $fileService;

    public function __construct(
        LoanService $loanService,
        CountryService $countryService,
        StorageService $storageService,
        LoanImportService $loanImportService,
        FileService $fileService,
        InvestorService $investorService
    ) {
        $this->loanService = $loanService;
        $this->countryService = $countryService;
        $this->storageService = $storageService;
        $this->loanImportService = $loanImportService;
        $this->fileService = $fileService;
        $this->investorService = $investorService;

        parent::__construct();
    }

    /**
     * @param int $loanId
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function overview(int $loanId)
    {
        $loan = $this->loanService->getById($loanId);

        $loanAccrueds = $this->loanService->getAccrued($loanId);

        $loanRepayments = $this->loanService->getLoanRepayments($loanId);

        return view(
            'admin::loans.overview',
            [
                'loan' => $loan,
                'installments' => $loan->installments(),
                'loanAccrueds' => $loanAccrueds,
                'loanRepayments' => $loanRepayments,
                'investorsShare' => $this->investorService->investorsLoanShare(null ,$loan)
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        // on 1st load of list page, we remove previous session
        $this->getSessionService()->add($this->cacheKey, []);

        return view(
            'admin::loans.list',
            [
                'cacheKey' => $this->cacheKey,
                'loans' => $this->getTableData(),
                'countries' => $this->countryService->getAll(),
                'loanTypes' => Loan::getTypesWithLabels(),
                'loanCountries' => $this->loanService->getLoansCountries(),
                'loanOriginators' => $this->loanService->getLoansOriginators(),
                'loanStatuses' => Loan::getMainStatuses(),
                'loanPaymentStatuses' => Loan::getPaymentStatuses(),
                'loanFinalPaymentStatuses' => Loan::getFinalPaymentStatuses(),
            ]
        );
    }

    /**
     * @param LoanSearchRequest $request
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
    public function refresh(LoanSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::loans.list-table',
            [
                'loans' => $this->getTableData(),
                'cacheKey' => $this->cacheKey,
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
        $cachedData = $this->getCachedData(Loan::class);
        // $cachedData = null;
        if ($cachedData === null) {
            $cachedData = $this->loanService->getLoansForAdmin(
                $limit ?? parent::getTableLength(),
                session($this->cacheKey, [])
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
    public function uploadNewLoans()
    {
        $files = $this->storageService->getImportLoanFiles();
        $allFiles = $this->loanImportService->getFilesByNames($files);

        return view('admin::loans.upload-loans', compact('allFiles'));
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

        if (!$this->loanImportService->isValidNewLoansFile($file)) {
            return redirect()
                ->route($this->indexUploadLoanFiles)
                ->with('fail', __('common.newLoansFileInvalid'));
        }

        // depends on btn selected we chose the file name
        $fileName = (
            $request->has('ImportSite') && 'ImportSite' == $request->get('ImportSite')
            ? StorageService::IMPORTED_LOANS_NAME_SITE
            : StorageService::IMPORTED_LOANS_NAME_OFFICE
        );


        $successfullImport = $this->loanImportService->importFile(
            $file,
            FileType::NEW_LOANS_ID,
            StorageService::IMPORT_LOANS_DIR,
            $fileName
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
    public function deleteDocumentWithNewLoans(int $loanDocId)
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
            StorageService::IMPORTED_LOANS_DIR
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
    public function downloadDocumentWithNewLoans(int $fileId)
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

    public function execute($fileId)
    {
        try {
            $output = [];
            $file = $this->fileService->getById($fileId);
            $fileName = str_replace(StorageService::IMPORT_LOANS_DIR, '', $file->file_name);
            foreach (self::COMMANDS_IMPORT_LOANS as $command) {
                $command = App::make($command);
                if ($command instanceof ImportLoans) {
                    Artisan::call($command->getName(),
                    [
                        'filename' => $fileName,
                    ]);
                } else {
                    Artisan::call($command->getName());
                }
                $output[$command->getNameForDb()] = Artisan::output();
            }
        } catch (Throwable $e) {
            throw new JsonException('Sorry, something went wrong. Please contact your administrator', 400);
        }

        return view(
            'admin::loans.output',
            ['commands' => $output]
        );
    }
}
