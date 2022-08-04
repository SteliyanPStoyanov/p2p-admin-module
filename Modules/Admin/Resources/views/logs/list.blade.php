@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    {{--Filter Form--}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="logHistoryCronForm" class="form-inline card-body"
                      action="{{ route('admin.cron-logs.list') }}" method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="createdAt"
                                   class="form-control w-100 singleDataPicker"
                                   value="{{ session($cacheKey . 'createdAt') }}"
                                   placeholder="{{__('common.FilterByCreatedAt')}}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="command" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByCommand')}}"
                                   value="{{ session($cacheKey . '.command') }}">
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
                        <option selected class="paginationValueLimit" value="50">50</option>
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
                        <div id="historyLogTable">
                            @include('admin::logs.list-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--End Main table--}}
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script>
        let logHistoryController = '{{ route('admin.cron-logs.refresh') }}';
        let formId = $("#logHistoryCronForm");
        let tableId = $('#historyLogTable');
        loadSimpleDataGrid(logHistoryController, formId, tableId);

        $(document).ready(function () {

            $('.singleDataPicker').daterangepicker({
                autoUpdateInput: false,
                "singleDatePicker": true,
                "autoApply": true,
                locale: {
                    format: 'DD.MM.YYYY',
                }
            });

            $('.singleDataPicker').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD.MM.YYYY'));
            });

        });

        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.cron-logs.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#logHistoryCronForm').serialize(),

                success: function (data) {
                    $('#historyLogTable').html(data);
                },
            });
        });
    </script>
@endpush
