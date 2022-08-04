
@foreach($loans as $loan)
    <tr>
        <td>{{ $loan->country->name }}</td>
        <td><a href="{{route('admin.loans.overview', $loan->loan_id)}}">{{ $loan->loan_id }}</a></td>
        <td>{{ $loan->created_at != null ? showDate($loan->created_at, 'H:i') : '' }}</td>
        <td>{{ loanType($loan->type) }}</td>
        <td>{{$loan->originator->name}}</td>
        <td>{{ amount($loan->amount) }}</td>
        <td>{{ amount($loan->amount_available) }}</td>
        <td>{{ $loan->interest_rate_percent }}%</td>
        <td>{{ $loan->period }}</td>
        <td>{{ amount($loan->invested_sum) }}</td>
        <td>{{ Modules\Common\Libraries\Calculator\Calculator::round($loan->invested_percent) }}&percnt;</td>
        <td>{{ $loan->status }}</td>
        <td>{{ $loan->getPaymentStatus() }}</td>
        <td class="tableRow">{{ $loan->unlisted ? __('common.Unlisted') : __('common.Listed') }}</td>
    </tr>
@endforeach

<tr id="pagination-nav">
    <td colspan="14">
        {{ $loans->onEachSide(1)->links() }}
    </td>
</tr>

