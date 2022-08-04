@extends('pages.layouts.app')

@section('title',  'Invest - ')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/calendar.min.css')) }}" >
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/invest-styles.css') }}">
@endsection

@section('content')
    <div class="ui vertical segment features-container available-loans-container invest-page-front-end max-width-set">
        <h2 class="ui header center aligned text-black title-loans" style="margin-bottom: 0; margin-top: 0">{{__('static.InvestPageAvailableLoansTitle')}}</h2>
        <h2 class="ui header center aligned text-black livewire-update" style="margin-bottom: 0; margin-top: 0">(<span id="totalLoansCountView">{{$loans->total()}}</span> / <span
                    id="totalLoansCountOnce">{{$loans->total()}}</span>)</h2>

            {{--form filters--}}
            <div class="card mt-5 mb-3">
                <form id="investForm" class="card-body filter-cards-style"
                      action="{{ route('invest') }}"
                      method="PUT">
                    @csrf
                    <div class="form-row w-100 overview-card">
                        <div class="col-lg-3 pl-3">
                            <div class="card">
                                <h5 class="card-header">{{__('common.InterestRate')}} %</h5>
                                <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
                                    {{__('common.Clear')}}
                                </a>
                                <div class="card-body">
                                    <input type="number" name="interest_rate_percent[from]"
                                           class="form-control w100 mb-3"
                                           placeholder="From" min="1"
                                           value=""
                                           step="1">
                                    <input type="number" name="interest_rate_percent[to]" class="form-control w100"
                                           value=""
                                           placeholder="To" min="1"
                                           step="1">
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-3 pl-3">
                            <div class="card">
                                <h5 class="card-header">{{__('common.Term')}} (months)</h5>
                                <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
                                    {{__('common.Clear')}}
                                </a>
                                <div class="card-body">
                                    <input type="number" name="period[from]" class="form-control w100 mb-3"
                                           placeholder="From" min="1" value=""
                                           step="1">
                                    <input type="number" name="period[to]" class="form-control w100"
                                           placeholder="To" min="1" value=""
                                           step="1">
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-3 pl-3">
                            <div class="card">
                                <h5 class="card-header">{{__('common.ListingDate')}}</h5>
                                <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
                                    {{__('common.Clear')}}
                                </a>
                                <div class="card-body">
                                    <div class="ui calendar mb-3" id="createdFromDatepicker">
                                        <div class="position-relative">
                                            <input type="text" name="created_at[from]"
                                                   class="form-control w100"
                                                   placeholder="From"
                                                   value=""
                                            >
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                    <div class="ui calendar mb-3" id="createdToDatepicker">
                                        <div class="position-relative">
                                            <input type="text" name="created_at[to]"
                                                   class="form-control w100"
                                                   placeholder="To"
                                                   value=""
                                            >
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-3 pl-3">
                            <div class="card">
                                <h5 class="card-header">Amount Available for investment</h5>
                                <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
                                    {{__('common.Clear')}}
                                </a>
                                <div class="card-body">
                                    <input type="number" name="amount_available[from]" class="form-control w100 mb-3"
                                           placeholder="From" min="1"
                                           value=""
                                           step="1">
                                    <input type="number" name="amount_available[to]" class="form-control w100"
                                           placeholder="To" min="1"
                                           value=""
                                           step="1">
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-6 mt-3 pl-3 pr-1">
                            <div class="card">
                                <h5 class="card-header">Loan Status</h5>
                                <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
                                    {{__('common.Clear')}}
                                </a>
                                @include('profile::invest.filter.range')

                            </div>
                        </div>
                        <div class="col-lg-3 pt-3 pl-3">
                            @php
                                $types = \Modules\Common\Entities\Loan::getTypesWithLabels();
                            @endphp

                            <div class="card">
                                <h5 class="card-header">{{__('common.LoanType')}}</h5>
                                <div class="card-body">
                                    <div class="form-group">
                                        <select class="form-control w-100" name="loan[type]">
                                            <option value="">{{__('common.AllLoanTypes')}}</option>
                                            @foreach($types as $type)
                                                <option value="{{loanType($type,true)}}">{{$type}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
                                    {{__('common.Clear')}}
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3 my-auto ml-auto">
                            <input type="submit" class="ui teal button btn-filter-submit float-right ml-3"
                                   value="{{__('static.InvestPageAvailableLoansFilterBtn')}}">
                            <button type="reset" class="ui basic button btn-filter-clear float-right"
                            >{{__('static.InvestPageAvailableLoansClearBtnAll')}}
                            </button>

                        </div>
                    </div>
                </form>
            </div>
            {{--end form filters--}}

            {{--data table--}}
            <div id="tableSt">
                <table class="ui table loans-table-desktop available-loans-table">
                    <thead>
                    <tr>
                        <th class="center aligned sorting
                        {{session($cacheKey . '.order.loan.country_id') ? 'active-sort' : ''}}">
                            <input type="text" name="order[loan][country_id]"
                                   value="{{session($cacheKey . '.order.loan.country_id') ?: 'desc'}}"
                            >
                            {!! trans('static.HomePageAvailableLoansTableCountry')!!}
                            <i class="fa {{session($cacheKey . '.order.loan.country_id') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.country_id') : 'fa-sort'}}"
                               aria-hidden="true"></i>
                        </th>
                        <th class="center aligned sorting
                        {{session($cacheKey . '.order.loan.loan_id') ? 'active-sort' : ''}}">
                            <input type="text" name="order[loan][loan_id]"
                                   value="{{session($cacheKey . '.order.loan.loan_id') ?: 'desc'}}"
                            >
                            {!! trans('static.HomePageAvailableLoansTableLoanID')!!}
                            <i class="fa {{session($cacheKey . '.order.loan.loan_id') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.loan_id') : 'fa-sort'}}"
                               aria-hidden="true"></i>
                        </th>
                        <th class="center aligned sorting
                        {{session($cacheKey . '.order.loan.lender_issue_date') ? 'active-sort' : ''}}">
                            <input type="text" name="order[loan][lender_issue_date]"
                                   value="{{session($cacheKey . '.order.loan.lender_issue_date') ?: 'desc'}}"
                            >
                            {!! trans('static.HomePageAvailableLoansTableIssueDate')!!}
                            <i class="fa {{session($cacheKey . '.order.loan.lender_issue_date') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.lender_issue_date') : 'fa-sort'}}"
                               aria-hidden="true"></i>
                        </th>
                        <th class="center aligned sorting
                        {{session($cacheKey . '.order.loan.type') ? 'active-sort' : ''}}">

                            <input type="text" name="order[loan][type]"
                                   value="{{session($cacheKey . '.order.loan.type') ?: 'desc'}}"
                            >
                            {!! trans('static.HomePageAvailableLoansTableLoanType')!!}
                            <i class="fa {{session($cacheKey . '.order.loan.type') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.type') : 'fa-sort'}}"
                               aria-hidden="true"></i>
                        </th>
                        <th class="center aligned sorting
                    {{session($cacheKey . '.order.loan.originator_id') ? 'active-sort' : ''}}">
                            <input type="text" name="order[loan][originator_id]"
                                   value="{{session($cacheKey . '.order.loan.originator_id') ?: 'desc'}}"
                            >
                            {!! trans('static.HomePageAvailableLoansTableLoanOriginator')!!}
                            <i class="fa {{session($cacheKey . '.order.loan.originator_id') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.originator_id') : 'fa-sort'}}"
                               aria-hidden="true"></i>
                        </th>
                        <th class="center aligned sorting
                        {{session($cacheKey . '.order.loan.interest_rate_percent') ? 'active-sort' : ''}}">
                            <input type="text" name="order[loan][interest_rate_percent]"
                                   value="{{session($cacheKey . '.order.loan.interest_rate_percent') ?: 'desc'}}"
                            >
                            {!! trans('static.HomePageAvailableLoansTableInterestRate')!!}
                            <i class="fa {{session($cacheKey . '.order.loan.interest_rate_percent') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.interest_rate_percent') : 'fa-sort'}}"
                               aria-hidden="true"></i>
                        </th>
                        <th class="center aligned sorting
                        {{session($cacheKey . '.order.loan.final_payment_date') ? 'active-sort' : ''}}">
                            <input type="text" name="order[loan][final_payment_date]"
                                   value="{{session($cacheKey . '.order.loan.final_payment_date') ?: 'desc'}}"
                            >
                            {!! trans('static.HomePageAvailableLoansTableTerm')!!}
                            <i class="fa {{session($cacheKey . '.order.loan.final_payment_date') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.final_payment_date') : 'fa-sort'}}"
                               aria-hidden="true"></i>
                        </th>
                        <th class="center aligned sorting
                        {{session($cacheKey . '.order.loan.amount') ? 'active-sort' : ''}}">
                            <input type="text" name="order[loan][amount]"
                                   value="{{session($cacheKey . '.order.loan.amount') ?: 'desc'}}"
                            >
                            {!! trans('static.HomePageAvailableLoansTableLoanAmount')!!}
                            <i class="fa {{session($cacheKey . '.order.loan.amount') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.amount') : 'fa-sort'}}"
                               aria-hidden="true"></i>
                        </th>
                        <th class="center aligned sorting
                        {{session($cacheKey . '.order.loan.amount_available') ? 'active-sort' : ''}}">
                            <input type="text" name="order[loan][amount_available]"
                                   value="{{session($cacheKey . '.order.loan.amount_available') ?: 'desc'}}"
                            >
                            {!! trans('static.HomePageAvailableLoansTableAvailable')!!}
                            <i class="fa {{session($cacheKey . '.order.loan.amount_available') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.amount_available') : 'fa-sort'}}"
                               aria-hidden="true"></i>
                        </th>
                        <th class="center aligned no-border"></th>
                    </tr>
                    </thead>
                    <tbody id="tableInvestBody">
                    @include('pages.invest.list-table')
                    </tbody>
                </table>
            </div>
            {{--end data table--}}
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/daterangepicker.min.js')) }}"></script>
    <script type="text/javascript" src="{{ assets_version(asset('js/dateRangePicker.js')) }}"></script>
    <script type="text/javascript" src="{{ assets_version(asset('js/calendar.min.js')) }}"></script>
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>

    <script nonce="investPage">
        loadSimpleDataGrid('{{ route('invest-refresh') }}', $("#investForm"), $("#tableInvestBody"));

        const calendarSettings = {
            type: 'date',
            monthFirst: false,
            formatter: {
                date: function (date, settings) {
                    let parsedDate = new Date(date).toLocaleDateString("en-GB");
                    let ourDate = parsedDate.replaceAll('/', '.');
                    return ourDate;
                }
            }
        };
        $('#createdFromDatepicker').calendar(calendarSettings);

        $('#createdToDatepicker').calendar(calendarSettings);

        function scrollToTopAnimation() {
            if (window.outerWidth < 425) {
                $("body, html").animate({scrollTop: $("#header-container").scrollTop()}, 300);
            }
        }

        $('.invest-form').hide();

        $('.btn-filter-clear').click(scrollToTopAnimation);
        $('.btn-filter-submit').click(scrollToTopAnimation);


        $(document).ajaxSuccess(function (event, xhr, settings) {

                if ($('#totalLoansCount').length != 0) {
                    if ($('#totalLoansCountView').text() !== $('#totalLoansCount').val()) {
                        $('#totalLoansCountView').fadeOut(100, function () {
                            $(this).html($('#totalLoansCount').val()).fadeIn(100);
                        });
                    } else {
                        $('#totalLoansCountView').html($('#totalLoansCount').val());
                    }
                }

                if (settings.type == 'GET') {
                    $('.invest-form').hide();
                }
            }
        );


        $(document).on('change', '#maxRows', function () {
            let routeRefreshLoan = '{{ route('invest-refresh')}}';
            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#investForm').serialize() + '&limit=' + this.value,

                success: function (data) {
                    $('#tableInvestBody').html(data);
                },
            });
        });
    </script>

@endpush

