<?php

namespace Modules\Common\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\InvestmentBunch;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Entities\InvestStrategyHistory;
use Modules\Common\Entities\Loan;
use Modules\Common\Entities\Portfolio;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartInterface;
use Modules\Common\Jobs\InvestAll\InvestAllJob;
use Modules\Common\Repositories\InvestmentRepository;
use Modules\Core\Services\BaseService;
use Throwable;

class InvestmentService extends BaseService
{
    private InvestmentRepository $investmentRepository;
    private InvestmentBunchService $investmentBunchService;

    /**
     * InvestmentService constructor.
     *
     * @param InvestmentRepository $investmentRepository
     * @param InvestmentBunchService $investmentBunchService
     */
    public function __construct(
        InvestmentRepository $investmentRepository,
        InvestmentBunchService $investmentBunchService
    ) {
        $this->investmentRepository = $investmentRepository;
        $this->investmentBunchService = $investmentBunchService;

        parent::__construct();
    }

    /**
     * @param int $investorId
     * @param array $where
     * @param int|null $limit
     * @return LengthAwarePaginator
     */
    public function getInvestorInvestments(
        int $investorId,
        array $where,
        int $limit = null
    ) {
        return $this->getByWhereConditions($limit, $where, $investorId);
    }

    /**
     * @param int|null $length
     * @param array $data
     * @param int $investorId
     * @return LengthAwarePaginator
     */
    public function getByWhereConditions(
        int $length = null,
        array $data,
        int $investorId
    ) {
        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }
        $orderConditions = $this->getOrderConditions($data);

         if (count($orderConditions) === 0) {
            $orderConditions = [
                'investment.created_at' => 'desc',
                'investment.investment_id' => 'desc'
            ];
        }

        unset($data['order']);

        $whereConditions = $this->getInvestorWhereConditions($data, $investorId);

