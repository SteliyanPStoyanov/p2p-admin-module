<div class="card mt-3 mb-3 filters-container hide-filters" id="filters-collapse">
    <form id="investForm" class="card-body filter-cards-style"
          action="{{ route('profile.invest') }}"
          method="PUT">
        @csrf
        <div class="form-row w-100 overview-card">
            <div class="row w-100">
                <div class="col">
                    @include('profile::invest.filter.interest-rate')
                </div>
                <div class="col pl-3">
                    @include('profile::secondary-market.filter.discount-premium')
                </div>
                <div class="col pl-3">
                    @include('profile::invest.filter.term')
                </div>
                <div class="col pl-3">
                    @include('profile::invest.filter.listing-date')
                </div>
                <div class="col pl-3">
                    @include('profile::secondary-market.filter.amount-available-for-investment')
                </div>
            </div>


            <div class="col-lg-3 mt-3">
                @include('profile::invest.filter.my-investments')
            </div>
            <div class="col-lg-6 mt-3 pl-3">
                @include('profile::invest.filter.loan-payment-status')
            </div>
            <div class="col-lg-3 pt-3 pl-3">
                @include('profile::invest.filter.loan-type')
            </div>
            <div class="col-lg-12 my-auto">
                <input style="width: 144px;" type="submit"
                       class="ui teal button btn-filter-submit float-right ml-3"
                       value="{{__('common.Filter')}}">
                <button type="reset" class="ui basic button btn-filter-clear float-right"
                >{{__('common.ClearAllFilters')}}
                </button>
            </div>
        </div>

    </form>
</div>

<div class="minimalistic-table" id="table-invests">
 @include('profile::secondary-market.list-table')
</div>

