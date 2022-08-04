<?php

namespace App\Http\Livewire;

use Auth;
use Livewire\Component;
use Modules\Common\Entities\CartSecondary;
use Modules\Common\Entities\SecondaryMarket\Cart\CartClient;

class InvestorCart extends Component
{
    protected $listeners = ['loanAdd' => '$refresh'];
    public float $sumSell = 0;
    public int $countSell;
    public float $sumBuy = 0;
    public int $countBuy;
    public int $investorId;


    public function mount($investorId)
    {
        $this->investorId = $investorId;
    }

    public function render(CartClient $cartClient)
    {
        if (
        $cartClient->isInvestorHasCart(
            $this->investorId,
            CartSecondary::TYPE_SELLER
        )
        ) {
            $cartSeller = $cartClient->getByInvestorId($this->investorId, CartSecondary::TYPE_SELLER);
            $this->sumSell = $this->calculateTotal($cartSeller->getLoans()->get());
            $this->countSell = $cartSeller->getLoans()->count();
        }


        if (
        $cartClient->isInvestorHasCart(
            $this->investorId,
            CartSecondary::TYPE_BUYER
        )
        ) {
            $cartBuyer = $cartClient->getByInvestorId($this->investorId, CartSecondary::TYPE_BUYER);
            $this->sumBuy = $this->calculateTotal($cartBuyer->getLoans()->get());
            $this->countBuy = $cartBuyer->getLoans()->count();
        }

        return view(
            'livewire.investor-cart'
        );
    }

    /**
     * @param $loans
     * @return float
     */
    public function calculateTotal($loans): float
    {
        $calcSum = 0;
        foreach ($loans->all() as $loan) {
            $calcSum += $loan->getPrice();
        }

        return $calcSum;
    }

    /**
     * @param $sellerType
     * @param CartClient $cartClient
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelAll($sellerType ,CartClient $cartClient)
    {

        $cart = $cartClient->getByInvestorId($this->investorId, $sellerType);
        $cartClient->deleteCart($cart->getCartId());

        if ($sellerType == CartSecondary::TYPE_BUYER) {
            return redirect()->route('profile.invest');
        }

        return redirect()->route('profile.myInvest');
    }
}
