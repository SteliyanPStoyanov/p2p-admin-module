<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Admin\Http\Requests\LoanSearchRequest;
use Modules\Admin\Http\Requests\WalletSearchRequest;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Wallet;
use Modules\Common\Services\CountryService;
use Modules\Common\Services\LoanService;
use Modules\Common\Services\WalletService;
use Modules\Core\Controllers\BaseController;

class WalletController extends BaseController
{
    protected WalletService $walletService;

    public function __construct(
        WalletService $walletService
    ) {
        $this->walletService = $walletService;

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
            'admin::wallets.list',
            [
                'cacheKey' => $this->cacheKey,
                'wallets' => $this->getTableData(),
                'types' => Investor::getTypes()
            ]
        );
    }

    /**
     * @param WalletSearchRequest $request
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
    public function refresh(WalletSearchRequest $request)
    {
        parent::setFiltersFromRequest($request);

        return view(
            'admin::wallets.list-table',
            [
                'wallets' => $this->getTableData(),
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
        $cachedData = $this->getCachedData(Wallet::class);

        if ($cachedData === null) {
            $cachedData = $this->walletService->getByWhereConditions(
                $limit ?? parent::getTableLength(),
                session($this->cacheKey, [])
            );
            $this->setCacheData($cachedData);
        }

        return $cachedData;
    }
}
