<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Market;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Common\Entities\SecondaryMarket\Market\Entities\SecondaryInvestmentCollectionInterface;
use Modules\Common\Services\MarketSecondaryService;

class SecondaryMarketClient
{
    private MarketSecondaryService $marketSecondaryService;

    public function __construct(MarketSecondaryService $marketSecondaryService)
    {
        $this->marketSecondaryService = $marketSecondaryService;
    }

    public function pushing(SecondaryInvestmentCollectionInterface $secondaryInvestmentCollection): void
    {
        $this->marketSecondaryService->push($secondaryInvestmentCollection->asArray());
    }

    public function isInvestmentOnMarket(int $investmentId): bool
    {
        return $this->marketSecondaryService->isInvestmentOnMarket($investmentId);
    }

    public function pull(int $limit, int $investorIdToExclude = 0, array $data, array $marketSecondaryIdsInCart = []): LengthAwarePaginator
    {
        return $this->marketSecondaryService->pull($limit, $investorIdToExclude, $data, $marketSecondaryIdsInCart);
    }

    public function deleteByCartId(int $cartId): void
    {
        $this->marketSecondaryService->delete($cartId);
    }

    public function deleteLoan(int $id): void
    {
        $this->marketSecondaryService->delete($id);
    }

    public function deleteByCartLoanId(int $cartLoanId): void
    {
        $this->marketSecondaryService->deleteByCartLoanId($cartLoanId);
    }

    public function getBySecondaryLoanOnSale(int $secondaryLoanOnSale): Collection
    {
        return $this->marketSecondaryService->getBySecondaryLoanOnSale($secondaryLoanOnSale);
    }
}
