@extends('profile::layouts.app')

@section('title',  'Cart - ')
@section('style')
    <style>
        .loader {
            border: 8px solid #f3f3f3;
            border-radius: 50%;
            border-top: 8px solid #3498db;
            width: 40px;
            height: 40px;
            -webkit-animation: spin 2s linear infinite; /* Safari */
            animation: spin 2s linear infinite;
            float: left;
            margin-right: 20px;
        }

        #Investing {
            line-height: 40px;
        }


        /* Safari */
        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection
@section('content')

    <div class="row">

        <div class="col-lg-12 trans-details text-black">
            <h3 id="Successful" class="mt-5 mb-5 text-black pl-0">{{__('common.GreatYourInvestmentWasSuccessful')}}</h3>
            <h3 id="Investing" class="mt-5 mb-5 text-black pl-0" style="position: relative;">
                <div class="loader"></div>
                You are investing now .......
            </h3>
            <div id="buy-box">
                @include('profile::secondary-cart.buy-box')
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('js/calendar.min.js') }}"></script>
    <script>
        investorHasBunchUrl = '{{route('profile.ajax.check.investorHasActiveBunch')}}';
        buyAllSuccessRefresh = '{{route('profile.cart-secondary.buyAllSuccessRefresh')}}';

        setTimeout(function () {
            disabledBunchActive();

        }, 3000);

        $('#Investing').show();
        $('#Successful').hide();

        function disabledBunchActive() {
            $.ajax({
                url: investorHasBunchUrl,
                type: 'get',
                success: function (data) {
                    if (data.success === false) {
                        $('#Investing').show();
                        $('#Successful').hide();
                        disabledBunchActive();
                        reloadInfo();
                        Livewire.emit('loanAdd');
                    } else {
                        $('#Investing').hide();
                        $('#Successful').show();
                        reloadInfo();
                        Livewire.emit('loanAdd');
                        return false;
                    }

                }
            });
        }

        function reloadInfo() {
            $.ajax({
                type: 'get',
                url: buyAllSuccessRefresh,
                data: {"_token": "{{ csrf_token() }}"},
                success: function (data) {
                    $('#buy-box').html(data);
                },
            });
        }

        window.setTimeout(function () {

            Livewire.emit('loanAdd');
        }, 100);
    </script>
@endpush
