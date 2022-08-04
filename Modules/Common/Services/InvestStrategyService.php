<?php

namespace Modules\Common\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\InvestorInstallment;
use Modules\Common\Entities\InvestStrategy;
use Modules\Common\Repositories\InvestStrategyRepository;
use Modules\Core\Exceptions\NotFoundException;
use Modules\Core\Exceptions\ProblemException;
use Modules\Core\Services\BaseService;
use Throwable;

class InvestStrategyService extends BaseService
{
    private InvestStrategyRepository $investStrategyRepository;
    private InvestmentBunchService $investmentBunchService;

    /**
     * InvestStrategyService constructor.
     *
     * @param InvestStrategyRepository $investStrategyRepository
     * @param InvestmentBunchService $investmentBunchService
     */
    public function __construct(
        InvestStrategyRepository $investStrategyRepository,
        InvestmentBunchService $investmentBunchService
    ) {
        $this->investStrategyRepository = $investStrategyRepository;
        $this->investmentBunchService = $investmentBunchService;

        parent::__construct();
    }

    /**
     * @return mixed
     * @throws NotFoundException
     */
    public function getAll()
    {
        $investStrategy = $this->investStrategyRepository->getAll();

        if (!$investStrategy) {
            throw new NotFoundException(__('common.investStrategyNotFound'));
        }

        return $investStrategy;
    }

    /**
     * @param int $id
     *
     * @return mixed
     *
     * @throws NotFoundException
     */
    public function getById(int $id, int $investorId = null)
    {
        $investStrategy = $this->investStrategyRepository->getById(
            $id,
            $investorId
        );

        if (!$investStrategy) {
            throw new NotFoundException(__('common.investStrategyNotFound'));
        }

        return $investStrategy;
    }

    /**
     * @param array $data
     * @param Investor $investor
     *
     * @return \Modules\Common\Entities\InvestStrategy
     * @throws ProblemException
     */
    public function create(array $data, Investor $investor)
    {
        $data['investor_id'] = $investor->investor_id;
        $data['wallet_id'] = $investor->wallet()->wallet_id;
        $data['priority'] = InvestStrategy::getInvestorNextPriority($investor->investor_id);

        $data['loan_type'] = json_encode(
            isset($data['loan_type']) ? array('type' => $data['loan_type']) : array()
        );
        $data['loan_payment_status'] = json_encode(
            isset($data['loan_payment_status']) ? array('payment_status' => $data['loan_payment_status']) : array()
        );

        if (!empty($data['portfolio_size'])) {
            $data['max_portfolio_size'] = $data['portfolio_size'];
        }
        $data['portfolio_size'] = 0; // always ZERO on create
        $data['total_invested'] = 0; // always ZERO on create

        try {
            $investStrategy = $this->investStrategyRepository->create($data);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.investStrategyCreationFailed'));
        }
        return $investStrategy;
    }

