<?php
declare(strict_types=1);

namespace Modules\Common\Services;

use Illuminate\Support\Collection;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\CartSecondaryLoans;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoanInterface;
use Modules\Common\Repositories\CartSecondaryLoansRepository;
use Modules\Core\Database\Collections\CustomEloquentCollection;
use Modules\Core\Services\BaseService;

class CartSecondaryLoansService extends BaseService
{
    protected CartSecondaryLoansRepository $cartSecondaryLoansRepository;

    public function __construct(CartSecondaryLoansRepository $cartSecondaryLoansRepository)
    {
        $this->cartSecondaryLoansRepository = $cartSecondaryLoansRepository;

        parent::__construct();
    }

    /**
     * @param array $loan
     * @return CartSecondaryLoans
     */
    public function create(array $loan): CartSecondaryLoans
    {
        return $this->cartSecondaryLoansRepository->create($loan);
    }

    /**
     * @param array $loan
     * @return CartSecondaryLoans
     */
    public function update(array $loan): CartSecondaryLoans
    {
        return $this->cartSecondaryLoansRepository->update($loan);
    }

    /**
     * @param int $cart_id
     * @return CartSecondaryLoans
     */
    public function get(int $cart_id): CartSecondaryLoans
    {
        return $this->cartSecondaryLoansRepository->get($cart_id);
    }

    /**
     * @param int $id
     * @return CartSecondaryLoans
     */
    public function getLoansByInvestorId(int $id, $type = CartSecondary::TYPE_SELLER, int $status = CartSecondaryLoans::LOAN_STATUS_OKAY): CustomEloquentCollection
    {
        return $this->cartSecondaryLoansRepository->getLoansByInvestorId($id, $type, $status);
    }

    /**
     * @param CartLoanInterface $loan
     * @return bool
     */
    public function isAlreadyInCart(CartLoanInterface $loan): bool
    {
        return $this->cartSecondaryLoansRepository->isAlreadyInCart($loan);
    }

    /**
     * @param CartLoanInterface $loan
     * @return int
     */
    public function findId(CartLoanInterface $loan): int
    {
        return $this->cartSecondaryLoansRepository->findId($loan);
    }

    /**
     * @param string $table
     * @param string $field
     * @param string $order
     * @param int $investorId
     * @param string $type
     * @return Collection
     */
    public function getOrdered(string $table, string $field, string $order, int $investorId, string $type, int $status): Collection
    {
        return $this->cartSecondaryLoansRepository->getOrdered($table, $field, $order, $investorId, $type, $status);
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $this->cartSecondaryLoansRepository->delete($id);
    }

    public function deleteBySecondaryMarketId(int $secondaryMarketId): void
    {
        $this->cartSecondaryLoansRepository->deleteBySecondaryMarketId($secondaryMarketId);
    }

    public function deleteManyBySecondaryMarketIds(array $secondaryMarketsId): void
    {
        $this->cartSecondaryLoansRepository->deleteManyBySecondaryMarketIds($secondaryMarketsId);
    }

    /**
     * @param $cart_id
     */
    public function deleteCart($cart_id): void
    {
        $this->cartSecondaryLoansRepository->deleteCart($cart_id);
    }

    public function changeCartStatus(int $cartId, int $status, string $reason = null): void
    {
        $this->cartSecondaryLoansRepository->changeCartStatus($cartId, $status, $reason);
    }

    public function changeStatus(int $cartLoanId, int $status, string $reason = null): void
    {
        $this->cartSecondaryLoansRepository->changeStatus($cartLoanId, $status, $reason);
    }

    public function getManyByInvestmentId(int $investorId, array $investmentIds, string $type = CartSecondary::TYPE_SELLER, int $status = CartSecondaryLoans::LOAN_STATUS_OKAY)
    {
        return $this->cartSecondaryLoansRepository->getManyByInvestmentIds($investorId, $investmentIds, $type, $status);
    }

    public function getManyByCartLoanId(array $cartLoanIds, $status = 1)
    {
        return $this->cartSecondaryLoansRepository->getManyByCartLoanIds($cartLoanIds, $status);
    }

    public function getManyBySecondaryMarketIds(array $secondaryMarketIds, $status = CartSecondaryLoans::LOAN_STATUS_OKAY)
    {
        return $this->cartSecondaryLoansRepository->getManyBySecondaryMarketIds($secondaryMarketIds, $status);
    }

    public function isInvestmentInCart(int $investmentId): bool
    {
        return (bool)count($this->cartSecondaryLoansRepository->isInvestmentInCart($investmentId));
    }
}
