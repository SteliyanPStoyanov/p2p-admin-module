@extends('profile::layouts.app')

@section('title',  'Invest - ')

@section('style')
    <link href="{{ asset('css/calendar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/') }}/css/invest-styles.css">
    <style>
        #investAllFormHas {
            width: 2%;
            position: relative;
            float: right;
        }

        #investAllFormHas .tooltip-error-form {
            bottom: -10px;
        }

        .invest-button-form.ui.teal.button {
            position: relative;
        }

        .invest-form.single-invest-button.show {
            display: inline-block !important;
        }

        .invest-form.single-buy-button.show {
            display: inline-block !important;
        }

        .ui.teal.button.invest-all-button.hide-some-element, .remove-all-from-cart.remove-buy-cart.hide-some-element,
        .invest-form.single-invest-button.hide-some-element {
            display: none;
        }

        .inline-block {
            display: inline-block;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="ui vertical segment features-container available-loans-container w-100">
            <h2 class="ui header text-black mb-0 title-loans">{{__('common.AvailableLoans')}}</h2>
            <h2 class="ui header center aligned text-black mt-0 livewire-update">(<span
                    id="totalLoansCountView">{{$loans->total()}}</span> / <span
                    id="totalLoansCountOnce">{{$loans->total()}}</span>)</h2>
            <a class="ui basic button filters-toggle btn collapsed position-absolute" style="z-index: 9999"
               data-toggle="collapse" href="#filters-collapse"
               role="button" aria-expanded="false" aria-controls="filters-collapse"><i class="fa fa-filter mr-2"></i>Filters</a>
            <div>
                <a id="tab1" class="market-tabs">Primary Market</a>
                <a id="tab2" class="ml-4 market-tabs">Secondary Market</a>
            </div>
            <div>
                <div class="ui tab active" id="pills-primary-market" data-tab="pills-primary-market">
                </div>
                <div class="ui tab" id="pills-secondary-market" data-tab="pills-secondary-market">
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('js/calendar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jsGrid.js') }}"></script>
    <script>
        let csrfToken = '{{ csrf_token() }}';
        let minAmount = {{(int)\SettingFacade::getSettingValue(
                                Modules\Admin\Entities\Setting::MIN_INVESTMENT_AMOUNT_KEY
                            )}};

        market = window.localStorage.getItem('market');

        if (market === 'primaryMarket' || market === null) {
            primaryMarket();
        }

        if (market === 'secondaryMarket') {
            secondaryMarket();
        }

        $('#tab1').on('click', function () {
            primaryMarket();
            window.localStorage.setItem('market', 'primaryMarket');
            location.reload();

        });

        $('#tab2').on('click', function () {
            secondaryMarket();
            window.localStorage.setItem('market', 'secondaryMarket');
            location.reload();
        });

        function primaryMarket() {
            $.get('{{ route('profile.invest.list') }}')
                .done(function (data) {
                    $("#pills-secondary-market").html('');
                    $("#pills-primary-market").html(data);
                    $('#tab1').addClass('active');
                    $('#tab2').removeClass('active');
                    $('#totalLoansCountOnce').fadeOut(100, function () {
                        $(this).html($('#totalLoansCount').val()).fadeIn(100);
                    });
                })
                .fail(function () {
                });
            $.tab('change tab', 'pills-primary-market');
            jsAppend('primaryMarket');
        }

        function secondaryMarket() {
            let link = '{{ route('profile.market-secondary.list') }}';

            if (window.location.pathname == '/profile/invest/unsuccessful') {
                link = '{{ route('profile.market-secondary.list-unsuccessful') }}';
            }

            $.get(link)
                .done(function (data) {
                    $("#pills-primary-market").html('');
                    $("#pills-secondary-market").html(data);
                    $('#tab2').addClass('active');
                    $('#tab1').removeClass('active');
                    $('#totalLoansCountOnce').fadeOut(1, function () {
                        $(this).html($('#totalLoansCount').val()).fadeIn(20);
                    });
                    $('#totalLoansCountView').fadeOut(1, function () {
                        $(this).html($('#totalLoansCount').val()).fadeIn(20);
                    });

                    setTimeout(function () {
                        $('.invest-form.single-buy-button').hide();
                        $('.invest-form.single-buy-button.investment-isOnCard').show();
                    }, 20);
                })
                .fail(function () {
                });
            $.tab('change tab', 'pills-secondary-market');
            jsAppend('secondaryMarket');
        }

        function jsAppend(market) {
            let jsMarkets = document.createElement("script");

            if (market === 'primaryMarket') {

                enterValidValue = '{!! trans('common.EnterValidValue')!!}';
                minAmountErrorSingle = '{!! trans('common.MinAmountErrorSingle')!!}';
                uninvestedAmountIsLower = '{!! trans('common.UninvestedAmountIsLower')!!}';
                uninvestedWallet = '{{ amount(Auth::guard('investor')->user()->wallet()->uninvested)}}';
                investorHasBunchUrl = '{{route('profile.ajax.check.investorHasActiveBunch')}}';
                routeRefreshLoan = '{{ route('profile.invest.refresh')}}';
                investAllUrl = '{{ route('profile.invest.investAll')}}';

                $('#secondaryMarketJs').remove();
                jsMarkets.setAttribute("src", "{{ assets_version(asset('js/investPrimaryMarket.js')) }}");
                jsMarkets.setAttribute("id", "primaryMarketJs");
            }


            if (market === 'secondaryMarket') {
                $('#primaryMarketJs').remove();
                enterValidValue = '{!! trans('common.EnterValidValue')!!}';
                investorHasBunchUrl = '{{route('profile.ajax.check.investorHasActiveBunch')}}';
                myInvestmentRemoveUrl = '{{ route('profile.cart-secondary.delete', '') }}/';
                routeRefreshLoan = '{{ route('profile.market-secondary.refresh')}}';
                jsMarkets.setAttribute("src", "{{ assets_version(asset('js/investSecondaryMarket.js')) }}");
                jsMarkets.setAttribute("id", "secondaryMarketJs");
            }

            setTimeout(function () {
                document.body.appendChild(jsMarkets);
            }, 200);

        }

    </script>
@endpush





