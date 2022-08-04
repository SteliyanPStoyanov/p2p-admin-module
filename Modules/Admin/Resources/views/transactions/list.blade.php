@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="transactionForm" class="form-inline card-body"
                      action="{{ route('admin.transactions.list') }}"
                      method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2">
                            <input name="transaction_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterById')}}"
                                   value="{{ session($cacheKey . '.transaction_id') }}">
                        </div>
                        <br>
                        <br>
                        <div class="form-group col-lg-2">
                            <input name="loan_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.LoanId')}}"
                                   value="{{ session($cacheKey . '.loan_id') }}">
                        </div>
                        <br>
                        <br>
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
                        <br>
                        <br>
                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="amount[from]" class="form-control w-100"
                                   id="amountFrom"
                                   value="{{ session($cacheKey . '.amount.from') }}"
                                   placeholder="{{__('common.AmountFrom')}}">
                        </div>
                        <br>
                        <br>
                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="amount[to]" class="form-control w-100"
                                   id="amountTo"
                                   value="{{ session($cacheKey . '.amount.to') }}"
                                   placeholder="{{__('common.AmountTo')}}">
                        </div>
                        <br>
                        <br>
                        <div class="form-group col-lg-2">
                            <input name="from" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FromAccount')}}"
                                   value="{{ session($cacheKey . '.from') }}">
                        </div>
                        <br>
                        <br>
                        <div class="form-group col-lg-2">
                            <input name="to" class="form-control w-100" type="text"
                                   placeholder="{{__('common.ToAccount')}}"
                                   value="{{ session($cacheKey . '.to') }}">
                        </div>
                        <br>
                        <br>
                        <div class="form-group col-lg-2">
                            <select name="type[]" class="form-control w-100">
                                <option value>{{__('common.SelectByTransactionType')}}</option>
                                @foreach($types as $type)
                                    <option
                                        @if(session($cacheKey . '.type') == $type)
                                        selected
                                        @endif
                                        value="{{$type}}">{{\Modules\Common\Entities\Transaction::getAdminLabel($type)}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-12 mt-4">
                            <x-btn-filter/>
                        </div>

                        <select class="form-control noClear" name="limit" id="maxRows"
                                style="position: absolute; right: 321px;bottom: 92px;z-index: 10;">
                            <option class="paginationValueLimit" value="10">10</option>
                            <option class="paginationValueLimit" value="25">25</option>
                            <option class="paginationValueLimit" value="50">50</option>
                            <option class="paginationValueLimit" value="100">100</option>
                        </select>


                    </div>
                </form>
                <div class="row">
                    <div class="col">
                        <div id="btns-panel">
                            <form method="POST" enctype="multipart/form-data"
                                  action="{{ route('admin.transactions.upload-payments') }}">
                                {{ admin_csrf_field() }}
                                <div class="input-group mb-2 justify-content-flex-end">
                                    <div class="custom-file wm-30">
                                        <label for="import_file" class="custom-file-label w-100"
                                               style="left:auto; text-align: left">
                                            {{ __('common.ImportPayments') }}
                                        </label>
                                        <input class="custom-file-input" name="import_file" type="file"
                                               id="import_file">
                                    </div>
                                    <button
                                        class="btn btn-success default-btn-last"
                                        type="submit"
                                        style="margin-left: 1%">
                                        {{__('common.Import')}}
                                    </button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="table-transactions">
                            <table class="table" style="table-layout: fixed">
                                <thead>
                                <tr>
                                    <th scope="col" class="col-1 w-25 sorting active-sort">
                                        <input type="text" name="order[transaction][transaction_id]"
                                               value="{{session($cacheKey . '.order.transaction.transaction_id') ?: 'desc'}}"
                                        >
                                        {{__('common.Id')}}
                                        <i class="fa {{session($cacheKey . '.order.transaction.transaction_id') ?
                            'fa-sort-'.session($cacheKey . '.order.transaction.transaction_id') : 'fa-sort'}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="sorting col-2 w-50" style="min-width: 120px;">
                                        <input type="text" name="order[transaction][created_at]"
                                               value="{{session($cacheKey . '.order.transaction.created_at') ?: 'desc'}}"
                                        >
                                        {{__('common.DateTime')}}
                                        <i class="fa {{session($cacheKey . '.order.transaction.created_at') ?
                            'fa-sort-'.session($cacheKey . '.order.transaction.created_at') : 'fa-sort'}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="sorting col-1 w-50">
                                        <input type="text" name="order[transaction][amount]"
                                               value="{{session($cacheKey . '.order.transaction.amount') ?: 'desc'}}"
                                        >
                                        {{__('common.Amount')}}
                                        <i class="fa {{session($cacheKey . '.order.transaction.amount') ?
                            'fa-sort-'.session($cacheKey . '.order.transaction.amount') : 'fa-sort'}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="sorting col-1 w-50">
                                        <input type="text" name="order[from]"
                                               value="{{session($cacheKey . '.order.from') ?: 'desc'}}"
                                        >
                                        {{__('common.From')}}
                                        <i class="fa {{session($cacheKey . '.order.from') ?
                            'fa-sort-'.session($cacheKey . '.order.from') : 'fa-sort'}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="sorting col-1 w-50">
                                        <input type="text" name="order[to]"
                                               value="{{session($cacheKey . '.order.to') ?: 'desc'}}"
                                        >
                                        {{__('common.To')}}
                                        <i class="fa {{session($cacheKey . '.order.to') ?
                            'fa-sort-'.session($cacheKey . '.order.to') : 'fa-sort'}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="sorting col-2 w-50">
                                        <input type="text" name="order[transaction][type]"
                                               value="{{session($cacheKey . '.order.transaction.type') ?: 'desc'}}"
                                        >
                                        {{__('common.Type')}}
                                        <i class="fa {{session($cacheKey . '.order.transaction.type') ?
                            'fa-sort-'.session($cacheKey . '.order.transaction.type') : 'fa-sort'}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="col-3 w-100">{{__('common.Details')}}</th>
                                </tr>
                                </thead>
                                <tbody id="transactionsTable">
                                @include('admin::transactions.list-table')
                                </tbody>
                            </table>
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
        loadSimpleDataGrid('{{ route('admin.transactions.refresh') }}', $("#transactionForm"), $("#transactionsTable"));
    </script>

    <script>
        $('.dataPicker').daterangepicker({
            locale: {
                format: 'DD.MM.YYYY',
            }
        });
    </script>

    <script>
        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.transactions.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#transactionForm').serialize(),

                success: function (data) {
                    $('#transactionsTable').html(data);
                },
            });
        });
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
    </script>

@endpush