    /**
     * @param $id
     * @param array $data
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function update($id, array $data, array $customProperties = [])
    {
        try {
            $investStrategy = $this->getById($id);

            $data['loan_type'] = json_encode(
                isset($data['loan_type']) ? array('type' => $data['loan_type']) : array()
            );
            $data['loan_payment_status'] = json_encode(
                isset($data['loan_payment_status']) ? array('payment_status' => $data['loan_payment_status']) : array()
            );

            $this->investStrategyRepository->edit(
                $investStrategy,
                $data,
                $customProperties
            );
        } catch (Throwable $e) {
            throw new ProblemException(__('common.investStrategyUpdateFailed'));
        }
    }

    /**
     * @param $id
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function delete($id, int $investorId = null)
    {
        $investStrategy = $this->getById($id, $investorId);

        try {
            $this->investStrategyRepository->delete($investStrategy);
            $this->reorderAllBelow($investStrategy);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.investStrategyDeletionFailed'));
        }
    }

    /**
     * @param $strategyId
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function enable(int $strategyId, int $investorId = null)
    {
        $investStrategy = $this->getById($strategyId, $investorId);
        if ($investStrategy->isActive()) {
            throw new ProblemException(__('common.investStrategyEnableForbidden'));
        }

        try {
            $this->investStrategyRepository->enable($investStrategy);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.investStrategyEnableFailed'));
        }
    }

    /**
     * @param $id
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function disable($id, int $investorId = null)
    {
        $investStrategy = $this->getById($id, $investorId);
        if (!$investStrategy->isActive()) {
            throw new ProblemException(__('common.investStrategyDisableForbidden'));
        }

        try {
            $this->investStrategyRepository->disable($investStrategy);
        } catch (Throwable $e) {
            throw new ProblemException(__('common.investStrategyDisableFailed'));
        }
    }

    /**
     * @param array $data
     *
     * @param int|null $length
     * @param bool $comparePortfolioSize
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getInvestorStrategies(
        array $data,
        ?int $length,
        bool $comparePortfolioSize = false
    ) {
        if (!empty($data['limit'])) {
            $length = $data['limit'];
            unset($data['limit']);
        }

        $whereConditions = $this->getWhereConditions($data);

        return $this->investStrategyRepository->getAll(
            $length,
            $whereConditions,
            ['priority'=>'ASC'],
            $comparePortfolioSize,
            true
        );
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     * @return array
     */
    public function getWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ): array {
        $where = [];

        $where[] = [
            'deleted',
            '=',
            0,
        ];
        $where[] = [
            'investor_id',
            '=',
            $data['investor_id'],
        ];

        unset($data['investor_id']);
        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param int|null $length
     * @param array $data
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getAllStrategy(
        ?int $length,
        array $data
    ) {
        $order = $this->getAdminOrderConditions($data);
        unset($data['order']);

        if (!empty($data['limit']) and $length != null) {
            $length = $data['limit'];
            unset($data['limit']);
        }
        if ($length == null) {
            unset($data['limit']);
        }

        $whereConditions = $this->getAdminWhereConditions($data);

        return $this->investStrategyRepository->getAll($length, $whereConditions, $order);
    }

    /**
     * @param array $data
     * @param array|string[] $names
     * @param string $prefix
     *
     * @return array
     */
    public function getAdminWhereConditions(
        array $data,
        array $names = ['name'],
        $prefix = ''
    ): array {
        $where = [];

        if (!empty($data['created_at'])) {
            if (!empty($data['created_at']['from'])) {
                $where[] = [
                    'invest_strategy.created_at',
                    '>=',
                    dbDate($data['created_at']['from'], '00:00:00'),
                ];
            }

            if (!empty($data['created_at']['to'])) {
                $where[] = [
                    'invest_strategy.created_at',
                    '<=',
                    dbDate($data['created_at']['to'], '23:59:59'),
                ];
            }
            unset($data['created_at']);
        }

        if (!empty($data['payment_status'])) {
            $where['loan_payment_status'] = $data['payment_status'];
            unset($data['payment_status']);
        }

        if (!empty($data['type'])) {
            $where['loan_type'] = $data['type'];
            unset($data['type']);
        }

        return array_merge($where, parent::getWhereConditions($data, $names, $prefix));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function getAdminOrderConditions(
        array $data
    ): array {
        $order = [];

        if (!empty($data['order']['invest_strategy']['loan_type'])) {
            $order['loan_type'] = $data['order']['invest_strategy']['loan_type'];
            unset($data['order']['invest_strategy']['loan_type']);
        }

        if (!empty($data['order']['invest_strategy']['loan_payment_status'])) {
            $order['loan_payment_status'] = $data['order']['invest_strategy']['loan_payment_status'];
            unset($data['order']['invest_strategy']['loan_payment_status']);
        }

        return array_merge($order, parent::getOrderConditions($data));
    }

    /**
     * @param $investStrategyId
     * @param int $limit
     *
     * @return mixed
     */
    public function getStrategyLoans($investStrategyId, int $limit)
    {
        return $this->investStrategyRepository->getAllLoans($investStrategyId, $limit);
    }

    /**
     * @param $id
     *
     * @throws NotFoundException
     * @throws ProblemException
     */
    public function reorderAllBelow(InvestStrategy $strategy)
    {
        return InvestStrategy::where([
            ['investor_id', '=', $strategy->investor_id],
            ['priority', '>', $strategy->priority],
            ['deleted', '=', 0],
        ])->decrement('priority');
    }

    public function getStrategiesPrincipalsForLoan(
        int $investorId,
        int $loanId,
        int $investmentId
    ): array
    {
        $rows = DB::select(DB::raw("
            SELECT
                ist.invest_strategy_id,
                SUM(ii.principal) as total_principal
            FROM invest_strategy ist
            JOIN investment_bunch ib on (
                ib.invest_strategy_id = ist.invest_strategy_id
                AND ib.investor_id  = ist.investor_id
            )
            JOIN investment inv on (
                inv.investment_bunch_id = ib.investment_bunch_id
                AND inv.investor_id  = ist.investor_id
            )
            JOIN investor_installment ii on (
                ii.investment_id = inv.investment_id
                AND ii.investor_id = ist.investor_id
                AND ii.paid = 0
                AND ii.loan_id = " . (int) $loanId . "
                AND ii.investment_id = " . (int) $investmentId . "
            )
            WHERE
                ist.investor_id = " . (int) $investorId . "
            GROUP BY ist.invest_strategy_id
        "));

        $result = [];
        foreach ($rows as $row) {
            $result[$row->invest_strategy_id] = $row->total_principal;
        }

        return $result;
    }

    public function getStrategyPrincipalForInstallment(
        InvestorInstallment $investorInstallment
    ): array
    {
        $investment = $investorInstallment->investment();
        if (!empty($investment->investment_id)) {
            $bunch = ($investment->investmentBunch())->first();
            if (!empty($bunch->invest_strategy_id)) {
                return [
                    $bunch->invest_strategy_id => $investorInstallment->principal,
                ];
            }
        }

        return [];

        // $rows = DB::select(DB::raw("
        //     SELECT
        //         ib.invest_strategy_id,
        //         ii.principal as total_principal
        //     FROM investor_installment ii
        //     JOIN investment inv on (
        //         inv.investment_id = ii.investment_id
        //         AND inv.investor_id  = ii.investor_id
        //     )
        //     JOIN investment_bunch ib on (
        //         ib.investment_bunch_id = inv.investment_bunch_id
        //         AND ib.investor_id  = inv.investor_id
        //     )
        //     WHERE
        //         ii.investor_installment_id = " . (int) $investorInstallment->investor_installment_id . "
        //     GROUP BY ist.invest_strategy_id
        // "));

        // $result = [];
        // foreach ($rows as $row) {
        //     $investor[$row->invest_strategy_id] = $row->total_principal;
        // }

        // return $result;
    }

    public function updateStrategiesAmounts(array $strategiesRepayments)
    {
        foreach ($strategiesRepayments as $strategyId => $repaidTotalPrincipal) {

            $strategy = $this->getById($strategyId);
            if (!empty($strategy->invest_strategy_id)) {
                $strategy->updateAmounts($repaidTotalPrincipal);
            }
        }
    }

    public function getStrategiesBalance()
    {
        $query = <<<EOD
select res.*
from (
    select
        is2.investor_id ,
        is2.invest_strategy_id ,
        is2."name" ,
        is2.reinvest ,
        coalesce(is2.max_portfolio_size, 0) as  max_portfolio_size,
        coalesce(is2.portfolio_size, 0) as  portfolio_size,
        coalesce(is2.total_invested, 0) as  total_invested,
        coalesce(is2.total_received, 0) as  total_received,
        (
            select coalesce(SUM(i2.amount),0)
            from investment i2
            join investment_bunch ib on i2.investment_bunch_id = ib.investment_bunch_id
            where ib.invest_strategy_id = is2.invest_strategy_id
        ) as total_invested_investments,
        (
            (
                select coalesce(SUM(ii.principal), 0)
                from investor_installment ii
                join investment i3 on i3.investment_id = ii.investment_id
                join investment_bunch ib3 on i3.investment_bunch_id = ib3.investment_bunch_id
                where ib3.invest_strategy_id = is2.invest_strategy_id and ii.paid = 1
            ) + (
                select coalesce(SUM(iih.principal), 0)
                from investor_installment_history iih
                join investment i3 on i3.investment_id = iih.investment_id
                join investment_bunch ib3 on i3.investment_bunch_id = ib3.investment_bunch_id
                where ib3.invest_strategy_id = is2.invest_strategy_id and iih.paid = 1
            )
        ) as total_received_inv_installments,
        (
            select coalesce(SUM(ii.principal), 0)
            from investor_installment ii
            join investment i3 on i3.investment_id = ii.investment_id
            join investment_bunch ib3 on i3.investment_bunch_id = ib3.investment_bunch_id
            where ib3.invest_strategy_id = is2.invest_strategy_id and ii.paid = 0
        ) as total_outstanding_inv_installments,
        (
            select coalesce(SUM(ii.principal), 0)
            from investor_installment ii
            join loan l2 on l2.loan_id = ii.loan_id
            join investment i3 on i3.investment_id = ii.investment_id
            join investment_bunch ib3 on i3.investment_bunch_id = ib3.investment_bunch_id
            where ib3.invest_strategy_id = is2.invest_strategy_id and ii.paid = 1 and l2.unlisted = 0
        ) as total_received_lost_installments
    from invest_strategy is2
    where is2.deleted = 0 AND is2.reinvest = 1
) as res
where res.portfolio_size != res.total_outstanding_inv_installments
order by res.investor_id, res.invest_strategy_id
EOD;

        return DB::select(
            DB::raw($query)
        );
    }

    public function changePlaces(
        InvestStrategy $decrementStrategy,
        InvestStrategy $incrementStrategy
    )
    {
        if ($decrementStrategy->investor_id != $incrementStrategy->investor_id) {
            return false;
        }

        return DB::select(
            DB::raw("
                UPDATE invest_strategy
                SET
                    priority = (
                        CASE WHEN invest_strategy_id = :decrementStrategyId
                        THEN (select x1.priority from invest_strategy x1 where x1.invest_strategy_id = :incrementStrategyId)
                        ELSE (select x2.priority from invest_strategy x2 where x2.invest_strategy_id = :decrementStrategyId)
                        END
                    )
                WHERE investor_id = :investorId and invest_strategy_id in (:decrementStrategyId, :incrementStrategyId)
            "),
            [
                'investorId' => $decrementStrategy->investor_id,
                'decrementStrategyId' => $decrementStrategy->invest_strategy_id,
                'incrementStrategyId' => $incrementStrategy->invest_strategy_id,
            ],
        );
    }
}
