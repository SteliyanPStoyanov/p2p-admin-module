@foreach($investStrategies as $strategy)
    <tr class="reorder-item-list" data-id="{{ $strategy->priority }}">
        <td class="center aligned pl-3 pr-3">
            <div class="mobile-table-title">{{__('common.Priority')}}</div>
            <div class="mobile-table-content">
                {{ $strategy->priority }}
            </div>
        </td>
        <td class="center aligned pl-3 pr-3 text-left">
            <div class="mobile-table-title">{{__('common.StrategyName')}}</div>
            <div class="mobile-table-content">
                {{ $strategy->name }}
            </div>
        </td>
        <td class="center aligned pl-3 pr-3">
            <div class="mobile-table-title">{{__('common.InterestRateAIMobile')}}</div>
            <div class="mobile-table-content no-wrap">
                {{autoInvestRate($strategy->min_interest_rate,$strategy->max_interest_rate)}}
            </div>
        </td>
        <td class="center aligned pl-3 pr-3">
            <div class="mobile-table-title">{{__('common.RemainingLoanTermMobile')}}</div>
            <div class="mobile-table-content no-wrap">
                {{autoInvestPeriod($strategy->min_loan_period ,$strategy->max_loan_period)}}
            </div>
        </td>
        <td class="center aligned pl-3 pr-3">
            <div class="mobile-table-title">{{__('common.InvestmentInOneLoanMobile')}}</div>
            <div class="mobile-table-content">
                @if(empty($strategy->max_amount))
                    from
                @endif
                @if(!empty($strategy->min_amount))
                    {{ amount($strategy->min_amount) }}
                @endif
                @if(empty($strategy->min_amount))
                    up to
                @endif
                @if(!empty($strategy->max_amount) || !empty($strategy->min_amount))

                @endif
                @if(!empty($strategy->max_amount))
                    - <br> {{ amount($strategy->max_amount) }}
                @endif

            </div>
        </td>
        <td class="center aligned pl-3 pr-3">
            <div class="mobile-table-title">{{__('common.NumberOfInvestmentsMobile')}}</div>
            <div class="mobile-table-content">
                @if(isset($strategy->count))
                    {{ $strategy->count }}
                @else
                    0
                @endif
            </div>
        </td>
        <td class="center aligned pl-3 pr-3">
            <div class="mobile-table-title">{{__('common.TargetPortfolioSizeMobile')}}</div>
            <div class="mobile-table-content">
                {{ amount($strategy->max_portfolio_size ?: 0) }}
            </div>
        </td>
        <td class="center aligned pl-3 pr-3">
            <div class="mobile-table-title">{{__('common.InvestedAmount')}}</div>
            <div class="mobile-table-content text-nowrap">
                {{ amount(($strategy->amount ?: 0)) }}
            </div>
        </td>
        <td class="center aligned" style="min-width: 250px" colspan="4">
            <div class="mobile-table-content row">
                <div class="col sorting-auto-invest">

                    <i class="fa fa-sort-asc"
                       onclick="priority($(this),'up',{{$strategy->priority}} ,{{$strategy->invest_strategy_id}})"
                       aria-hidden="true"></i>

                    <i class="fa fa-sort-desc"
                       onclick="priority($(this),'down',{{$strategy->priority}} ,{{$strategy->invest_strategy_id}})"
                       aria-hidden="true"></i>

                </div>
                <div class="col">
                    @if($strategy->active == 1)
                        <a onclick="return activateDeactivateStrategy(this);"
                           href="{{route('profile.autoInvest.disable', $strategy->invest_strategy_id)}}">
                            Stop
                        </a>
                    @else
                        <a onclick="return activateDeactivateStrategy(this);"
                           href="{{route('profile.autoInvest.enable', $strategy->invest_strategy_id)}}">
                            Activate
                        </a>
                    @endif

                </div>
                <div class="col">
                    <a href="{{route('profile.autoInvest.edit', $strategy->invest_strategy_id)}}">Edit</a>
                </div>
                <div class="col">
                    <a onclick="return activateDeactivateStrategy(this);"
                       href="{{route('profile.autoInvest.delete', $strategy->invest_strategy_id)}}">Delete</a>
                </div>
            </div>
        </td>
    </tr>
@endforeach