        return $this->investmentRepository->getAll($length, $whereConditions, $orderConditions);
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    protected function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ) {
        $where[] = [
            'investment.investor_id',
            '=',
            Auth::guard('investor')->user()->investor_id
        ];


        if (!empty($data['loan_created_at'])) {
            if (!empty($data['loan_created_at']['from'])) {
                $where[] = [
                    'loan.created_at',
                    '>=',
                    dbDate($data['loan_created_at']['from'], '00:00:00'),
                ];
            }

            if (!empty($data['loan_created_at']['to'])) {
                $where[] = [
                    'loan.created_at',
                    '<=',
                    dbDate($data['loan_created_at']['to'], '23:59:59'),
                ];
            }
            unset($data['loan_created_at']);
        }

        if (!empty($data['interest_rate_percent'])) {
            if (!empty($data['interest_rate_percent']['from'])) {
                $where[] = [
                    'loan.interest_rate_percent',
                    '>=',
                    $data['interest_rate_percent']['from'],
                ];
            }

            if (!empty($data['interest_rate_percent']['to'])) {
                $where[] = [
                    'loan.interest_rate_percent',
                    '<=',
                    $data['interest_rate_percent']['to'],
                ];
            }

            unset($data['interest_rate_percent']);
        }

        if (!empty($data['period'])) {
            if (!empty($data['period']['from'])) {
                $where[] = [
                    'loan.final_payment_date',
                    '>=',
                    Carbon::now()->addMonths($data['period']['from']),
                ];
            }

            if (!empty($data['period']['to'])) {
                $where[] = [
                    'loan.final_payment_date',
                    '<=',
                    Carbon::now()->addMonths($data['period']['to']),
                ];
            }

            unset($data['period']);
        }


        if (!empty($data['created_at'])) {
            if (!empty($data['created_at']['from'])) {
                $where[] = [
                    'investment.created_at',
                    '>=',
                    dbDate($data['created_at']['from'], '00:00:00'),
                ];
            }

            if (!empty($data['created_at']['to'])) {
                $where[] = [
                    'investment.created_at',
                    '<=',
                    dbDate($data['created_at']['to'], '23:59:59'),
                ];
            }
            unset($data['created_at']);
        }


        if (!empty($data['payment_status'])) {
            foreach ($data['payment_status'] as $range) {
                $where['loan.payment_status'][] = Portfolio::getQualityMapping($range);
            }
            unset($data['payment_status']);
        }

        if (!empty($data['final_payment_status'])) {
            foreach ($data['final_payment_status'] as $finalPaymentStatus) {
                $where['loan.final_payment_status'][] = $finalPaymentStatus;
            }
            unset($data['payment_status']);
        }

        if (!empty($data['loan']['status'])) {
            if ($data['loan']['status'] == Loan::STATUS_REPAID) {
                $where[] = [
                    'loan.unlisted',
                    '=',
                    1,
                ];
            } else {
                $where[] = [
                    'loan.status',
                    '=',
                    $data['loan']['status'],
                ];
            }
            unset($data['loan']['status']);
        } else {
            $where[] = [
                'loan.status',
                '=',
                Loan::STATUS_ACTIVE
            ];
        }
        if (!empty($data['loan']['type'])) {
            $where[] = [
                'loan.type',
                '=',
                $data['loan']['type'],
            ];
            unset($data['loan']['type']);
        }
        unset($data['loan']);

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * Create investment bunch and put in to queue with run_now status(if there is no active bunches)
     * Used in:
     *     - InvestController.investAll() - manual
     *
     * @param int $investorId
     * @param float $amount
     * @param array $filters
     * @param int $possibleCountToBuy
     */
    public function massInvestByAmountAndFilters(
        int $investorId,
        float $amount,
        array $filters,
        int $possibleCountToBuy
    ) {
        try {
            $investmentBunch = $this->investmentBunchService->create(
                $investorId,
                $amount,
                $filters,
                $possibleCountToBuy
            );

            if (empty($investmentBunch->investment_bunch_id)) {
                Log::channel('invest_all')->error(
                    'Failed creating inv.bunch:'
                    . ' #' . $investorId
                    . ' amount =' . $amount
                    . ', count = ' . $possibleCountToBuy
                    . ', filters: ' . json_encode($filters)
                );

                return false;
            }
        } catch (Throwable $e) {
            Log::channel('invest_all')->error(
                'Failed creating inv.bunch:'
                . ' #' . $investorId
                . ' amount =' . $amount
                . ', count = ' . $possibleCountToBuy
                . ', filters: ' . json_encode($filters)
                . ', ERROR: ' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine()
            );

            return false;
        }

        return $this->runBunch($investmentBunch, null, true);
    }

    /**
     * Create investment bunch and put in to queue with run_now status(if there is no active bunches)
     * Used in:
     *     - AutoInvest command
     *
     * @param InvestStrategy $strategy
     * @param bool $multiRun -  after finishing run next strategy
     * @return bool
     */
    public function massInvestByStrategy(
        InvestStrategy $strategy,
        bool $multiRun = false
    ): bool {
        $investorId = $strategy->investor_id;
        $possibleCountToBuy = $strategy->getMaxPossibleInvestmentsCount();

        try {
            $investmentBunch = $this->investmentBunchService->createFromStrategy(
                $investorId,
                $strategy,
                $possibleCountToBuy,
                $multiRun
            );

            if (empty($investmentBunch->investment_bunch_id)) {
                Log::channel('invest_all')->error(
                    'Failed creating (auto)inv.bunch:'
                    . ' #' . $investorId
                    . ' strategy#' . $strategy->getId()
                    . ', count = ' . $possibleCountToBuy
                );

                return false;
            }
        } catch (Throwable $e) {
            Log::channel('invest_all')->error(
                'Failed creating (auto)inv.bunch:'
                . ' #' . $investorId
                . ' strategy#' . $strategy->getId()
                . ', count = ' . $possibleCountToBuy
                . ', ERROR: ' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine()
            );

            return false;
        }

        return $this->runBunch($investmentBunch, null, true);
    }

    public function massInvestSecondaryMarket(
        CartInterface $cartSecondary,
        bool $multiRun = false
    ) {
        $investorId = $cartSecondary->getInvestor()->investor_id;
        $possibleCountToBuy = count($cartSecondary->getLoans());

        try {
            $investmentBunch = $this->investmentBunchService->createFromSecondaryCart(
                $investorId,
                $cartSecondary,
                $possibleCountToBuy,
                $multiRun
            );
        } catch (Throwable $e) {
            Log::channel('invest_all')->error(
                'Failed creating (auto)inv.bunch:'
                . ' #' . $investorId
                . ' strategy#' . $cartSecondary->getCartId()
                . ', count = ' . $possibleCountToBuy
                . ', ERROR: ' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine()
            );

            return false;
        }

        return $this->runBunch($investmentBunch, null, true);
    }

    /**
     * Put job in the queue
     *
     * @param InvestmentBunch $bunch
     * @param int|null $loanId - if passed, will search loan after that
     * @param bool|boolean $runNow - direct run or delay
     * @return bool
     */
    public function runBunch(
        InvestmentBunch $bunch,
        int $loanId = null,
        bool $runNow = false
    ): bool {
        try {
            InvestAllJob::pushToQueue($bunch, $loanId, $runNow);
        } catch (Throwable $e) {
            Log::channel('invest_all')->error(
                'Failed to run mass invest, bunch: '
                . json_encode($bunch->toArray())
                . ', ERROR: ' . $e->getMessage()
                . ', file: ' . $e->getFile()
                . ', line: ' . $e->getLine()
            );

            return false;
        }

        return true;
    }

    /**
     * @param int $investorId
     * @param string|null $status
     * @return int
     */
    public function getInvestmentsCount(int $investorId, string $status = null)
    {
        return $this->investmentRepository->getInvestmentsCount(
            $investorId,
            $status
        );
    }

    /**
     * @param int|null $length
     * @param array $data
     * @param int $investorId
     * @return LengthAwarePaginator
     */
    public function getByInvestorWhereConditions(
        ?int $length,
        array $data,
        int $investorId
    ) {
        if (!empty($data['limit']) and $length != null) {
            $length = $data['limit'];
            unset($data['limit']);
        }
        if ($length == null) {
            unset($data['limit']);
        }

        $orderConditions = $this->getOrderConditions($data);
        unset($data['order']);

        $whereConditions = $this->getInvestorWhereConditions($data, $investorId);

        return $this->investmentRepository->getAll($length, $whereConditions, $orderConditions);
    }

    /**
     * @param array $data
     * @param int $investorId
     * @param array|string[] $names
     * @param string $prefix
     * @return array
     */
    protected function getInvestorWhereConditions(
        array $data,
        int $investorId,
        array $names = ['name'],
        string $prefix = ''
    ) {
        $where[] = [
            'investment.investor_id',
            '=',
            $investorId
        ];

        if (!isset($data['deleted'])) {
            $where[] = [
                'investment.deleted',
                '=',
                0,
            ];
        }

        if (!empty($data['loan_created_at'])) {
            if (!empty($data['loan_created_at']['from'])) {
                $where[] = [
                    'loan.created_at',
                    '>=',
                    dbDate($data['loan_created_at']['from'], '00:00:00'),
                ];
            }

            if (!empty($data['loan_created_at']['to'])) {
                $where[] = [
                    'loan.created_at',
                    '<=',
                    dbDate($data['loan_created_at']['to'], '23:59:59'),
                ];
            }
            unset($data['loan_created_at']);
        }

        if (!empty($data['created_at'])) {
            if (!empty($data['created_at']['from'])) {
                $where[] = [
                    'investment.created_at',
                    '>=',
                    dbDate($data['created_at']['from'], '00:00:00'),
                ];
            }

            if (!empty($data['created_at']['to'])) {
                $where[] = [
                    'investment.created_at',
                    '<=',
                    dbDate($data['created_at']['to'], '23:59:59'),
                ];
            }
            unset($data['created_at']);
        }
        if (!empty($data['interest_rate_percent'])) {
            if (!empty($data['interest_rate_percent']['from'])) {
                $where[] = [
                    'loan.interest_rate_percent',
                    '>=',
                    $data['interest_rate_percent']['from'],
                ];
            }

            if (!empty($data['interest_rate_percent']['to'])) {
                $where[] = [
                    'loan.interest_rate_percent',
                    '<=',
                    $data['interest_rate_percent']['to'],
                ];
            }

            unset($data['interest_rate_percent']);
        }

        if (!empty($data['period'])) {
            if (!empty($data['period']['from'])) {
                $where[] = [
                    'loan.final_payment_date',
                    '>=',
                    Carbon::now()->addMonths($data['period']['from']),
                ];
            }

            if (!empty($data['period']['to'])) {
                $where[] = [
                    'loan.final_payment_date',
                    '<=',
                    Carbon::now()->addMonths($data['period']['to']),
                ];
            }

            unset($data['period']);
        }

        if (!empty($data['investment']['loan_id'])) {
            $where[] = [
                'investment.loan_id',
                '=',
                $data['investment']['loan_id']
            ];
            unset($data['investment']);
        }

        if (!empty($data['originator'])) {
            $where[] = [
                'loan.originator_id',
                '=',
                $data['originator']
            ];
            unset($data['originator']);
        }

        if (!empty($data['payment_status'])) {
            foreach ($data['payment_status'] as $range) {
                $where['loan.payment_status'][] = Portfolio::getQualityMapping($range);
            }
            unset($data['payment_status']);
        }

        if (!empty($data['final_payment_status'])) {
            foreach ($data['final_payment_status'] as $finalPaymentStatus) {
                $where['loan.final_payment_status'][] = $finalPaymentStatus;
            }
            unset($data['payment_status']);
        }

        if (!empty($data['invested_amount'])) {
            if (!empty($data['invested_amount']['from'])) {
                $where['invested_amount']['from'] = $data['invested_amount']['from'];
            }
            if (!empty($data['invested_amount']['to'])) {
                $where['invested_amount']['to'] = $data['invested_amount']['to'];
            }

            unset($data['invested_amount']);
        }
        if (!empty($data['loan']['status'])) {
            if ($data['loan']['status'] == Loan::STATUS_REPAID) {
                $where[] = [
                    'loan.unlisted',
                    '=',
                    1,
                ];
                $where['loan_unlisted'] = 1;
            } else {
                $where[] = [
                    'loan.status',
                    '=',
                    $data['loan']['status'],
                ];
            }
            unset($data['loan']['status']);
        } else {
            $where[] = [
                'loan.status',
                '=',
                Loan::STATUS_ACTIVE
            ];
        }
        if (!empty($data['loan']['type'])) {
            $where[] = [
                'loan.type',
                '=',
                $data['loan']['type'],
            ];
            unset($data['loan']['type']);
        }
        unset($data['loan']);

        if (!empty($data['investment']['listed'])) {
            $where['card'] = $data['investment']['listed'];
            unset($data['investment']['listed']);
        }

        if (isset($data['market']) && $data['market'] <= 0) {
            $where['investment.parent_id'] = 'IS NULL';

            unset($data['market']);
        }

        if (isset($data['market']) && $data['market'] == 1) {
            $where['investment.parent_id'] = 'IS NOT NULL';

            unset($data['market']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param array $data
     */
    public function saveStrategyHistory(array $data): void
    {
        $investStrategyHistory = new InvestStrategyHistory();
        $investStrategyHistory->fill($data);
        $investStrategyHistory->save();
    }
}
