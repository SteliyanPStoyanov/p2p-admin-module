<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <form id="historyForm" class="form-inline card-body"
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
                    </div>
                    <select class="form-control noClear" name="limit" id="maxRows"
                            style="position: absolute; right: 330px;bottom: 25px;z-index: 10;">
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
        <div id="main-table">
            <div>
                <div class="table-responsive">
                    <div>
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][invest_strategy_id]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.invest_strategy_id') ?: 'desc'}}"
                                    >
                                    {{__('common.InvestStrategyId')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.invest_strategy_id') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.invest_strategy_id') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][investor_id]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.investor_id') ?: 'desc'}}"
                                    >
                                    {{__('common.InvestorId')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.investor_id') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.investor_id') : ''}}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][name]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.name') ?: 'desc'}}"
                                    > {{__('common.Name')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.name') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.name') : '' }}"
                                       aria-hidden="true"></i>
                                </th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][priority]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.priority') ?: 'desc'}}"
                                    >
                                    {{__('common.Priority')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.priority') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.priority') : '' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][min_amount]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.min_amount') ?: 'desc'}}"
                                    >
                                    {{__('common.MinAmount')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.min_amount') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.min_amount') : '' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][max_amount]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.max_amount') ?: 'desc'}}"
                                    >{{__('common.MaxAmount')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.max_amount') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.max_amount') : '' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][min_interest_rate]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.min_interest_rate') ?: 'desc'}}"
                                    >{{__('common.MinInterestRate')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.min_interest_rate') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.min_interest_rate') : ' ' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][max_interest_rate]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.max_interest_rate') ?: 'desc'}}"
                                    >{{__('common.MaxInterestRate')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.max_interest_rate') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.max_interest_rate') : ' ' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][min_loan_period]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.min_loan_period') ?: 'desc'}}"
                                    >{{__('common.MinLoanPeriod')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.min_loan_period') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.min_loan_period') : ' ' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][max_loan_period]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.max_loan_period') ?: 'desc'}}"
                                    >{{__('common.MaxLoanPeriod')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.max_loan_period') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.max_loan_period') : ' ' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][loan_type]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.loan_type') ?: 'desc'}}"
                                    >{{__('common.LoanType')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.loan_type') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.loan_type') : ' ' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][loan_payment_status]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.loan_payment_status') ?: 'desc'}}"
                                    >{{__('common.LoanPaymentStatus')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.loan_payment_status') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.loan_payment_status') : ' ' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][portfolio_size]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.portfolio_size') ?: 'desc'}}"
                                    >{{__('common.PortfolioSize')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.portfolio_size') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.portfolio_size') : ' ' }}"
                                       aria-hidden="true"></i></th>

                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][created_at]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.created_at') ?: 'desc'}}"
                                    >{{__('common.CreatedAt')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.created_at') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.created_at') : ' ' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting active-sort">
                                    <input type="text" name="order[invest_strategy_history][archived_at]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.archived_at') ?: 'desc'}}"
                                    >{{__('common.ArchivedAt')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.archived_at') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.archived_at') : ' ' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][active]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.active') ?: 'desc'}}"
                                    >{{__('common.Status')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.active') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.active') : ' ' }}"
                                       aria-hidden="true"></i></th>
                                <th scope="col" class="text-center sorting">
                                    <input type="text" name="order[invest_strategy_history][deleted]"
                                           value="{{session($cacheKey . '.order.invest_strategy_history.deleted') ?: 'desc'}}"
                                    >{{__('common.Deleted')}}
                                    <i class="fa {{session($cacheKey . '.order.invest_strategy_history.deleted') ?
                            'fa-sort-'.session($cacheKey . '.order.invest_strategy_history.deleted') : ' ' }}"
                                       aria-hidden="true"></i></th>
                            </tr>
                            </thead>
                            <tbody id="table-history">
                            @include('admin::invest-strategy.history-list')
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')

    <script>
        url = document.location.toString();
        if (url.split('#')[1] === 'history') {
            loadSimpleDataGrid('{{ route('admin.invest-strategy.refreshHistory' ,$investStrategy->invest_strategy_id) }}', $("#historyForm"), $("#table-history"));

            $("#maxRows").change(function () {
                let routeRefreshLoan = '{{ route('admin.invest-strategy.refreshHistory' ,$investStrategy->invest_strategy_id)}}';

                $.ajax({
                    type: 'get',
                    url: routeRefreshLoan,
                    data: $('#historyForm').serialize(),

                    success: function (data) {
                        $('#table-history').html(data);
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
        }


    </script>
@endpush

