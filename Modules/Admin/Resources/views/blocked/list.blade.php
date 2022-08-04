@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="blockedIpForm" class="form-inline card-body"
                      action="{{ route('admin.blocked-ip.list') }}"
                      method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100 mb-3">
                        <div class="form-group col-lg-2">
                            <input name="email" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByEmail')}}"
                                   value="{{ session($cacheKey . '.email') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="ip" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByIp')}}"
                                   value="{{ session($cacheKey . '.ip') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <x-select-active active="{{ session($cacheKey . '.active') }}"/>
                        </div>
                    </div>
                    <div class="form-row w-100">
                        <div class="clearfix"></div>
                        <div class="col-lg-12 mt-4">
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
    {{--Main Table--}}
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <div id="blockIpTable">
                            @include('admin::blocked.list-table')
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
        loadSimpleDataGrid('{{ route('admin.blocked-ip.refresh') }}', $("#blockedIpForm"), $("#blockIpTable"), true, 10000);

        $(document).ready(function () {

            $("#maxRows").change(function () {
                let routeRefreshBlockedIp = '{{ route('admin.blocked-ip.refresh')}}';

                $.ajax({
                    type: 'get',
                    url: routeRefreshBlockedIp,
                    data: $('#blockedIpForm').serialize(),

                    success: function (data) {
                        $('#blockIpTable').html(data);
                    },
                });
            });
        });
    </script>
@endpush
