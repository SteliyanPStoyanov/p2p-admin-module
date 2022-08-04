<?php
declare(strict_types=1);

namespace Modules\Common\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Common\Repositories\MarketSecondaryRepository;
use Modules\Core\Services\BaseService;

class MarketSecondaryService extends BaseService
{
    private MarketSecondaryRepository $marketSecondaryRepository;

    public function __construct(MarketSecondaryRepository $marketSecondaryRepository)
    {
        $this->marketSecondaryRepository = $marketSecondaryRepository;

        parent::__construct();
    }

    public function push(array $investments)
    {
        $this->marketSecondaryRepository->push($investments);
    }

    public function getManyByInvestmentId(int $investorId, array $investmentIds)
    {
        return $this->marketSecondaryRepository->getManyByInvestmentIds($investorId, $investmentIds);
    }

    public function isInvestmentOnMarket(int $investmentId): bool
    {
        return (bool)count($this->marketSecondaryRepository->isInvestmentOnMarket($investmentId));
    }

    public function pull(int $limit, int $investorIdToExclude = 0, array $data, array $marketSecondaryIdsInCart = []): LengthAwarePaginator
    {
        return $this->marketSecondaryRepository->pull($limit, $investorIdToExclude, $data, $marketSecondaryIdsInCart);
    }

    /**
     * @throws \Exception
     */
    public function delete(int $id): void
    {
         $this->marketSecondaryRepository->delete($id);
    }

    public function deleteByCartId(int $cartId): void
    {
        $this->marketSecondaryRepository->deleteByCartId($cartId);
    }

    public function deleteByCartLoanId(int $cartLoanId): void
    {
        $this->marketSecondaryRepository->deleteByCartLoanId($cartLoanId);
    }

    public function getBySecondaryLoanOnSale(int $secondaryLoanOnSale): Collection
    {
        return $this->marketSecondaryRepository->getBySecondaryLoanOnSale($secondaryLoanOnSale);
    }
}
