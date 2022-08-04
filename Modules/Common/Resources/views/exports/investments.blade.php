<table>
    <tbody>
    <tr>
        <th>Investment Date</th>
        <th>Loan ID</th>
        <th>Listing Date</th>
        <th>Country</th>
        <th>Loan Type</th>
        <th>Lender</th>
        <th>Loan Balance</th>
        <th>Interest Rate</th>
        <th>Term</th>
        <th>Invested Amount</th>
        <th>Outstanding Investment</th>
        <th>Loan Status</th>
        <th>Payment Status</th>
        <th>Listing Status</th>
    </tr>
    @foreach($investments as $investment)
        <tr>
            <td>{{ formatDate($investment->investment_created_at , 'd.m.Y H:i:s')}}</td>
            <td> {{ $investment->loan_id}} </td>
            <td> {{ showDate($investment->loan_created_at) }} </td>
            <td> {{ $investment->country_name }} </td>
            <td> {{ loanType($investment->type) }} </td>
            <td> {{ $investment->originator_name }} </td>
            <td> {{ amount($investment->loan_remaining_principal) }} </td>
            <td> {{ rate($investment->interest_rate_percent) }} </td>
            <td> {{ termFormat($investment->final_payment_date) }} </td>
            <td> {{ amount($investment->amount) }} </td>
            <td> {{ amount($investment->invested_sum)}} </td>
            <td> {{ ucfirst($investment->status) }} </td>
            <td> {{ ucfirst($investment->payment_status) }} </td>
            <td> {{ $investment->unlisted == 1 ? 'Unlisted' : 'Listed' }} </td>
        </tr>
    @endforeach
    </tbody>
</table>
