<?php

declare(strict_types=1);

namespace Modules\Common\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\MarketSecondary;
use Modules\Common\Services\SecondaryMarket\WherePipeline\WherePipeline;
use Modules\Core\Repositories\BaseRepository;

class MarketSecondaryRepository extends BaseRepository
{
    public function push(array $investments)
    {
        DB::table('market_secondary')->insert($investments);
    }

    public function getManyByInvestmentIds(int $investorId, array $investmentIds)
    {
        return MarketSecondary::whereIn('investment_id', $investmentIds)
            ->where('investor_id', $investorId)
            ->where('active', '1')
            ->where('deleted', '0')
            ->get();
    }

    public function isInvestmentOnMarket(int $investmentId): Collection
    {
        return MarketSecondary::where('investment_id', $investmentId)->get();
    }

    /**
     * @param int $limit
     * @param int $investorIdToExclude
     * @param array $whereData
     * @return LengthAwarePaginator
     */
    public function pull(
        int $limit,
        int $investorIdToExclude = 0,
        array $whereData,
        array $marketSecondaryIdsInCart = []
    ): LengthAwarePaginator {
        $marketSecondary = MarketSecondary::where(
            [
                'market_secondary.active' => 1,
                'market_secondary.deleted' => 0
            ]
        )->join(
            'loan',
            'loan.loan_id',
            '=',
            'market_secondary.loan_id',
        );

        if ($marketSecondaryIdsInCart) {
            $marketSecondary->whereIn('market_secondary_id', $marketSecondaryIdsInCart);
        }

        $order = [];
        if (empty($order) && !empty($whereData['order'])) {
            $order = $this->getOrderConditions($whereData);
            unset($whereData['order']);
        } else {
            $order = [
                'market_secondary.active' => 'DESC',
                'market_secondary.loan_id' => 'DESC'
            ];
        }

        if (!empty($order)) {
            foreach ($order as $key => $direction) {
                $marketSecondary->orderBy($key, $direction);
            }
        }

        if ($whereData) {
            $wrapper = WherePipeline::run(
                $marketSecondary,
                $whereData,
                $investorIdToExclude
            );

            $marketSecondary = $wrapper->getMarketSecondary();
        }

        if ($investorIdToExclude) {
            $marketSecondary->where('investor_id', '<>', $investorIdToExclude);
        }

        return $marketSecondary->paginate($limit);
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        MarketSecondary::where('market_secondary_id', $id)->delete();
    }

    /**
     * @param int $cartLoanId
     * @throws \Exception
     */
    public function deleteByCartLoanId(int $cartLoanId): void
    {
        // MarketSecondary::where('secondary_loan_on_sale', $cartLoanId)->delete() Doesn't update deleted field
        $marketSecondaryEntries = MarketSecondary::where('secondary_loan_on_sale', $cartLoanId)->get();

        foreach ($marketSecondaryEntries as $marketSecondaryEntry) {
            $marketSecondaryEntry->delete();
        }
    }

    public function deleteByCartId(int $cartId): void
    {
        // MarketSecondary::where('secondary_loan_on_sale', $cartLoanId)->delete() Doesn't update deleted field
        $loans = CartSecondaryLoans::where('cart_secondary_id', $cartId)->get();

        $loanIds = [];
        foreach ($loans as $loan) {
            $loanIds[] = $loan->cart_loan_id;
        }

        if ($loanIds) {
            $marketSecondaryEntries = MarketSecondary::whereIn('secondary_loan_on_sale', $loanIds)->get();

            foreach ($marketSecondaryEntries as $marketSecondaryEntry) {
                $marketSecondaryEntry->delete();
            }
        }
    }

    public function getBySecondaryLoanOnSale(int $secondaryLoanOnSale)
    {
        return MarketSecondary::where([
            'secondary_loan_on_sale' => $secondaryLoanOnSale
        ])->get();
    }
}
