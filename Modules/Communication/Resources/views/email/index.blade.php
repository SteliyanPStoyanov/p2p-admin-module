@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
    <style>
        .button-div {
            display: none !important;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="emailForm" class="form-inline card-body"
                      action="{{ route('admin.email.list') }}"
                      method="PUT">
                    @csrf
                    <div class="form-row w-100">

                        <div class="col-lg-2 mb-3">
                            <input name="title" class="form-control w-100 mb-3" type="text"
                                   placeholder="{{__('table.Name')}}"
                                   value="{{ session($cacheKey . '.title') }}">
                        </div>
                        <div class="col-lg-2 mb-3">
                            <input name="sender_from" class="form-control w-100 mb-3" type="text"
                                   placeholder="{{__('common.Sender')}}"
                                   value="{{ session($cacheKey . '.sender_from') }}">
                        </div>
                        <div class="col-lg-2 mb-3">
                            <input type="text" autocomplete="off" name="send_at" class="form-control w-100"
                                   id="sendAt"
                                   value="{{ session($cacheKey . '.send_at') }}"
                                   placeholder="{{__('common.SendAt')}}">
                        </div>
                        <div class="col-lg-12">
                            <x-btn-filter/>
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
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="table-emails">
                            @include('communication::email.list-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script>
        loadDateRangePicker($("#sendAt"));
        loadSimpleDataGrid('{{ route('admin.email.refresh') }}', $("#emailForm"), $("#table-emails"));
    </script>
    <script>
        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.email.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#emailForm').serialize(),

                success: function (data) {
                    $('#table-emails').html(data);
                },
            });
        });
    </script>

@endpush
