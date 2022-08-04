@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ asset('css/table-style.css') }}">
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="emailTemplateForm" class="form-inline card-body"
                      action="{{ route('admin.emailTemplate.list') }}"
                      method="PUT">
                    @csrf
                    <div class="form-row w-100">
                        <div class="col-lg-2 mb-3">
                            <input name="key" class="form-control w-100 mb-3" type="text"
                                   placeholder="{{__('common.FilterByName')}}"
                                   value="{{ session($cacheKey . '.key') }}">
                        </div>
                        <div class="col-lg-2 mb-3">
                            <input type="text" autocomplete="off" name="createdAt" class="form-control w-100"
                                   id="createdAt"
                                   value="{{ session($cacheKey . '.createdAt') }}"
                                   placeholder="{{__('common.FilterByCreatedAt')}}">
                        </div>
                        <div class="col-lg-2 mb-3">
                            <input type="text" autocomplete="off" name="updatedAt"
                                   class="form-control w-100" id="updatedAt"
                                   value="{{ session($cacheKey . '.updatedAt') }}"
                                   placeholder="{{__('common.FilterByUpdatedAt')}}">
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
                    <div id="btns-panel" class="mb-3">
                        <x-btn-create url="{{ route('communication.emailTemplate.create') }}"
                                      name="{{ __('common.Create') }}"/>
                    </div>
                    <div class="table-responsive">
                        <div id="table-emailTemplates">
                            @include('communication::email-template.list-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript" src="{{ asset('js/jsGrid.js') }}"></script>
    <script>
        loadDateRangePicker($("#sendAt"));
        loadSimpleDataGrid('{{ route('admin.emailTemplate.refresh') }}', $("#emailTemplateForm"), $("#table-emailTemplates"));
    </script>
    <script>
        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.emailTemplate.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#emailTemplateForm').serialize(),

                success: function (data) {
                    $('#table-emailTemplates').html(data);
                },
            });
        });
    </script>

@endpush
