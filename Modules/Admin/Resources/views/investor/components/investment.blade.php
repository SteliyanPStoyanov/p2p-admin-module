<div class="container mw-100 pr-0">
    <div class=row">
        <div class="col-lg-12 pl-0 pr-0">
            <div style="min-height: 350px">

                <div class="table-responsive">
                    <div id="table-invests">
                        <form id="investorInvestments" class="form-inline mb-5"
                              action="{{ route('admin.administrators.list') }}"
                              method="GET">
                            {{ admin_csrf_field() }}
                            <div class="form-row w-100" style="position: relative;">
                                <div class="form-group col-lg-2 mb-3">
                                    <input type="text" autocomplete="off" name="loan_created_at[from]"
                                           class="form-control w-100 singleDataPicker"

                                           value="{{ session($cacheKey . '.loan_created_at.from') }}"
                                           placeholder="{{__('common.ListingDateFrom')}}">
                                </div>
                                <div class="form-group col-lg-2 mb-3">
                                    <input type="text" autocomplete="off" name="loan_created_at[to]"
                                           class="form-control w-100 singleDataPicker"

                                           value="{{ session($cacheKey . '.loan_created_at.to') }}"
                                           placeholder="{{__('common.ListingDateFrom')}}">
                                </div>
                                <div class="form-group col-lg-2 mb-3">
                                    <input type="text" autocomplete="off" name="created_at[from]"
                                           class="form-control w-100 singleDataPicker"

                                           value="{{ session($cacheKey . '.createdAt.from') }}"
                                           placeholder="{{__('common.InvestDateFrom')}}">
                                </div>
                                <div class="form-group col-lg-2 mb-3">
                                    <input type="text" autocomplete="off" name="created_at[to]"
                                           class="form-control w-100 singleDataPicker"

                                           value="{{ session($cacheKey . '.createdAt.to') }}"
                                           placeholder="{{__('common.InvestDateFrom')}}">
                                </div>
                                <div class="form-group col-lg-2 mb-3">
                                    <select name="originator" class="form-control w-100">
                                        <option value>{{__('common.Lender')}}</option>
                                        @foreach($loanOriginators as $loanOriginator)
                                            <option
                                                @if(session($cacheKey . '.originator_id') == $loanOriginator->originator_id)
                                                selected
                                                @endif
                                                value="{{$loanOriginator->originator_id}}">{{$loanOriginator->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-2 mb-3">
                                    <input name="investment[loan_id]" class="form-control w-100" type="text"
                                           placeholder="{{__('common.Id')}}"
                                           value="{{ session($cacheKey . '.investment.loan_id') }}">
                                </div>
                                <div class="form-group col-lg-2 mb-3">
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

                                <div class="form-group col-lg-2 mb-3">
                                    <input type="number" autocomplete="off" name="interest_rate_percent[from]"
                                           class="form-control w-100"
                                           value="{{ session($cacheKey . '.interest_rate_percent.from') }}"
                                           placeholder="{{__('common.InterestRateFrom')}}">
                                </div>

                                <div class="form-group col-lg-2 mb-3 ">
                                    <input type="number" autocomplete="off" name="interest_rate_percent[to]"
                                           class="form-control w-100"
                                           value="{{ session($cacheKey . '.interest_rate_percent.to') }}"
                                           placeholder="{{__('common.InterestRateTo')}}">
                                </div>

                                <div class="form-group col-lg-2 mb-3 ">
                                    <input type="number" autocomplete="off" name="period[from]"
                                           class="form-control w-100"
                                           value="{{ session($cacheKey . '.period.from') }}"
                                           placeholder="{{__('common.LoanTermFrom')}}">
                                </div>

                                <div class="form-group col-lg-2 mb-3 ">
                                    <input type="number" autocomplete="off" name="period[to]" class="form-control w-100"
                                           value="{{ session($cacheKey . '.period.to') }}"
                                           placeholder="{{__('common.LoanTermTo')}}">
                                </div>
                                <div class="form-group col-lg-2 mb-3 ">
                                    <select name="status" class="form-control w-100">
                                        <option value>{{__('common.LoanStatus')}}</option>
                                        @foreach($loanStatuses as $loanStatus)
                                            <option
                                                @if(session($cacheKey . '.status') == $loanStatus)
                                                selected
                                                @endif
                                                value="{{$loanStatus}}">{{ucfirst($loanStatus)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-2 mb-3 ">
                                    <select id="paymentStatuses" name="payment_status" class="form-control w-100">
                                        <option value="">{{__('common.PaymentStatus')}}</option>
                                        @foreach($loanPaymentStatuses as $loanPaymentStatus)
                                            <option
                                                @if(session($cacheKey . '.payment_status') == $loanPaymentStatus)
                                                selected
                                                @endif
                                                value="{{$loanPaymentStatus}}">{{payStatus($loanPaymentStatus)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-2 mb-3 ">
                                    <select name="unlisted" class="form-control w-100">
                                        <option value>{{__('common.SelectListingStatus')}}</option>
                                        <option
                                            @if(session($cacheKey . '.unlisted') === 0)
                                            selected
                                            @endif
                                            value="0">{{ __('common.Listed') }}
                                        </option>
                                        <option
                                            @if(session($cacheKey . '.unlisted') == 1)
                                            selected
                                            @endif
                                            value="1">{{ __('common.Unlisted') }}
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group col-lg-2 mb-3 ">
                                    <select name="market" class="form-control w-100">
                                        <option value>{{__('common.AllMarkets')}}</option>
                                        <option
                                            @if(session($cacheKey . '.market') === 0)
                                            selected
                                            @endif
                                            value="0">{{ __('common.PrimaryMarket') }}
                                        </option>
                                        <option
                                            @if(session($cacheKey . '.market') == 1)
                                            selected
                                            @endif
                                            value="1">{{ __('common.SecondaryMarket') }}
                                        </option>
                                    </select>
                                </div>

                                <div class="clearfix"></div>
                                <div class="col-lg-12 mt-4">
                                    <x-btn-filter/>
                                     <a href="{{route('admin.investor-investment.export' , $investor->investor_id)}}"
                               class="form-control btn-success  mr-1" target="_blank"
                               style="position: absolute; right: 280px;bottom: 0px;z-index: 10;">Export</a>
                                </div>
                                <select class="form-control noClear" name="limit" id="maxRows"
                                        style="position: absolute; right: 370px; bottom: 0px; z-index: 10;">
                                    <option class="paginationValueLimit" selected value="10">10</option>
                                    <option class="paginationValueLimit" value="25">25</option>
                                    <option class="paginationValueLimit" value="50">50</option>
                                    <option class="paginationValueLimit" value="100">100</option>
                                </select>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <div>
                                <table class="table">
                                    <thead>
                                    @include('admin::investor.components.investment-sorting-thead')
                                    </thead>
                                    <tbody id="investorInvestmentsTable">
                                    @include('admin::investor.components.investment-list-table')
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@push('scripts')

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
        var url = document.location.toString();
        if (url.split('#')[1] === 'investments') {
            loadSimpleDataGrid('{{ route('admin.investors-investments-refresh', $investor->investor_id) }}', $("#investorInvestments"), $("#investorInvestmentsTable"));
        }

        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.investors-investments-refresh', $investor->investor_id) }}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#investorInvestments').serialize(),
                success: function (data) {
                    $('#investorInvestmentsTable').html(data);
                },
            });
        });
    </script>
@endpush
