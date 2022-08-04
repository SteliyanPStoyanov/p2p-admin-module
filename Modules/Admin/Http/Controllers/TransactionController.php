<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\Http\Requests\TransactionSearchRequest;
use Modules\Admin\Http\Requests\UploadPayments;
use Modules\Common\Entities\FileType;
use Modules\Common\Services\TransactionService;
use Modules\Common\Entities\Transaction;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Services\StorageService;

class TransactionController extends BaseController
{
    protected TransactionService $transactionService;

    /**
     * TransactionController constructor.
     *
     * @param TransactionService $transactionService
     *
     * @throws \ReflectionException
     */
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;

        parent::__construct();
    }

    public function list(TransactionSearchRequest $request)
    {
        $this->checkForRequestParams($request);

        // on 1st load of list page, we remove previous session and set default order
        $this->getSessionService()->add($this->cacheKey, ['order' => ['transaction' => ['transaction_id' => 'desc']]]);

        return view(
            'admin::transactions.list',
            [
                'transactions' => $this->getTableData(),
                'types' => Transaction::getTypes(),
                'cacheKey' => $this->cacheKey,
            ]
        );
    }

    /**
     * @param UploadPayments $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFile(UploadPayments $request)
    {
        $file = $request->import_file;

        $imported = $this->transactionService->importPayments(
            $file,
            FileType::IMPORTED_PAYMENT_ID,
            StorageService::IMPORTED_PAYMENTS_DIR,
            StorageService::IMPORTED_PAYMENTS_NAME
        );

        if (!$imported) {
            return redirect()
                ->route('admin.transactions.list')
                ->with(
                    'fail',
                    __('common.unsuccessfullyImportedPayments')
                );
        }

        return redirect()
            ->route('admin.transactions.list')
            ->with(
                'success',
                __('common.successfullyImportedPaymentDocument')
            );
    }

    /**
     * @param TransactionSearchRequest $request
     */
    protected function checkForRequestParams(TransactionSearchRequest $request
    ) {
        if ($request->exists(
            ['name', 'phone', 'email', 'active', 'createdAt', 'updatedAt']
        )) {
            $this->cleanFilters();
            parent::setFiltersFromRequest($request);
        }
    }

    /**
     * @param TransactionSearchRequest $request
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
    public function refresh(TransactionSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::transactions.list-table',
            [
                'transactions' => $this->getTableData(),
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
        $cachedData = $this->getCachedData(Transaction::class);

        if ($cachedData === null) {
            $cachedData = $this->transactionService->getByWhereConditions(
                $limit ?? parent::getTableLength(),
                session($this->cacheKey, [])
            );
            $this->setCacheData($cachedData);
        }

        return $cachedData;
    }
}
