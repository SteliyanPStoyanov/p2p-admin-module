<?php
declare(strict_types=1);

namespace Modules\Common\Entities\SecondaryMarket\Cart;

// In case we gonna need pagination
// https://laracasts.com/discuss/channels/laravel/how-to-paginate-laravel-collection
use Illuminate\Support\Collection;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\Investor;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\Cart;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartInterface;
use Modules\Common\Entities\SecondaryMarket\Cart\Entities\CartLoansCollection;


class CartBuilder
{
    public function new(Investor $investor, string $type, CartLoansCollection $loans): CartInterface
    {
        return Cart::new(
            $investor,
            $type,
            $loans
        );
    }

    public function createBlank(CartSecondary $cartSecondary): CartInterface
    {
        return Cart::create(
            $cartSecondary->cart_secondary_id,
            $cartSecondary->investor,
            $cartSecondary->type,
            new CartLoansCollection()
        );
    }

    public function create(CartSecondary $cartSecondary, CartLoansCollection $cartLoansCollection): CartInterface
    {
        return Cart::create(
            $cartSecondary->cart_secondary_id,
            $cartSecondary->investor,
            $cartSecondary->type,
            $cartLoansCollection
        );
    }

    public function collection(): Collection
    {

    }
}
