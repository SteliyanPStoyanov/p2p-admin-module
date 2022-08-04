@foreach($investStrategies as $investStrategy)

    <tr>
        <td class="text-center">
            <a href="{{route('admin.invest-strategy.overview', $investStrategy->invest_strategy_id)}}">{{ $investStrategy->invest_strategy_id }}</a>
        </td>
        <td class="text-center">{{$investStrategy->investor->investor_id}}</td>
        <td class="text-center">{{$investStrategy->name}}</td>
        <td class="text-center">{{$investStrategy->priority}}</td>
        <td class="text-center">{{ amount($investStrategy->min_amount ? $investStrategy->min_amount : 0)}}</td>
        <td class="text-center">{{ amount($investStrategy->max_amount ? $investStrategy->max_amount : 0)}}</td>
        <td class="text-center">{{ rate($investStrategy->min_interest_rate)}}</td>
        <td class="text-center">{{ rate($investStrategy->max_interest_rate)}}</td>
        <td class="text-center">{{ $investStrategy->min_loan_period ? $investStrategy->min_loan_period . ' m.' : ''}}</td>
        <td class="text-center">{{ $investStrategy->max_loan_period ? $investStrategy->max_loan_period .' m.' : ''}}</td>
        <td class="text-center">{{ loanTypeJson($investStrategy->loan_type)}}</td>
        <td class="text-center">{{ loanPaymentStatusJson($investStrategy->loan_payment_status)}}</td>
        <td class="text-center">{{ amount($investStrategy->max_portfolio_size)}}</td>
        <td class="text-center">{{ amount($investStrategy->getOutstandingInvestment())}}</td>
        <td class="text-center">{{ showDate($investStrategy->created_at)}}</td>
        <td class="text-center active-{{$investStrategy->active == 1 ? '1' : '0'}}">
            {{ $investStrategy->active == 1 ? 'Active' : 'Inactive'}}
        </td>
        <td class="text-center deleted-{{$investStrategy->deleted == 0 ? '0' : '1'}}">
            {{ $investStrategy->deleted == 0 ? 'Active' : 'Deleted'}}
        </td>
    </tr>
@endforeach


<tr id="pagination-nav">
    <td colspan="17">
        {{ $investStrategies->onEachSide(1)->links() }}
    </td>
</tr>

