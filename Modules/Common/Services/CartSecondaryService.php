<?php
declare(strict_types=1);

namespace Modules\Common\Services;

use Modules\Common\Entities\CartSecondary;
use Modules\Common\Repositories\CartSecondaryRepository;
use Modules\Core\Services\BaseService;

class CartSecondaryService extends BaseService
{
    protected CartSecondaryRepository $cartSecondaryRepository;

    public function __construct(CartSecondaryRepository $cartSecondaryRepository)
    {
        $this->cartSecondaryRepository = $cartSecondaryRepository;

        parent::__construct();
    }

    /**
     * @param array $cart
     * @return CartSecondary
     */
    public function create(array $cart): CartSecondary
    {
        return $this->cartSecondaryRepository->create($cart);
    }

    /**
     * @param array $cart
     * @return void
     */
    public function update(array $cart): void
    {
        $this->cartSecondaryRepository->update($cart);
    }

    /**
     * @param int $investor_id
     * @param string $type
     * @return bool
     */
    public function isInvestorHasCart(int $investor_id, string $type): bool
    {
        return $this->cartSecondaryRepository->isInvestorHasCart($investor_id, $type);
    }

    /**
     * @param int $id
     * @return CartSecondary
     */
    public function get(int $id): CartSecondary
    {
        return $this->cartSecondaryRepository->get($id);
    }

    /**
     * @param int $id
     * @param string $type
     * @return CartSecondary
     */
    public function getByInvestorId(int $id, string $type, int $status = -1): CartSecondary
    {
        return $this->cartSecondaryRepository->getByInvestorId($id, $type, $status);
    }

    public function countInvested(int $cartId): int
    {
        return $this->cartSecondaryRepository->countInvested($cartId);
    }

    public function countProblematicInvestments(int $cartId): int
    {
        return $this->cartSecondaryRepository->countProblematicInvestments($cartId);
    }

    public function amountInvested(int $cartId): float
    {
        return $this->cartSecondaryRepository->amountInvested($cartId);
    }
}
