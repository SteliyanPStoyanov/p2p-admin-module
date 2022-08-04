@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="investorForm" class="form-inline card-body"
                      action="{{ route('admin.tasks.list') }}"
                      method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100 mb-3">
                        <div class="form-group col-lg-2">
                            <input name="task_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterById')}}"
                                   value="{{ session($cacheKey . '.task_id') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="name" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByName')}}"
                                   value="{{ session($cacheKey . '.name') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <select name="task_type" class="form-control w-100">
                                <option value>{{__('common.SelectByType')}}</option>
                                @foreach($types as $type)
                                    <option
                                        @if(session($cacheKey . '.task_type') == $type)
                                        selected
                                        @endif
                                        value="{{$type}}">{{$type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2">
                            <select name="status" class="form-control w-100">
                                <option value>{{__('common.SelectByStatus')}}</option>
                                @foreach($statuses as $status)
                                    <option
                                        @if(session($cacheKey . '.status') == $status)
                                        selected
                                        @endif
                                        value="{{$status}}">{{$status}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row w-100">

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
                            <input type="number" autocomplete="off" name="amount[from]" class="form-control w-100"
                                   id="totalAmountFrom"
                                   value="{{ session($cacheKey . '.amount.from') }}"
                                   placeholder="{{__('common.AmountFrom')}}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="amount[to]" class="form-control w-100"
                                   id="totalAmountTo"
                                   value="{{ session($cacheKey . '.amount.to') }}"
                                   placeholder="{{__('common.AmountTo')}}">
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
                    <div class="table-responsive">
                        <div id="table-investors">
                            @include('admin::task.list-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-wrapper"></div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
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
    </script>
    <script>

        loadSimpleDataGrid('{{ route('admin.tasks.refresh') }}', $("#investorForm"), $("#table-investors"), false, 10000);

        $(document).ready(function () {

            $("#maxRows").change(function () {
                let routeRefreshLoan = '{{ route('admin.tasks.refresh')}}';

                $.ajax({
                    type: 'get',
                    url: routeRefreshLoan,
                    data: $('#investorForm').serialize(),

                    success: function (data) {
                        $('#table-investors').html(data);
                    },
                });
            });

            $("#table-investors").on('click', '.process-task', function (event) {
                let url = '{{route('admin.tasks.update-process-by')}}';
                let taskId = $(this).attr('data-taskId');
                let taskType = $(this).attr('data-taskType');

                $(this).parent().siblings('.status').text("{{\Modules\Common\Entities\Task::TASK_STATUS_PROCESSING}}");

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {"_token": "{{ csrf_token() }}", 'task_id': taskId, 'task_type': taskType},
                    headers: {
                        "Accept": "application/json",
                    },
                    success: function (data) {

                        if (data.url) {
                            window.location.href = data.url;
                        }
                        $('#modal-wrapper').html(data);

                        $('#modal-' + taskId).modal('show');
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
        });
    </script>
@endpush
