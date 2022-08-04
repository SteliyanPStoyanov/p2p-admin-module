@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="investorForm" class="form-inline card-body"
                      action="{{ route('admin.investors.list') }}"
                      method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2">
                            <input name="name" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByName')}}"
                                   value="{{ session($cacheKey . '.name') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="investor_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByInvestorId')}}"
                                   value="{{ session($cacheKey . '.investor_id') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="email" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByEmail')}}"
                                   value="{{ session($cacheKey . '.email') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="createdAt[from]"
                                   class="form-control w-100 singleDataPicker"

                                   value="{{ session($cacheKey . '.createdAt.from') }}"
                                   placeholder="{{__('common.FilterByRegisteredAtFrom')}}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="createdAt[to]"
                                   class="form-control w-100 singleDataPicker"

                                   value="{{ session($cacheKey . '.createdAt.to') }}"
                                   placeholder="{{__('common.FilterByRegisteredAtTo')}}">
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
                    <div class="form-row w-100 mt-3">
                        <div class="form-group col-lg-2">
                            <select name="type" class="form-control w-100">
                                <option value>{{__('common.SelectByType')}}</option>
                                @foreach($types as $type)
                                    <option
                                        @if(session($cacheKey . '.type') == $type)
                                        selected
                                        @endif
                                        value="{{$type}}">{{$type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="total_amount[from]" class="form-control w-100"
                                   id="totalAmountFrom"
                                   value="{{ session($cacheKey . '.total_amount.from') }}"
                                   placeholder="{{__('common.TotalAmountFrom')}}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="total_amount[to]" class="form-control w-100"
                                   id="totalAmountTo"
                                   value="{{ session($cacheKey . '.total_amount.to') }}"
                                   placeholder="{{__('common.TotalAmountTo')}}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="uninvested_amount[from]"
                                   class="form-control w-100"
                                   id="uninvestedAmountFrom"
                                   value="{{ session($cacheKey . '.uninvested_amount.from') }}"
                                   placeholder="{{__('common.UninvestedAmountFrom')}}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="uninvested_amount[to]"
                                   class="form-control w-100"
                                   id="uninvestedAmountTo"
                                   value="{{ session($cacheKey . '.uninvested_amount.to') }}"
                                   placeholder="{{__('common.UninvestedAmountTo')}}">
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
                        <div>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[investor][investor_id]"
                                               value="{{session($cacheKey . '.order.investor.investor_id') ?: 'desc'}}"
                                        >
                                        {{__('common.investorId')}}
                                        <i class="fa {{session($cacheKey . '.order.investor.investor_id') ?
                            'fa-sort-'.session($cacheKey . '.order.investor.investor_id') : ''}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[investor][email]"
                                               value="{{session($cacheKey . '.order.investor.email') ?: 'desc'}}"
                                        >
                                        {{__('common.Email')}}
                                        <i class="fa {{session($cacheKey . '.order.investor.email') ?
                            'fa-sort-'.session($cacheKey . '.order.investor.email') : ''}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[investor][created_at]"
                                               value="{{session($cacheKey . '.order.investor.created_at') ?: 'desc'}}"
                                        > {{__('common.RegisteredAt')}}
                                        <i class="fa {{session($cacheKey . '.order.investor.created_at') ?
                            'fa-sort-'.session($cacheKey . '.order.investor.created_at') : '' }}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[investor][first_name]"
                                               value="{{session($cacheKey . '.order.investor.first_name') ?: 'desc'}}"
                                        >
                                        {{__('common.Name')}}
                                        <i class="fa {{session($cacheKey . '.order.investor.first_name') ?
                            'fa-sort-'.session($cacheKey . '.order.investor.first_name') : '' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[investor][status]"
                                               value="{{session($cacheKey . '.order.investor.status') ?: 'desc'}}"
                                        >
                                        {{__('common.Status')}}
                                        <i class="fa {{session($cacheKey . '.order.investor.status') ?
                            'fa-sort-'.session($cacheKey . '.order.investor.status') : '' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[investor][type]"
                                               value="{{session($cacheKey . '.order.investor.type') ?: 'desc'}}"
                                        >{{__('common.InvestorType')}}
                                        <i class="fa {{session($cacheKey . '.order.investor.type') ?
                            'fa-sort-'.session($cacheKey . '.order.investor.type') : '' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[wallet][total_amount]"
                                               value="{{session($cacheKey . '.order.wallet.total_amount') ?: 'desc'}}"
                                        >{{__('common.AccountBalance')}}
                                        <i class="fa {{session($cacheKey . '.order.wallet.total_amount') ?
                            'fa-sort-'.session($cacheKey . '.order.wallet.total_amount') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[wallet][uninvested]"
                                               value="{{session($cacheKey . '.order.wallet.uninvested') ?: 'desc'}}"
                                        >{{__('common.UninvestedAmount')}}
                                        <i class="fa {{session($cacheKey . '.order.wallet.uninvested') ?
                            'fa-sort-'.session($cacheKey . '.order.wallet.uninvested') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                </tr>
                                </thead>
                                <tbody id="table-investors">
                                @include('admin::investor.list-table')
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

        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.investors.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#investorForm').serialize(),

                success: function (data) {
                    $('#table-investors').html(data);
                },
            });
        });

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
        loadSimpleDataGrid('{{ route('admin.investors.refresh') }}', $("#investorForm"), $("#table-investors"));
    </script>
@endpush
