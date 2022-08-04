<div>

    <h2 class="page-title text-truncate text-dark font-weight-medium mb-1 text-center mb-3">
        <strong>#{{$investStrategy->invest_strategy_id}}</strong>
    </h2>
    <div class="container mw-100">
        <div class="row">
            <div class="col-xl-5 col-lg-6 col-md-6 pl-0">
                <div class="card">
                    <h3 class="card-header pt-4 pl-4"><b>{{__('common.InvestStrategyDetails')}}</b></h3>
                    <div class="card-body">
                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.InvestorId')}}</strong>:</span>
                            <span class="card-content">
                                            <a target="_blank"
                                               href="{{ route('admin.investors.overview', $investStrategy->investor_id) }}">
                                            {{$investStrategy->investor_id}}
                                            </a>
                                        </span>
                        </p>
                        <p class="card-text">
                            <span class="card-title-main"><strong>{{__('common.Name')}}</strong>:</span>
                            <span class="card-content">{{$investStrategy->name}}</span>
                        </p>
                        <p class="card-text">
                            <span class="card-title-main"><strong>{{__('common.Priority')}}</strong>:</span>
                            <span class="card-content">{{$investStrategy->priority}}</span>
                        </p>
                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.MinAmount')}}</strong>:</span>
                            <span class="card-content">{{amount($investStrategy->min_amount)}}</span>
                        </p>
                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.MaxAmount')}}</strong>:</span>
                            <span class="card-content">{{amount($investStrategy->max_amount)}}</span>
                        </p>
                        <p class="card-text">
                                    <span
                                        class="card-title-main"><strong>{{__('common.MinInterestRate')}}</strong>:</span>
                            <span class="card-content">{{rate($investStrategy->min_interest_rate)}}</span>
                        </p>
                        <p class="card-text">
                                    <span
                                        class="card-title-main"><strong>{{__('common.MaxInterestRate')}}</strong>:</span>
                            <span class="card-content">{{rate($investStrategy->max_interest_rate)}}</span>
                        </p>

                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.MinLoanPeriod')}}</strong>:</span>
                            <span
                                class="card-content">{{$investStrategy->min_loan_period ? $investStrategy->min_loan_period .' m.' : ''}}</span>
                        </p>
                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.MaxLoanPeriod')}}</strong>:</span>
                            <span
                                class="card-content">{{$investStrategy->max_loan_period ? $investStrategy->max_loan_period .' m.' : '' }}</span>
                        </p>
                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.LoanType')}}</strong>:</span>
                            <span class="card-content">{{loanTypeJson($investStrategy->loan_type)}}</span>
                        </p>
                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.LoanPaymentStatus')}}</strong>:</span>
                            <span
                                class="card-content">{{loanPaymentStatusJson($investStrategy->loan_payment_status)}}</span>
                        </p>
                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.PortfolioSize')}}</strong>:</span>
                            <span
                                class="card-content">{{amount($investStrategy->max_portfolio_size)}}</span>
                        </p>
                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.CreatedAt')}}</strong>:</span>
                            <span class="card-content">{{showDate($investStrategy->created_at)}}</span>
                        </p>
                        <p class="card-text">
                                        <span
                                            class="card-title-main"><strong>{{__('common.OutstandingInvestment')}}</strong>:</span>
                            <span
                                class="card-content">{{ amount($investStrategy->getOutstandingInvestment())}}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-7 col-lg-6 col-md-6" style="min-width: 20vw; width: auto">
                <h3 class="card-header pt-4 pl-4">
                    <b>{{__('common.LoansInvestedIn')}}</b>
                    <b class="pl-3 float-right">{{__('common.Loans')}} - {{$loans->total()}}</b>
                </h3>
                <div class="table-responsive">

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center ">
                                {{__('common.LoanId')}}
                            </th>

                            <th scope="col" class="text-center ">
                                {{__('common.InvestedAmount')}}
                            </th>
                            <th scope="col" class="text-center ">
                                {{__('common.PercentFunded')}}
                            </th>
                        </tr>
                        </thead>
                        <tbody id="table-invest-strategy" class="text-center">
                        @include('admin::invest-strategy.loan-list')
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>
@push('scripts')
    <script type="text/javascript" src="{{ asset('js/jsGrid.js') }}"></script>
    <script>
        url = document.location.toString();

        if (url.split('#')[1] === 'overview' || url.split('#')[1] === undefined) {
            loadSimpleDataGrid('{{ route('admin.invest-strategy.refreshLoan' ,$investStrategy->invest_strategy_id) }}', $("#investForm"), $("#table-invest-strategy"));

        }
    </script>
@endpush

