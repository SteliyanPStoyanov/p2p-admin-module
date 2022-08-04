@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="templateForm" class="form-inline card-body"
                      action="{{ route('admin.user-agreement.list') }}"
                      method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2 mt-3">
                            <input name="name" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByName')}}"
                                   value="{{ session($cacheKey . '.name') }}">
                        </div>
                        <div class="form-group col-lg-2 mt-3">
                            <select name="type" class="form-control w-100" id="type">
                                <option value="">
                                    {{ __('common.TemplateType') }}
                                </option>
                                @foreach($types as $type)
                                    <option value="{{$type}}">{{$type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 mt-3">
                            <input type="text" autocomplete="off" name="createdAt[from]"
                                   class="form-control w-100 singleDataPicker"

                                   value="{{ session($cacheKey . '.createdAt.from') }}"
                                   placeholder="{{__('common.FilterByCreatedAtFrom')}}">
                        </div>
                        <div class="form-group col-lg-2 mt-3">
                            <input type="text" autocomplete="off" name="createdAt[to]"
                                   class="form-control w-100 singleDataPicker"

                                   value="{{ session($cacheKey . '.createdAt.to') }}"
                                   placeholder="{{__('common.FilterByCreatedAtTo')}}">
                        </div>
                        <div class="col-lg-12">
                            <x-btn-filter/>
                        </div>
                        <select class="form-control" name="limit" id="maxRows"
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
                        <x-btn-create url="{{ route('admin.user-agreement.create') }}"
                                      name="{{ __('common.Create') }}"/>
                    </div>
                    <div class="table-responsive">
                        <div id="table-templates">
                            @include('admin::user-agreement.list-table')
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
        loadSimpleDataGrid('{{ route('admin.user-agreement.refresh') }}', $("#templateForm"), $("#table-templates"));
    </script>
    <script>
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

        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.emailTemplate.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#templateForm').serialize(),

                success: function (data) {
                    $('#table-templates').html(data);
                },
            });
        });
    </script>

@endpush
