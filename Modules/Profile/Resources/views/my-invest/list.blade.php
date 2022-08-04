@extends('profile::layouts.app')

@section('title',  'My investments - ')

@section('style')
    <link href="{{ assets_version(asset('css/calendar.min.css')) }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/invest-styles.css') }}">
@endsection
@section('content')

    <div class="row">
        <div class="ui vertical segment features-container available-loans-container" id="my-investments-container">
            @if (session('success'))
                <div class="col-12">
                    <div class="p-1 my-4 text-green">{{session('success')}}</div>
                </div>
            @endif
            <h2 class="ui header text-center text-black mb-0 title-loans">My investments</h2>
            <h2 class="ui header center aligned text-black mt-0 livewire-update">(<span
                    id="totalLoansCountView">{{$investments->total()}}</span> / <span
                    id="totalLoansCountOnce">{{$totalInvestments}}</span>)</h2>

            <a class="ui basic button filters-toggle btn collapsed" data-toggle="collapse" href="#filters-collapse"
               role="button" aria-expanded="false" aria-controls="filters-collapse"><i class="fa fa-filter mr-2"></i>Filters</a>
            <div class="card mt-3 mb-3 filters-container hide-filters" id="filters-collapse">
                <form id="myInvestForm" class="card-body filter-cards-style"
                      action="{{ route('profile.invest') }}"
                      method="PUT">
                    @csrf

                    <div class="form-row w-100 overview-card">
                        <div class="col-lg-3">
                            @include('profile::my-invest.filter.interest-rate')
                        </div>
                        <div class="col-lg-3 pl-3">
                            @include('profile::my-invest.filter.interest-rate')
                        </div>
                        <div class="col-lg-3 pl-3">
                            @include('profile::my-invest.filter.investment-date')
                        </div>
                        <div class="col-lg-3 pl-3">
                            @include('profile::my-invest.filter.invested-amount')
                        </div>
                        <div class="col-lg-3 mt-3">
                            @include('profile::my-invest.filter.loan-status')
                        </div>
                        <div class="col-lg-6 mt-3 pl-3">
                            @include('profile::my-invest.filter.loan-payment-status')
                        </div>
                        <div class="col-lg-3 pt-3 pl-3">
                            @include('profile::my-invest.filter.loan-type')
                        </div>

                        <div class="col-lg-3 mt-3">
                            @include('profile::my-invest.filter.listed-for-sale')
                        </div>
                        <div class="col-lg-9 my-auto">
                            <input style="width: 144px;" type="submit"
                                   class="ui teal button btn-filter-submit float-right ml-3"
                                   value="{{__('common.Filter')}}">
                            <button style="width: 144px;" type="reset"
                                    class="ui basic button btn-filter-clear float-right"
                            >{{__('common.ClearAllFilters')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="minimalistic-table" id="table-myInvests">
                @include('profile::my-invest.list-table')
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/calendar.min.js')) }}"></script>
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script>
        let minAmountErrorAll = '{!! trans('common.AmountIsOutOfRangeError')!!}';
        let csrfToken = '{{ csrf_token() }}';
        let routeRefreshLoan = '{{ route('profile.myInvest.refresh')}}';
        let loanRepaid = '{{\Modules\Common\Entities\Loan::STATUS_REPAID}}';
        let myInvestmentRemoveUrl = '{{ route('profile.cart-secondary.delete', '') }}/';
        let myInvestmentMarketRemoveUrl = '{{ route('profile.cart-secondary.delete', '') }}/';
        let enterValidValue = '{!! trans('common.EnterValidValue')!!}';
        window.localStorage.setItem('marketType', 'sell');

    </script>
    <script type="text/javascript" src="{{ asset('js/myInvestment.js') }}"></script>
@endpush
