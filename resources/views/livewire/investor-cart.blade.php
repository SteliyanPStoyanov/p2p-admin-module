<div>
    <div id="cart-holder">
        @if($countBuy > 0)
            <h3 class="mt-3 mb-3 text-black text-center">
                Investments for purchase
            </h3>
            <div id="cart-count-total" class="mb-3">
                <span class="cart-count">{{$countBuy}}</span>
                <span class="cart-total">{{amount($sumBuy)}}</span>
            </div>
            <div class="mb-4" style="display: flex; justify-content: space-between;">
                <a wire:click.prevent="cancelAll('{{Modules\Common\Entities\CartSecondary::TYPE_BUYER}}')"
                   href="">Cancel</a>
                <a href="{{ route('profile.cart-secondary.cart').'#'. Modules\Common\Entities\CartSecondary::TYPE_BUYER }}">Confirm</a>
            </div>
        @endif
        @if($countSell > 0)
            <h3 class="mt-3 mb-3 text-black text-center">
                Investments for sale
            </h3>
            <div id="cart-count-total" class="mb-3">
                <span class="cart-count">{{$countSell}}</span>
                <span class="cart-total">{{amount($sumSell)}}</span>
            </div>
            <div class="mb-4" style="display: flex; justify-content: space-between;">
                <a wire:click.prevent="cancelAll('{{Modules\Common\Entities\CartSecondary::TYPE_SELLER}}')"
                   href="">Cancel</a>
                <a href="{{ route('profile.cart-secondary.cart').'#'. Modules\Common\Entities\CartSecondary::TYPE_SELLER}}">Confirm</a>
            </div>
        @endif
    </div>
</div>

