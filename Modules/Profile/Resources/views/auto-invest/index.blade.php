@extends('profile::layouts.app')

@section('title',  'Auto invest - ')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/auto-invest-styles.css') }}">

@endsection
@section('content')
    <div class="row auto-invest pl-1" id="autoinvest-strategy">
        <div class="col-lg-12">
            <h2 class="mt-5 mb-5 text-left text-black">{{__('common.AutoInvestStrategies')}}</h2>
        </div>
        <div class="col-lg-6 pb-4 px-0">

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="description mb-3">
                        <p class="text-black">
                            {{__('common.ActivateTheAutoInvest')}}
                        </p>
                        <p class="text-black">
                            {{__('common.AfrangaHasMadeInvestingEven')}}
                        </p>
                    </div>
                    <a href="{{route('profile.autoInvest.create')}}"
                       class="ui teal button btn float-left mr-3 ">{{__('common.CreateStrategy')}}</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6 pb-4 px-0 pl-5 " id="myTabContent">
            <h3 class="card-header text-black no-border no-box bg-transparent p-0 m-0">
                {{__('common.UsefulInformation')}}
            </h3>
            <div class="tab-pane fade show active" id="GettingStarted" role="tabpanel"
                 aria-labelledby="GettingStarted-tab">
                <div class="accordion">
                    @include('profile::auto-invest.accordion')
                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-5">
            @if (session('success'))
                <div class="col-12">
                    <div class="p-1 my-4 text-green">{{session('success')}}</div>
                </div>
            @endif

            <h2 class="text-left text-black">{{__('common.AutoInvestStrategiesTable')}}</h2>
            @if (count($investStrategies) > 0)
                <div class="minimalistic-table" id="table-invests">
                    <table class="ui table available-loans-table" id="investTable">
                        <thead>
                        <tr>
                            <th scope="col" class="center aligned">
                                {{__('common.Priority')}}
                            </th>
                            <th scope="col" class="center aligned four wide">
                                {{__('common.StrategyName')}}
                            </th>
                            <th scope="col" class="center aligned two wide">
                                {!! trans('common.InterestRateAI')!!}
                            </th>
                            <th scope="col" class="center aligned two wide">
                                {!! trans('common.RemainingLoanTerm')!!}
                            </th>
                            <th scope="col" class="center aligned two wide">
                                {!! trans('common.InvestmentInOneLoan') !!}
                            </th>
                            <th scope="col" class="center aligned one wide">
                                {!! trans('common.NumberOfInvestments') !!}
                            </th>
                            <th scope="col" class="center aligned two wide">
                                {!! trans('common.TargetPortfolioSize')!!}
                            </th>
                            <th scope="col" class="center aligned one wide">
                                {!! trans('common.InvestedAmount')!!}
                            </th>
                            <th scope="col" class="center aligned action-btn">

                            </th>
                            <th scope="col" class="center aligned action-btn">

                            </th>
                            <th scope="col" class="center aligned action-btn">

                            </th>
                            <th scope="col" class="center aligned action-btn">

                            </th>
                        </tr>
                        </thead>
                        <tbody id="investsTable">
                        @include('profile::auto-invest.list-table')
                        </tbody>

                    </table>
                </div>
            @else
                <p class="text-black">{!! trans('common.NoInvestStrategy')!!}</p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/calendar.min.js')) }}"></script>
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script>

        loadSimpleDataGrid(
            '{{ route('profile.autoInvest.refresh') }}',
            $(".investForm"),
            $("#investsTable"),
            false,
            0,
            false
        );

        $(document).on('change', '#maxRows', function () {
            reload();
        });

        function reload() {
            let routeRefreshLoan = '{{ route('profile.autoInvest.refresh')}}';
            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#investForm').serialize(),
                success: function (data) {
                    $('#investsTable').html(data);
                },
            });
        }

        function activateDeactivateStrategy(elem) {
            $.ajax({
                type: 'get',
                url: elem.getAttribute('href'),
                success: function (response) {
                    $("#investsTable").html('').append(response);
                    $('td.readOnly').css({"pointer-events": "none", "cursor": "not-allowed", "opacity": "0.60"});
                },

                error: function (jqXHR) {
                    let messages = jqXHR.responseJSON.message;
                    $(elem).parent().append('<div class="tooltip-error-form">' + messages + '</div>');
                    setTimeout(function () {
                        $('.tooltip-error-form').remove();
                    }, 2000);
                }
            });
            return false;
        }

        function priority(elem, direction, priority, strategyId) {
            $.ajax({
                type: 'get',
                data: {direction, priority, strategyId},
                url: '{{ route('profile.priority.change')}}',
                success: function (data) {
                    if (data.success === true) {
                        elem.append('<div class="tooltip-success-form">' + data.message + '</div>');
                        setTimeout(function () {
                            loadSimpleDataGrid(
                                '{{ route('profile.autoInvest.refresh') }}',
                                $(".investForm"),
                                $("#investsTable"),
                                false,
                                0,
                                false,
                                false,
                                true
                            );
                            $('.tooltip-success-form').remove();
                        }, 200);
                    } else {
                        elem.append('<div class="tooltip-error-form">' + data.message + '</div>');
                        setTimeout(function () {
                            $('.tooltip-error-form').remove();
                        }, 2000);
                    }
                },
            });
            return false;
        }

    </script>
@endpush

