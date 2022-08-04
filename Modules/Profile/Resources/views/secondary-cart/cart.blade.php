@extends('profile::layouts.app')

@section('title',  'Cart - ')

@section('content')

    <link href="{{ asset('css/calendar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('/') }}/css/invest-styles.css">
    <link rel="stylesheet" href="{{ url('/') }}/css/auto-invest-styles.css">
    <div class="row">
        <div class="ui vertical segment features-container available-loans-container w-100">
            <div>
                <a id="tab1" class="market-tabs">{{__('common.Invest')}}</a>
                <a id="tab2" class="ml-4 market-tabs">{{__('common.Sell')}}</a>
            </div>
            <div>
                <div class="ui tab active" id="pills-cart-buy" data-tab="pills-cart-buy">
                </div>
                <div class="ui tab " id="pills-cart-sell" data-tab="pills-cart-sell">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/calendar.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jsGrid.js') }}"></script>
    <script>

        let url = document.location.toString();
        if (url.match('#')) {

            if (url.split('#')[1] === 'sell') {
                sellCart();
            }
            if (url.split('#')[1] === 'buy') {
                buyCart();
            }
        } else {
            buyCart();
        }

        let calendarSettings = {
            type: 'date',
            monthFirst: false,
            formatter: {
                date: function (date, settings) {
                    let parsedDate = new Date(date).toLocaleDateString("en-GB");
                    return parsedDate.replaceAll('/', '.');
                }
            }
        };

        premiumLimitError = '{{__('common.DiscountOutOfRangeError')}}';
        principalForSaleRangeError = '{{__('common.PrincipalForSaleRangeError')}}';
        principalForSaleRangeErrorMin = '{{__('common.PrincipalForSaleRangeErrorMin')}}';
        principalForSalePleaseEnter = '{{__('common.PrincipalForSalePleaseEnter')}}';
        premiumDecimalError = '{{__('common.PleaseEnterValueFirstDigit')}}';
        premiumLimit = '{{ (int)\SettingFacade::getSettingValue(Modules\Admin\Entities\Setting::PREMIUM_LIMIT_VALUE_KEY)}}';


        $('#tab1').on('click', function () {
            buyCart();
        });

        $('#tab2').on('click', function () {
            sellCart();
        });

        function sellCart() {
            $.get('{{ route('profile.cart-secondary.list') }}')
                .done(function (data) {
                    $("#pills-cart-buy").html('');
                    $("#pills-cart-sell").html(data);

                    $('#tab2').addClass('active');
                    $('#tab1').removeClass('active');
                    $('#totalLoansCountOnce').fadeOut(100, function () {
                        $(this).html($('#totalLoansCount').val()).fadeIn(100);
                    });

                    $(function () {
                        window.setTimeout(function () {
                            $('[data-toggle="tooltip"]').tooltip({container: '.pusher', placement: 'top'});
                        }, 200);

                    });
                })
                .fail(function (data) {
                    $("#pills-cart-buy").html('');
                    $("#pills-cart-sell").html('<div class="p-1 my-4 bg-danger text-left">' + data.responseJSON.message + '</div>');
                    $('#tab2').addClass('active');
                    $('#tab1').removeClass('active');
                });
            $.tab('change tab', 'pills-cart-sell');
            loadAdditionalJs('sell');
        }

        function buyCart() {
            $.get('{{ route('profile.cart-secondary.list-buy') }}')
                .done(function (data) {
                    $("#pills-cart-sell").html('');
                    $("#pills-cart-buy").html(data);
                    $('#tab1').addClass('active');
                    $('#tab2').removeClass('active');
                    $('#totalLoansCountOnce').fadeOut(100, function () {
                        $(this).html($('#totalLoansCount').val()).fadeIn(100);
                    });

                    $(function () {
                        window.setTimeout(function () {
                            $('[data-toggle="tooltip"]').tooltip({container: '.pusher', placement: 'top'});
                        }, 200);

                    });
                })
                .fail(function (data) {
                    $("#pills-cart-sell").html('');
                    $("#pills-cart-buy").html('<div class="p-1 my-4 bg-danger text-left">' + data.responseJSON.message + '</div>');
                    $('#tab1').addClass('active');
                    $('#tab2').removeClass('active');
                });
            $.tab('change tab', 'pills-cart-buy');
            loadAdditionalJs('buy');
        }

        function loadAdditionalJs(marketType) {
            if (marketType === 'sell') {
                csrfToken = '{{ csrf_token() }}';
                routeRefreshLoan = '{{ route('profile.cart-secondary.refresh')}}';
                cartSaveUrl = '{{ route('profile.cart-secondary.submit', '') }}';
                cartDeleteUrl = '{{ route('profile.cart-secondary.delete', '') }}/';
                minAmountErrorAll = '{!! trans('common.AmountIsOutOfRangeError')!!}';

            }

            if (marketType === 'buy') {
                csrfToken = '{{ csrf_token() }}';
                routeRefreshLoan = '{{ route('profile.cart-secondary.refresh-buy')}}';
                cartDeleteUrl = '{{ route('profile.cart-secondary.delete-buyers-loan', '') }}/';
                minAmountErrorAll = '{!! trans('common.AmountIsOutOfRangeError')!!}';
                buyAllUrl = '{{ route('profile.cart-secondary.buy', '') }}';
            }
        }

    </script>
    <script type="text/javascript" src="{{ asset('js/cartSecondary.js') }}"></script>
@endpush
