<?php

namespace Modules\Common\Services;

use Modules\Admin\Entities\Administrator;
use Modules\Common\Repositories\InvestStrategyHistoryRepository;
use Modules\Core\Services\BaseService;

class InvestStrategyHistoryService extends BaseService
{
    private InvestStrategyHistoryRepository $investStrategyHistoryRepository;
    private InvestmentBunchService $investmentBunchService;

    /**
     * InvestStrategyService constructor.
     *
     * @param InvestStrategyHistoryRepository $investStrategyHistoryRepository
     * @param InvestmentBunchService $investmentBunchService
     */
    public function __construct(
        InvestStrategyHistoryRepository $investStrategyHistoryRepository,
        InvestmentBunchService $investmentBunchService
    )
    {
        $this->investStrategyHistoryRepository = $investStrategyHistoryRepository;
        $this->investmentBunchService = $investmentBunchService;

        parent::__construct();
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
    )
    {
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

        return $this->investStrategyHistoryRepository->getAll($length, $whereConditions, $order);
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
    ): array
    {
        $where = [];

        $where[] = [
            'invest_strategy_history.archived_by',
            '!=',
            Administrator::SYSTEM_ADMINISTRATOR_ID,
        ];

        if (!empty($data['created_at'])) {
            if (!empty($data['created_at']['from'])) {
                $where[] = [
                    'invest_strategy_history.created_at',
                    '>=',
                    dbDate($data['created_at']['from'], '00:00:00'),
                ];
            }

            if (!empty($data['created_at']['to'])) {
                $where[] = [
                    'invest_strategy_history.created_at',
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
    ): array
    {
        $order = [];

        if (!empty($data['order']['invest_strategy_history']['loan_type'])) {
            $order['loan_type'] = $data['order']['invest_strategy_history']['loan_type'];
            unset($data['order']['invest_strategy_history']['loan_type']);
        }

        if (!empty($data['order']['invest_strategy_history']['loan_payment_status'])) {
            $order['loan_payment_status'] = $data['order']['invest_strategy_history']['loan_payment_status'];
            unset($data['order']['invest_strategy_history']['loan_payment_status']);
        }

        return array_merge($order, parent::getOrderConditions($data));
    }


}
