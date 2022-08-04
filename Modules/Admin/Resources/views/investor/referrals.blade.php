@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    {{--Filter Form--}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="investorReferralsForm" class="form-inline card-body"
                      action="{{ route('admin.investors-referrals.list') }}" method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2 mb-3">
                            <input type="text" autocomplete="off" name="investor_id" class="form-control w-100"
                                   value="{{ session($cacheKey . 'investor_id') }}"
                                   placeholder="{{__('common.FilterByInvestorId')}}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input type="text" autocomplete="off" name="name" class="form-control w-100"
                                   value="{{ session($cacheKey . 'name') }}"
                                   placeholder="{{__('common.FilterByNames')}}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input name="email" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByEmail')}}"
                                   value="{{ session($cacheKey . '.email') }}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input name="deposit[from]" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByDepositFrom')}}"
                                   value="{{ session($cacheKey . '.deposit.from') }}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input name="deposit[to]" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByDepositTo')}}"
                                   value="{{ session($cacheKey . '.deposit.to') }}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input name="invested[from]" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByInvestedFrom')}}"
                                   value="{{ session($cacheKey . '.invested.from') }}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input name="invested[to]" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByInvestedTo')}}"
                                   value="{{ session($cacheKey . '.invested.to') }}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input name="referrals_count[from]" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByReferralCountFrom')}}"
                                   value="{{ session($cacheKey . '.referrals_count.from') }}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input name="referrals_count[to]" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByReferralCountTo')}}"
                                   value="{{ session($cacheKey . '.referrals_count.to') }}">
                        </div>
                        <div class="form-row w-100">
                            <div class="col-lg-12 mt-4">
                                <x-btn-filter/>
                            </div>
                        </div>
                        <select class="form-control noClear" name="limit" id="maxRows"
                                style="position: absolute; right: 321px;bottom: 25px;z-index: 10;">
                            <option class="paginationValueLimit" value="10">10</option>
                            <option class="paginationValueLimit" value="25">25</option>
                            <option class="paginationValueLimit" value="50">50</option>
                            <option class="paginationValueLimit" value="100">100</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--End Filter Form--}}

    {{--Form bonus for investor--}}
    <div class="card">
        <form class="form-inline card-body"
              action="{{route('admin.investors-referrals.give-bonus')}}" method="POST" class="col-12">
            {{ admin_csrf_field() }}
            <div class="form-row w-100">
                {{--                <input class="form-control w-20" type="number" name="bonusAmount" id="bonusAmount"--}}
                {{--                       placeholder="{{__('table.SumForBonus')}}"><br><br>--}}
                <input class="form-control" id="bonusAmount" name="bonusAmount" type="text"
                       placeholder="{{__('common.SumForBonus')}}">
                <button type="submit" name="action" class="btn btn-cyan">{{__('common.GiveBonus')}}</button>
            </div>
        </form>
    </div>
    {{--Form end  bonus for investor--}}

    {{--Main Table--}}
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="table-investors-referrals">
                            @include('admin::investor.components.referrals-list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--End Main Table--}}
    </div>
<div id="modal-wrapper"></div>
@endsection()
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script>
        loadSimpleDataGrid('{{route('admin.investors-referrals.list-refresh')}}', $("#investorReferralsForm"), $("#table-investors-referrals"));
    </script>
    <script>
        $(document).ready(function () {
            $("#bonusAmount").attr('maxlength', '2');
        });
    </script>
    <script>
        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.investors-referrals.list-refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#investorReferralsForm').serialize(),

                success: function (data) {
                    $('#table-investors-referrals').html(data);
                },
            });
        });
        $("#table-investors-referrals").on('click', '.show-referral', function (event) {
            event.preventDefault();
            let url = '{{route('admin.investor.show-referral')}}';
            let investorId = $(this).attr('data-investorId');

            $.ajax({
                url: url,
                type: 'GET',
                data: {"_token": "{{ csrf_token() }}", 'investor_id': investorId},
                headers: {
                    "Accept": "application/json",
                },
                success: function (data) {

                    if (data.url) {
                        window.location.href = data.url;
                    }
                    $('#modal-wrapper').html(data);

                    $('#modal-' + investorId).modal('show');
                },
                error: function (jqXHR) {

                    let errorsId = jqXHR.responseJSON.task_id;

                    let errorsType = jqXHR.responseJSON.task_type;

                    let errorHandler = $("#errorHandlerAjax");

                    let errors = '<div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">\n';

                    if (errorsType) {
                        errorsType.forEach(function (error) {
                            errors += error + '<br/>';
                        });
                    }

                    if (errorsId) {
                        errorsId.forEach(function (error) {
                            errors += error + '<br/>';
                        });
                    }

                    errors += '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                        '    <span aria-hidden="true">&times;</span>\n' +
                        '  </button>\n' +
                        '</div>';
                    errorHandler.html(errors);
                }
            });
        });
    </script>

@endpush
