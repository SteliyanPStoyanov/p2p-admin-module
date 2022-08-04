<link href="{{ asset('css/calendar.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url('/') }}/css/invest-styles.css">
<link rel="stylesheet" href="{{ url('/') }}/css/auto-invest-styles.css">
<div class="row">
    <div class="ui vertical segment features-container available-loans-container" id="my-investments-container"
         style="padding-top:3rem !important;">
        <h3 class="mb-3 text-black">{{__('common.YouAreAboutToSell')}}</h3>
        <p class="text-black">{{__('common.LearnMoreAboutHow')}}<a href="#toHowDoes">section</a>.</p>
        <div class="text-black">
                <span class="float-left"
                      style="width: 130px; display: inline-block;">{{__('common.ApplyDiscountPremium')}}</span>

            <i class="fa fa-info-circle float-left ml-1 mt-4 secondary-market-cart-tooltip"
               aria-hidden="true" data-toggle="tooltip" data-placement="top"
               data-original-title="{{__('common.ApplyDiscountPremiumTooltip')}}"></i>

            <form style="position: relative; width: 210px" class="float-left ml-3" action=""
                  onSubmit="return changeDiscount($(this));">

                <div class="input-group mb-3 float-left" style="width: 130px">
                    <input style="width: 64%;" data-prev-value="0.0" type="number" class="form-control text-center pr-1 interest-rate-field" step="0.1"
                           value="0.0"
                           >
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <button style="width: 80px;"
                        class="ui basic button btn-filter-clear float-left m-0 pl-0"
                >{{__('common.Apply')}}
                </button>
            </form>
        </div>
        <div class="col-12 mt-5 d-none" id="putOnSecondaryMarket">
            <h3 class="mb-3">{{__('common.YourInvestmentsOnTheSecondaryMarket')}}</h3>
        </div>
        <div class="minimalistic-table">
            <table class="ui table available-loans-table secondary-market-cart" id="myInvestmentsTable">
                <thead>
                @include('profile::secondary-cart.sorting.table-head')
                </thead>
                <tbody id="table-myInvests">
                @include('profile::secondary-cart.list-table')
                </tbody>
                <tfoot>

                </tfoot>
            </table>
            <div class="row" id="toHowDoes">
                <div class="col-lg-12">
                    <button
                        class="ui teal button btn-filter-submit float-left"
                        id="sellAll"
                        onClick="cartSell({{$cart->getCartId()}})"
                    >{{__('common.SellLoans')}}
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


