<?php

namespace Modules\Profile\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Common\Entities\Investment;
use Modules\Common\Entities\Loan;
use Modules\Common\Services\CartSecondaryLoansService;
use Modules\Common\Exports\MyInvestmentsExport;
use Modules\Common\Services\InvestmentService;
use Modules\Common\Services\MarketSecondaryService;
use Modules\Core\Controllers\BaseController;
use Modules\Core\Exceptions\BaseException;
use Modules\Core\Exceptions\JsonException;
use Modules\Profile\Http\Requests\InvestmentSearchRequest;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MyInvestmentController extends BaseController
{
    protected InvestmentService $investmentService;
    protected CartSecondaryLoansService $cartSecondaryLoansService;
    protected MarketSecondaryService $marketSecondaryService;

    /**
     * MyInvestmentController constructor.
     * @param InvestmentService $investmentService
     * @param CartSecondaryLoansService $cartSecondaryLoansService
     * @param MarketSecondaryService $marketSecondaryService
     * @throws \ReflectionException
     */
    public function __construct(

        InvestmentService $investmentService,
        CartSecondaryLoansService $cartSecondaryLoansService,
        MarketSecondaryService $marketSecondaryService
    ) {
        $this->investmentService = $investmentService;
        $this->cartSecondaryLoansService = $cartSecondaryLoansService;
        $this->marketSecondaryService = $marketSecondaryService;

        parent::__construct();
    }

    public function list()
    {
        try {
            // on 1st load of list page, we remove previous session
            $this->getSessionService()->add($this->cacheKey, []);

            $investor = $this->getInvestor();

            $investmentsTotalSum = $this->investmentService->getByInvestorWhereConditions(
                null,
                [],
                (int)$investor->investor_id
            );

            $investment_ids = [];
            foreach ($this->getTableData() as $item) {
                $investment_ids[] = $item->investment_id;
            }

            $loansInCart = $this->cartSecondaryLoansService->getManyByInvestmentId($investor->investor_id, $investment_ids);

            $loansOnMarket = $this->marketSecondaryService->getManyByInvestmentId($investor->investor_id, $investment_ids);

            $json = json_encode($loansOnMarket->keyBy('investment_id')->toArray());

            return view(
                'profile::my-invest.list',
                [
                    'investor' => $investor,
                    'cacheKey' => $this->cacheKey,
                    'investments' => $this->getTableData(),
                    'loansInCart' => $loansInCart,
                    'loansOnMarket' => $loansOnMarket,
                    'json' => $json,
                    'totalInvestments' => $this->investmentService->getInvestmentsCount(
                        (int)$investor->investor_id,
                        Loan::STATUS_ACTIVE
                    ),
                    'investmentsTotalSum' => $investmentsTotalSum
                ]
            );
        } catch (Throwable $e) {
            echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();
            return view('errors.generic');
        }
    }

    /**
     * @param InvestmentSearchRequest $request
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
     * @throws Throwable
     */
    public function refresh(InvestmentSearchRequest $request)
    {
        $validated = $request->validated();
        try {
            parent::setFiltersFromRequest($request);

            $investorId = (int)\Auth::guard('investor')->user()->investor_id;
            $investmentsTotalSum = $this->investmentService->getByInvestorWhereConditions(
                null,
                session($this->cacheKey, []),
                $investorId
            );

            $status = Loan::STATUS_ACTIVE;
            if (!empty($validated['loan']['status'])) {
                $status = $validated['loan']['status'];
            }

            if ($status == Loan::STATUS_REPAID) {
                $investmentsTotalSum = null;
            }

            $investment_ids = [];
            foreach ($this->getTableData(session($this->cacheKey . '.limit')) as $item) {
                $investment_ids[] = $item->investment_id;
            }

            $loansInCart = $this->cartSecondaryLoansService->getManyByInvestmentId($investorId, $investment_ids);

            $loansOnMarket = $this->marketSecondaryService->getManyByInvestmentId($investorId, $investment_ids);


            $json = json_encode($loansOnMarket->keyBy('investment_id')->toArray());

            return view(
                'profile::my-invest.list-table',
                [
                    'investments' => $this->getTableData(session($this->cacheKey . '.limit')),
                    'investor' => $this->getInvestor(),
                    'cacheKey' => $this->cacheKey,
                    'loansInCart' => $loansInCart,
                    'loansOnMarket' => $loansOnMarket,
                    'json' => $json,
                    'totalInvestments' => $this->investmentService->getInvestmentsCount(
                        $investorId,
                        $status
                    ),
                    'investmentsTotalSum' => $investmentsTotalSum
                ]
            )->render();
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
    }

    /**
     * @param int|null $limit
     */
    protected function getTableData(int $limit = null)
    {
        if ($limit != session($this->cacheKey . '.limit')) {
            $data = $this->getSessionService()->get($this->cacheKey);
            $this->getSessionService()->remove($this->cacheKey);
            $data['limit'] = $limit;
            $this->getSessionService()->add($this->cacheKey, $data);
        }

        $investorId = (int) $this->getInvestor()->investor_id;
        return $this->investmentService->getInvestorInvestments(
            $investorId,
            session($this->cacheKey, []),
            $limit ?? parent::getTableLength()
        );
    }

    /**
     * @return StreamedResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws JsonException
     */
    public function export(): StreamedResponse
    {
        $this->getSessionService()->remove($this->cacheKey . '.limit');

        try {
            $myInvestmentExport = new MyInvestmentsExport(
                $this->investmentService->getInvestorInvestments(
                    (int)$this->getInvestor()->investor_id,
                    session($this->cacheKey, [])
                )
            );

            $fileName = date('Ymd') . '-my-investments-export';
        } catch (Throwable $e) {
            throw new JsonException(
                $e instanceof BaseException ? $e->getMessage() : __('common.SomethingWentWrong'),
                400
            );
        }
        return $this->getStorageService()->download(
            $fileName,
            ['collectionClass' => $myInvestmentExport],
            'xlsx',
        );
    }
}
