@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="blogPageForm" class="form-inline card-body"
                      action="{{ route('admin.blog-page.list') }}"
                      method="PUT">
                    @csrf
                    <div class="form-row w-100">

                        <div class="form-group col-lg-2">
                            <input name="tags" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByTags')}}"
                                   value="{{ session($cacheKey . '.tags') }}">
                        </div>


                        <div class="form-group col-lg-2">
                            <input name="name" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByCreator')}}"
                                   value="{{ session($cacheKey . '.name') }}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="createdAt[from]" class="form-control w-100
                            singleDataPicker"
                                   value="{{ session($cacheKey . '.createdAt.from') }}"
                                   placeholder="{{__('common.FilterByCreatedAtFrom')}}">
                        </div>


                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="createdAt[to]"
                                   class="form-control w-100 singleDataPicker"
                                   value="{{ session($cacheKey . '.createdAt.to') }}"
                                   placeholder="{{__('common.FilterByCreatedAtTo')}}">
                        </div>

                        <div class="form-group col-lg-2">
                            <x-select-active active="{{ session($cacheKey . '.active') }}"/>
                        </div>
                        <div class="form-group col-lg-2">
                            <x-select-deleted deleted="{{ session($cacheKey . '.deleted') }}"/>
                        </div>

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


    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div id="btns-panel">
                        <x-btn-create url="{{ route('admin.blog-page.create') }}" name="{{ __('common.Create')}}"/>
                    </div>
                    <div class="table-responsive">
                        <div id="table-blog-pages">
                            @include('admin::blog-page.list-table')
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
        loadSimpleDataGrid('{{ route('admin.blog-page.refresh') }}', $("#blogPageForm"), $("#table-blog-pages"));
    </script>

    <script>
        $('.singleDataPicker').daterangepicker({
            autoUpdateInput: false,
            autoApply: true,
            "singleDatePicker": true,
            locale: {
                format: 'DD.MM.YYYY',
            }
        });
        $('.singleDataPicker').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY'));
        });


        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.blog-page.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#blogPageForm').serialize(),

                success: function (data) {
                    $('#table-blog-pages').html(data);
                },
            });
        });
    </script>
@endpush
