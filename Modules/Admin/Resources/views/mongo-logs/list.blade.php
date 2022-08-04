@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/table-style.css') }}">
@endsection

@section('content')
    {{--Filter Form--}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="mongoLogForm" class="form-inline card-body"
                      action="{{ route('admin.mongo-logs.list', $adapterKey) }}" method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="created_at" class="form-control w-100"
                                   id="createdAt"
                                   value="{{ session($cacheKey . 'created_at') }}"
                                   placeholder="{{__('common.CreatedAt')}}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="table" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByTable')}}"
                                   value="{{ session($cacheKey . '.table') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="investor_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByInvestorId')}}"
                                   value="{{ session($cacheKey . '.investor_id') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="loan_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.LoanId')}}"
                                   value="{{ session($cacheKey . '.loan_id') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <select name="action" class="w-100 form-control">
                                <option value="">{{ __('common.FilterByAction') }}</option>
                                <option value="create">create</option>
                                <option value="edit">edit</option>
                                <option value="delete">delete</option>
                            </select>
                        </div>
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

                </form>
            </div>
        </div>
    </div>
    {{--End Filter Form--}}

    {{--Main Table--}}
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="mongoLogTable">
                            @include('admin::mongo-logs.list-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--End Main table--}}
@endsection

@push('scripts')

    <script type="text/javascript" src="{{ asset('js/jsGrid.js') }}"></script>
    <script>
        let logHistoryController = '{{ route('admin.mongo-logs.refresh', $adapterKey) }}';
        let formId = $("#mongoLogForm");
        let tableId = $('#mongoLogTable');
        loadSimpleDataGrid(logHistoryController, formId, tableId);

        let selector = $("#createdAt");
        selector.daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        selector.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
        });

        selector.on('cancel.daterangepicker', function () {
            $(this).val('');
        });
    </script>

    <script>
        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.mongo-logs.refresh', $adapterKey)}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#mongoLogForm').serialize(),

                success: function (data) {
                    $('#mongoLogTable').html(data);
                },
            });
        });
    </script>
@endpush
