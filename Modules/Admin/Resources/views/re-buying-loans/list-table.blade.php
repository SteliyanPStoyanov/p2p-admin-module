<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Country')}}</th>
        <th scope="col">{{__('common.LoanStatus')}}</th>
        <th scope="col">{{__('common.LoanId')}}</th>
        <th scope="col">{{__('common.ListingDate')}}</th>
        <th scope="col">{{__('common.LoanType')}}</th>
        <th scope="col">{{__('common.Lender')}}</th>
        <th scope="col">{{__('common.LoanAmount')}}</th>
        <th scope="col">{{__('common.OutstandingAmount')}}</th>
        <th scope="col">{{__('common.InterestRate')}}</th>
        <th scope="col">{{__('common.Term')}}</th>
        <th scope="col">{{__('common.InvestedAmount')}}</th>
        <th scope="col">{{__('common.PercentFunded')}}</th>
        <th scope="col">{{__('common.PaymentStatus')}}</th>
        <th scope="col">{{__('common.ListingStatus')}}</th>
    </tr>
    </thead>
    <tbody id="administratorsTable">
    @foreach($loans as $loan)
        <tr>
            <td>{{ $loan->country->name }}</td>
            <td>{{ $loan->status }}</td>
            <td><a href="{{route('admin.loans.overview', $loan->loan_id)}}">{{ $loan->loan_id }}</a></td>
            <td>{{ $loan->created_at != null ? $loan->created_at->format('d-m-Y H:i') : '' }}</td>
            <td>{{ loanType($loan->type) }}</td>
            <td>{{ $loan->lender_id }}</td>
            <td>{{ amount($loan->amount) }}</td>
            <td>{{ amount($loan->amount_available) }}</td>
            <td>{{ rate($loan->interest_rate_percent) }}</td>
            <td>{{ $loan->period }}</td>
            <td>{{ amount($loan->getInvestorSharedAmount()) }}</td>
            <td>{{ rate($loan->getInvestorSharedPercent()) }}</td>
            <td>{{ $loan->payment_status }}</td>
            <td class="tableRow">{{ $loan->unlisted ? __('common.Unlisted') : __('common.Listed') }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="10">
            {{ $loans->onEachSide(1)->links() }}
        </td>
    </tr>
    </tfoot>
</table>
