@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="walletForm" class="form-inline card-body"
                      action="{{ route('admin.wallets.list') }}"
                      method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2">
                            <input name="name" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByOwner')}}"
                                   value="{{ session($cacheKey . '.name') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="investor_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByOwnerId')}}"
                                   value="{{ session($cacheKey . '.investor_id') }}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="total_amount[from]" class="form-control w-100"
                                   value="{{ session($cacheKey . '.total_amount.from') }}"
                                   placeholder="{{__('common.BalanceFrom')}}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="total_amount[to]" class="form-control w-100"
                                   value="{{ session($cacheKey . '.total_amount.to') }}"
                                   placeholder="{{__('common.BalanceTo')}}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="invested[from]" class="form-control w-100"
                                   value="{{ session($cacheKey . '.invested.from') }}"
                                   placeholder="{{__('common.InvestedFrom')}}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input type="number" autocomplete="off" name="invested[to]" class="form-control w-100"
                                   value="{{ session($cacheKey . '.invested.to') }}"
                                   placeholder="{{__('common.InvestedTo')}}">
                        </div>
                        <div class="form-group col-lg-2 mt-3">
                            <input type="number" autocomplete="off" name="uninvested[from]" class="form-control w-100"
                                   value="{{ session($cacheKey . '.uninvested.from') }}"
                                   placeholder="{{__('common.UninvestedFrom')}}">
                        </div>

                        <div class="form-group col-lg-2 mt-3">
                            <input type="number" autocomplete="off" name="uninvested[to]" class="form-control w-100"
                                   value="{{ session($cacheKey . '.uninvested.to') }}"
                                   placeholder="{{__('common.UninvestedTo')}}">
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
                        <div class="form-group col-lg-2 mt-3">
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
                    </div>
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

                </form>
            </div>
        </div>

    </div>
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[wallet][wallet_id]"
                                           value="{{session($cacheKey . '.order.wallet.wallet_id') ?: 'desc'}}"
                                    >
                                    {{__('common.Id')}}
                                    <i class="fa {{session($cacheKey . '.order.wallet.wallet_id') ?
                            'fa-sort-'.session($cacheKey . '.order.wallet.wallet_id') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[wallet][created_at]"
                                           value="{{session($cacheKey . '.order.wallet.created_at') ?: 'desc'}}"
                                    >
                                    {!!  __('common.DateCreated') !!}
                                    <i class="fa {{session($cacheKey . '.order.wallet.created_at') ?
                            'fa-sort-'.session($cacheKey . '.order.wallet.transaction_id') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[investor][first_name]"
                                           value="{{session($cacheKey . '.order.investor.first_name') ?: 'desc'}}"
                                    >
                                    {{__('common.Owner')}}
                                    <i class="fa {{session($cacheKey . '.order.investor.first_name') ?
                            'fa-sort-'.session($cacheKey . '.order.investor.first_name') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[wallet][investor_id]"
                                           value="{{session($cacheKey . '.order.wallet.investor_id') ?: 'desc'}}"
                                    >
                                    {{__('common.OwnerId')}}
                                    <i class="fa {{session($cacheKey . '.order.wallet.investor_id') ?
                            'fa-sort-'.session($cacheKey . '.order.wallet.investor_id') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[wallet][total_amount]"
                                           value="{{session($cacheKey . '.order.wallet.total_amount') ?: 'desc'}}"
                                    >
                                    {{__('common.Balance')}}
                                    <i class="fa {{session($cacheKey . '.order.wallet.total_amount') ?
                            'fa-sort-'.session($cacheKey . '.order.wallet.total_amount') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[wallet][invested]"
                                           value="{{session($cacheKey . '.order.wallet.invested') ?: 'desc'}}"
                                    >
                                    {{__('common.Invested')}}
                                    <i class="fa {{session($cacheKey . '.order.wallet.invested') ?
                            'fa-sort-'.session($cacheKey . '.order.wallet.invested') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[wallet][uninvested]"
                                           value="{{session($cacheKey . '.order.wallet.uninvested') ?: 'desc'}}"
                                    >
                                    {{__('common.Uninvested')}}
                                    <i class="fa {{session($cacheKey . '.order.wallet.uninvested') ?
                            'fa-sort-'.session($cacheKey . '.order.wallet.uninvested') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[wallet][blocked_amount]"
                                           value="{{session($cacheKey . '.order.wallet.blocked_amount') ?: 'desc'}}"
                                    >
                                    {{__('common.BlockedAmount')}}
                                    <i class="fa {{session($cacheKey . '.order.wallet.blocked_amount') ?
                            'fa-sort-'.session($cacheKey . '.order.wallet.blocked_amount') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[investor][type]"
                                           value="{{session($cacheKey . '.order.investor.type') ?: 'desc'}}"
                                    >
                                    {{__('common.Type')}}
                                    <i class="fa {{session($cacheKey . '.order.investor.type') ?
                            'fa-sort-'.session($cacheKey . '.order.investor.type') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="table-wallets">
                            @include('admin::wallets.list-table')
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
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
        loadSimpleDataGrid('{{ route('admin.wallets.refresh') }}', $("#walletForm"), $("#table-wallets"));
    </script>
    <script>
        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.wallets.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#walletForm').serialize(),

                success: function (data) {
                    $('#table-wallets').html(data);
                },
            });
        });
    </script>

@endpush
