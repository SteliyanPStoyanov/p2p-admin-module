@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ asset('css/table-style.css') }}">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="registrationAttemptForm" class="form-inline card-body"
                      action="{{ route('admin.registration-attempt.list') }}"
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
                        <div id="registrationAttempt">
                            @include('admin::registration-attempt.list-table')
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
        loadSimpleDataGrid('{{ route('admin.registration-attempt.refresh') }}', $("#registrationAttemptForm"), $("#registrationAttempt"), true, 10000);

        $(document).ready(function () {

            $("#maxRows").change(function () {
                let routeRefreshRegistrationAttempt = '{{ route('admin.registration-attempt.refresh')}}';

                $.ajax({
                    type: 'get',
                    url: routeRefreshRegistrationAttempt,
                    data: $('#registrationAttemptForm').serialize(),

                    success: function (data) {
                        $('#registrationAttempt').html(data);
                    },
                });
            });
        });
    </script>
@endpush
