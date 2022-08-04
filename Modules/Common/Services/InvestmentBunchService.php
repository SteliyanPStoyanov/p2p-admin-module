<?php

namespace Modules\Common\Services;

use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartInterface;
use \Throwable;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Repositories\InvestmentBunchRepository;
use Modules\Core\Services\BaseService;

class InvestmentBunchService extends BaseService
{
    private InvestmentBunchRepository $investmentBunchRepository;

    /**
     * @param InvestmentBunchRepository $investmentBunchRepository
     */
    public function __construct(
        InvestmentBunchRepository $investmentBunchRepository
    ) {
        $this->investmentBunchRepository = $investmentBunchRepository;

        parent::__construct();
    }

    public function getById(int $investmentBunchId)
    {
        return $this->investmentBunchRepository->getById($investmentBunchId);
    }

    /**
     * @param int $investorId
     * @param string $amount
     * @param array $filters
     * @param int $finished
     *
     * @return \Modules\Common\Entities\InvestmentBunch
     */
    public function create(
        int $investorId,
        string $amount,
        array $filters,
        int $possibleCountToBuy,
        int $finished = 0,
        int $investStrategyId = null
    )
    {
        $data = [];
        $data['total'] = $possibleCountToBuy;
        $data['amount'] = $amount;
        $data['filters'] = json_encode($filters);
        $data['finished'] = $finished;
        $data['investor_id'] = $investorId;

        if (!empty($investStrategyId)) {
            $data['invest_strategy_id'] = $investStrategyId;
        }

        return $this->investmentBunchRepository->create($data);
    }

    public function createFromStrategy(
        int $investorId,
        InvestStrategy $strategy,
        int $possibleCountToBuy,
        bool $multiRun = false
    )
    {
        try {
            $filters = $this->getFiltersFromStrategy($strategy);

            $data = [];
            $data['total'] = $possibleCountToBuy;
            $data['filters'] = json_encode($filters);
            $data['investor_id'] = $investorId;
            $data['invest_strategy_id'] = $strategy->getId();
            $data['multi_run'] = (true === $multiRun ? 1 : 0);

            $bunch = $this->investmentBunchRepository->create($data);
            return $bunch;

        } catch (Throwable $e) {
            Log::channel('invest_all')->error('Failed creating inv.bunch:'
                . ' #' . $investorId
                . ', strategy = ' . $strategy->getId()
                . ', possibleCountToBuy = ' . $possibleCountToBuy
                . ', $multiRun = ' . ($multiRun === true ? 1 : 0)
                . ', ERROR: ' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine()
            );

            return null;
        }
    }

    public function createFromSecondaryCart(
        int $investorId,
        CartInterface $cart,
        int $possibleCountToBuy,
        bool $multiRun = false
    )
    {
        try {
            $filters = [];

            $data = [];
            $data['total'] = $possibleCountToBuy;
            $data['filters'] = json_encode($filters);
            $data['investor_id'] = $investorId;
            $data['cart_secondary_id'] = $cart->getCartId();
            $data['multi_run'] = (true === $multiRun ? 1 : 0);

            return $this->investmentBunchRepository->create($data);

        } catch (Throwable $e) {
            Log::channel('invest_all')->error('Failed creating inv.bunch:'
                . ' #' . $investorId
                . ', cart = ' . $cart->getCartId()
                . ', possibleCountToBuy = ' . $possibleCountToBuy
                . ', $multiRun = ' . ($multiRun === true ? 1 : 0)
                . ', ERROR: ' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine()
            );

            return null;
        }
    }

    /**
     * Return an array with filters on loan table
     * They will be included into investment_bunch
     * @param  InvestStrategy $strategy
     * @return array
     */
    public function getFiltersFromStrategy(InvestStrategy $strategy): array
    {
        $filter = [];

        if (!empty($strategy->min_amount)) {
            $filter['amount_available']['from'] = $strategy->min_amount;
        }
        if (!empty($strategy->max_amount)) {
            $filter['amount_available']['to'] = $strategy->max_amount;
        }
        if (!empty($strategy->min_interest_rate)) {
            $filter['interest_rate_percent']['from'] = $strategy->min_interest_rate;
        }
        if (!empty($strategy->max_interest_rate)) {
            $filter['interest_rate_percent']['to'] = $strategy->max_interest_rate;
        }
        if (!empty($strategy->min_loan_period)) {
            $filter['period']['from'] = $strategy->min_loan_period;
        }
        if (!empty($strategy->max_loan_period)) {
            $filter['period']['to'] = $strategy->max_loan_period;
        }
        if (!empty($strategy->loan_type)) {
            $types = json_decode($strategy->loan_type, true);
            if (!empty($types["type"])) {
                foreach ($types["type"] as $key => $type) {
                    $filter["loan"]["type"][] = $type;
                }
            }
        }
        if (!empty($strategy->loan_payment_status)) {
            $paymentStatuses = json_decode($strategy->loan_payment_status, true);
            if (!empty($paymentStatuses["payment_status"])) {
                $filter["payment_status"] = [];
                foreach ($paymentStatuses["payment_status"] as $key => $paymentStatus) {
                    $filter["payment_status"][] = Portfolio::getQualityMapping($paymentStatus, true);
                }
            }
        }
        if ($strategy->include_invested >= 0 && $strategy->include_invested <= 0) {
            $filter['include_invested'] = 'exclude';
        }

        return $filter;
    }

    /**
     * @param int $investmentBunchId
     *
     * @return \Modules\Common\Entities\InvestmentBunch
     */
    public function investBunchAndStrategyUpdate(
        int $investmentBunchId,
        float $amount
    )
    {
        $investmentBunch = $this->investmentBunchRepository->getById(
            $investmentBunchId
        );

        $strategy = $investmentBunch->investStrategy();
        if (!empty($strategy->invest_strategy_id)) {
            $strategy->portfolio_size = (float) $strategy->portfolio_size + $amount;
            $strategy->total_invested = (float) $strategy->total_invested + $amount;
            $strategy->save();
        }

        return $investmentBunch->increment('count');
    }
}
