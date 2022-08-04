<link href="{{ asset('css/calendar.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url('/') }}/css/invest-styles.css">
<link rel="stylesheet" href="{{ url('/') }}/css/auto-invest-styles.css">
<div class="row">
    <div class="ui vertical segment features-container available-loans-container" id="my-investments-container"
         style="padding-top:3rem !important;">
        <h3 class="mb-3 text-black">{{__('common.WeHaveAddedYourSelection')}}</h3>
        <p class="text-black">{{__('common.ReviewCarefullyConfirm')}}</p>
        <div class="minimalistic-table">
            <table class="ui table available-loans-table secondary-market-cart" id="myInvestmentsTable">
                <thead>
                @include('profile::secondary-cart.sorting.table-head-buy')
                </thead>
                <tbody id="table-myInvests">
                @include('profile::secondary-cart.list-table-buy')
                </tbody>
                <tfoot>

                </tfoot>
            </table>
            <div class="row" id="toHowDoes">
                <div class="col-lg-12">
                    <button
                        class="ui teal button btn-filter-submit float-left"
                        id="sellAll"
                        onClick="cartBuy({{$cart->getCartId()}})"
                    >{{__('common.Invest')}}
                    </button>
                    <a href="{{route('profile.cart-secondary.deleteAll' , $cart->getCartId())}}"
                       style="width: 100%; max-width: 200px;"
                       class="ui basic button btn-filter-clear float-left"
                    >{{__('common.CancelAndDelete')}}
                    </a>
                </div>
            </div>
        </div>
        <div class="row auto-invest mt-5">
            @include('profile::secondary-cart.accordion')
        </div>
    </div>
</div>
