@extends('profile::layouts.app')

@section('title',  'Account statement - ')

@section('style')
    <link rel="stylesheet"
          href="{{ assets_version(url('/') . '/css/bootstrap-select.css') }}">
    <link href="{{ assets_version(asset('css/calendar.min.css')) }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/account-statement.css') }}">

@endsection
@section('content')

    <div class="col-lg-12 trans-details text-black">
        <h2 class="mt-5 mb-5 text-black pl-0">{{__('common.AccountStatement')}}</h2>
        <form id="accountStatement" class="row"
              action="{{ route('profile.profile.accountStatement') }}"
              method="PUT">
            @csrf
            <div class="col-lg-2">
                <div class="card">
                    <h5 class="card-header pl-0">{{__('common.StartDate')}}</h5>
                    <div class="card-body">
                        <div class="ui calendar" id="createdFromDatepicker">
                            <div class="position-relative">
                                <input type="text" name="createdAt[from]"
                                       class="form-control w100"
                                       placeholder="From" min="1"
                                       value="{{\Carbon\Carbon::now()->format('d-m-Y')}}"
                                       step="1">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 pl-3">
                <div class="card">
                    <h5 class="card-header pl-0">{{__('common.EndDate')}}</h5>
                    <div class="card-body">
                        <div class="ui calendar" id="createdToDatepicker">
                            <div class="position-relative">
                                <input type="text" name="createdAt[to]"
                                       class="form-control w100"
                                       placeholder="From" min="1"
                                       value="{{\Carbon\Carbon::now()->format('d-m-Y')}}"
                                       step="1">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 pl-3" id="statement-transaction-type">
                <div class="card">
                    <h5 class="card-header pl-0">{{__('common.TransactionType')}}</h5>
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <select
                              id="selectpickerAC"
                              name="type[]"
                              title="{{__('common.TransactionType')}}"
                              multiple data-actions-box="true"
                            >
                              @foreach($types as $typeKey => $type)
                                <option value="{{$typeKey}}">{{$type}}</option>
                              @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 pl-3 d-flex align-items-end">
                <input type="submit"
                       class="ui teal w-100 button btn-filter-submit float-right ml-3"
                       value="{{__('common.ShowResults')}}">

            </div>
        </form>
        <ul class="nav mt-4">
            <li class="nav-item">
                <a class="nav-link pl-0"
                   onclick="staticFillter(
                       '{{showDate(Carbon\Carbon::now())}}',
                       '{{showDate(Carbon\Carbon::now())}}');"
                   href="#">Today</a>
            </li>
            <li class="nav-item">
                <a class="nav-link "
                   onclick="staticFillter(
                       '{{showDate(Carbon\Carbon::now()->subDay(1))}}',
                       '{{showDate(Carbon\Carbon::now()->subDay(1))}}');"
                   href="#">Yesterday</a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   onclick="staticFillter(
                       '{{showDate(Carbon\Carbon::now()->startOfWeek())}}',
                       '{{showDate(Carbon\Carbon::now())}}');"
                   href="#">Current Week</a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                   onclick="staticFillter(
                       '{{showDate(Carbon\Carbon::now()->firstOfMonth())}}',
                       '{{showDate(Carbon\Carbon::now())}}');"
                   href="#">Current Month</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   onclick="staticFillter(
                       '{{showDate(Carbon\Carbon::now()->subWeek()->startOfWeek())}}',
                       '{{showDate(Carbon\Carbon::now()->subWeek()->endOfWeek())}}');"
                   href="#">Last Week</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   onclick="staticFillter(
                       '{{showDate(Carbon\Carbon::now()->subMonth()->firstOfMonth())}}',
                       '{{showDate(Carbon\Carbon::now()->subMonth()->lastOfMonth())}}');"
                   href="#">Last Month</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   onclick="staticFillter(
                       '{{showDate(Carbon\Carbon::now()->subMonth()->firstOfMonth())}}',
                       '{{showDate(Carbon\Carbon::now())}}');"
                   href="#">This Month and Last Month</a>
            </li>
        </ul>

        <div id="transactionData">
            @include('profile::profile.list-table')
        </div>

    </div>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/calendar.min.js')) }}"></script>
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script type="text/javascript" src="{{ assets_version(asset('js/bootstrap-select.js')) }}"></script>
    <script>
        let accCountClicks = 0;
         accountStatementUrl =  '{{ route('profile.accountStatement.export')}}';
        $(document).ready(function () {
            $('#selectpickerAC').selectpicker();
        });


        loadSimpleDataGrid('{{ route('profile.transaction.refresh') }}', $("#accountStatement"), $("#transactionData"), false, 0, false);

        const calendarSettings = {
            type: 'date',
            initialDate: new Date(),
            maxDate: new Date(),
            monthFirst: false,
            formatter: {
                date: function (date, settings) {
                    let parsedDate = new Date(date);
                    return parsedDate.getDate() + "." + (parsedDate.getMonth() + 1) + "." + parsedDate.getFullYear();
                }
            }
        };

        $('#createdFromDatepicker').calendar(calendarSettings);

        $('#createdToDatepicker').calendar(calendarSettings);

        $(document).on('change', '#maxRows', function () {
            routeRefreshLoan();
        });

        function staticFillter(startDate, endDate) {
            $('#createdFromDatepicker').calendar('set date', startDate);
            $('#createdToDatepicker').calendar('set date', endDate);

            routeRefreshLoan();
        }

        function routeRefreshLoan() {
            let routeRefreshLoan = '{{ route('profile.transaction.refresh')}}';
            let dataForm = $('#accountStatement').serialize();
            if ($('#maxRows').val() > 0) {
                dataForm += '&limit=' + $('#maxRows').val();
            }
            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: dataForm,
                success: function (data) {
                    $('#transactionData').html(data);
                },
            });
        }
    </script>
     <script type="text/javascript" src="{{ assets_version(asset('js/account-statement-export.js')) }}"></script>
@endpush
