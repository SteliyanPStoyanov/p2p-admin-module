@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="investForm" class="form-inline card-body"
                      action="{{ route('admin.invest-strategy.list') }}"
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
                            <input name="min_amount" class="form-control w-100" type="text"
                                   placeholder="{{__('common.MinAmount')}}"
                                   value="{{ session($cacheKey . '.min_amount') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="max_amount" class="form-control w-100" type="text"
                                   placeholder="{{__('common.MaxAmount')}}"
                                   value="{{ session($cacheKey . '.max_amount') }}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input name="min_interest_rate" class="form-control w-100" type="text"
                                   placeholder="{{__('common.MinInterestRate')}}"
                                   value="{{ session($cacheKey . '.min_interest_rate') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="max_interest_rate" class="form-control w-100" type="text"
                                   placeholder="{{__('common.MaxInterestRate')}}"
                                   value="{{ session($cacheKey . '.max_interest_rate') }}">
                        </div>

                    </div>
                    <div class="form-row w-100 mt-3">
                        <div class="form-group col-lg-2">
                            <input name="portfolio_size" class="form-control w-100" type="text"
                                   placeholder="{{__('common.PortfolioSize')}}"
                                   value="{{ session($cacheKey . '.portfolio_size') }}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input name="priority" class="form-control w-100" type="text"
                                   placeholder="{{__('common.Priority')}}"
                                   value="{{ session($cacheKey . '.priority') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="min_loan_period" class="form-control w-100" type="text"
                                   placeholder="{{__('common.MinLoanPeriod')}}"
                                   value="{{ session($cacheKey . '.min_loan_period') }}">
                        </div>

                        <div class="form-group col-lg-2">
                            <input name="max_loan_period" class="form-control w-100" type="text"
                                   placeholder="{{__('common.MaxLoanPeriod')}}"
                                   value="{{ session($cacheKey . '.max_loan_period') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="created_at[from]"
                                   class="form-control w-100 singleDataPicker"

                                   value="{{ session($cacheKey . '.created_at.from') }}"
                                   placeholder="{{__('common.FilterByCreatedAtFrom')}}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="created_at[to]"
                                   class="form-control w-100 singleDataPicker"

                                   value="{{ session($cacheKey . '.created_at.to') }}"
                                   placeholder="{{__('common.FilterByCreatedAtTo')}}">
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group col-lg-2 mt-3">
                            <select name="type" class="form-control w-100">
                                <option value>{{__('common.LoanType')}}</option>
                                @foreach($loanTypes as $key=> $loanType)
                                    <option
                                        @if(session($cacheKey . '.type') == $loanType)
                                        selected
                                        @endif
                                        value="{{loanType($loanType,true)}}">{{$loanType}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 mt-3">
                            <select name="payment_status" class="form-control w-100">
                                <option value>{{__('common.PaymentStatus')}}</option>
                                @foreach($loanPaymentStatuses as $loanPaymentStatus)
                                    <option
                                        @if(session($cacheKey . '.payment_status') == $loanPaymentStatus)
                                        selected
                                        @endif
                                        value="{{$loanPaymentStatus}}">{{$loanPaymentStatus}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 mt-3">
                            <select name="active" class="form-control w-100">
                                <option value>{{__('common.Status')}}</option>
                                <option
                                    @if(session($cacheKey . '.active') === 0)
                                    selected
                                    @endif
                                    value="0">Inactive
                                </option>
                                <option
                                    @if(session($cacheKey . '.active') == 1)
                                    selected
                                    @endif
                                    value="1">Active
                                </option>

                            </select>
                        </div>
                        <div class="form-group col-lg-2 mt-3">
                            <select name="deleted" class="form-control w-100">
                                <option value>{{__('common.Deleted')}}</option>
                                <option
                                    @if(session($cacheKey . '.deleted') === 0)
                                    selected
                                    @endif
                                    value="1">{{__('common.Yes')}}</option>
                                <option
                                    @if(session($cacheKey . '.deleted') == 1)
                                    selected

                                    @endif
                                    value="0">{{__('common.No')}}</option>

                            </select>
                        </div>
                        <div class="col-lg-12 mt-4">
                            <x-btn-filter/>
                            <a href="{{route('admin.invest-strategy.export')}}"
                               class="form-control btn-success  mr-1" target="_blank"
                               style="position: absolute; right: 282px;bottom: 0px;z-index: 10;">Export</a>
                        </div>
                        <select class="form-control noClear" name="limit" id="maxRows"
                                style="position: absolute; right: 400px;bottom: 25px;z-index: 10;">
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
                                        <input type="text" name="order[invest_strategy][invest_strategy_id]"
                                               value="{{session($cacheKey . '.order.invest_strategy.invest_strategy_id') ?: 'desc'}}"
                                        >
                                        {{__('common.InvestStrategyId')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.invest_strategy_id') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.invest_strategy_id') : ''}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][investor_id]"
                                               value="{{session($cacheKey . '.order.invest_strategy.investor_id') ?: 'desc'}}"
                                        >
                                        {{__('common.InvestorId')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.investor_id') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.investor_id') : ''}}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][name]"
                                               value="{{session($cacheKey . '.order.invest_strategy.name') ?: 'desc'}}"
                                        > {{__('common.Name')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.name') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.name') : '' }}"
                                           aria-hidden="true"></i>
                                    </th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][priority]"
                                               value="{{session($cacheKey . '.order.invest_strategy.priority') ?: 'desc'}}"
                                        >
                                        {{__('common.Priority')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.priority') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.priority') : '' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][min_amount]"
                                               value="{{session($cacheKey . '.order.invest_strategy.min_amount') ?: 'desc'}}"
                                        >
                                        {{__('common.MinAmount')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.min_amount') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.min_amount') : '' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][max_amount]"
                                               value="{{session($cacheKey . '.order.invest_strategy.max_amount') ?: 'desc'}}"
                                        >{{__('common.MaxAmount')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.max_amount') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.max_amount') : '' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][min_interest_rate]"
                                               value="{{session($cacheKey . '.order.invest_strategy.min_interest_rate') ?: 'desc'}}"
                                        >{{__('common.MinInterestRate')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.min_interest_rate') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.min_interest_rate') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][max_interest_rate]"
                                               value="{{session($cacheKey . '.order.invest_strategy.max_interest_rate') ?: 'desc'}}"
                                        >{{__('common.MaxInterestRate')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.max_interest_rate') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.max_interest_rate') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][min_loan_period]"
                                               value="{{session($cacheKey . '.order.invest_strategy.min_loan_period') ?: 'desc'}}"
                                        >{{__('common.MinLoanPeriod')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.min_loan_period') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.min_loan_period') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][max_loan_period]"
                                               value="{{session($cacheKey . '.order.invest_strategy.max_loan_period') ?: 'desc'}}"
                                        >{{__('common.MaxLoanPeriod')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.max_loan_period') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.max_loan_period') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][loan_type]"
                                               value="{{session($cacheKey . '.order.invest_strategy.loan_type') ?: 'desc'}}"
                                        >{{__('common.LoanType')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.loan_type') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.loan_type') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][loan_payment_status]"
                                               value="{{session($cacheKey . '.order.invest_strategy.loan_payment_status') ?: 'desc'}}"
                                        >{{__('common.LoanPaymentStatus')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.loan_payment_status') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.loan_payment_status') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][portfolio_size]"
                                               value="{{session($cacheKey . '.order.invest_strategy.portfolio_size') ?: 'desc'}}"
                                        >{{__('common.PortfolioSize')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.portfolio_size') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.portfolio_size') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][portfolio_size]"
                                               value="{{session($cacheKey . '.order.invest_strategy.portfolio_size') ?: 'desc'}}"

                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.portfolio_size') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.portfolio_size') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][created_at]"
                                               value="{{session($cacheKey . '.order.invest_strategy.created_at') ?: 'desc'}}"
                                        >{{__('common.CreatedAt')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.created_at') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.created_at') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][active]"
                                               value="{{session($cacheKey . '.order.invest_strategy.active') ?: 'desc'}}"
                                        >{{__('common.Status')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.active') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.active') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                    <th scope="col" class="text-center sorting">
                                        <input type="text" name="order[invest_strategy][deleted]"
                                               value="{{session($cacheKey . '.order.invest_strategy.deleted') ?: 'desc'}}"
                                        >{{__('common.Deleted')}}
                                        <i class="fa {{session($cacheKey . '.order.invest_strategy.deleted') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy.deleted') : ' ' }}"
                                           aria-hidden="true"></i></th>
                                </tr>
                                </thead>
                                <tbody id="table-investors">
                                @include('admin::invest-strategy.list-table')
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
        loadSimpleDataGrid('{{ route('admin.invest-strategy.refresh') }}', $("#investForm"), $("#table-investors"));
        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.invest-strategy.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#investForm').serialize(),

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

    </script>
@endpush
