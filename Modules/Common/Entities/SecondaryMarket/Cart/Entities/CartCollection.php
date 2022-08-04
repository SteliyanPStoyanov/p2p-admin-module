<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart\Entities;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class CartCollection implements CartCollectionInterface
{
    private Collection $carts;

    public function __construct()
    {
        $this->carts = collect([]);
    }

    public function add(CartInterface $cart): void
    {
        $this->checkCartId($cart);

        $this->carts->put(
            $cart->getCartId(), $cart
        );
    }

    public function get(): Collection
    {
        return $this->carts;
    }

    public function delete(int $id): void
    {
        $this->carts->forget($id);
    }

    private function checkCartId(CartInterface $cart): void
    {
        if (!is_int($cart->getCartId()) || $cart->getCartId() <= 0)
        {
            throw new InvalidArgumentException("Cart has to be saved first");
        }
    }
}
