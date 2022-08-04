<table>
    <thead>
    <tr>
        <th style="height: 50px; padding: 5pt 0;">Country</th>
        <th>Loan ID</th>
        <th>Issue Date</th>
        <th>Loan type</th>
        <th>Loan <br> Originator</th>
        <th>Interest rate</th>
        <th>Remaining <br> Term</th>
        <th>Loan <br> Amount</th>
        <th>Investment <br> Date</th>
        <th>Invested <br> amount</th>
        <th>Received <br> payments</th>
        <th>Outstanding <br> Investment</th>
        <th>Loan Status</th>
        <th>Buyback <br> Guarantee</th>
        <th>Currency</th>
    </tr>
    </thead>
    <tbody>
    @forelse($myInvestment as $investment)
        <tr>
            <td style="padding: 2pt 0; height: 20px;">{{ $investment->loan->country->name }}</td>
            <td>{{ $investment->loan->loan_id }}</td>
            <td>{{PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel(
                    Carbon\Carbon::parse($investment->loan->lender_issue_date)
                )}}</td>
            <td>{{ loanType($investment->type)}} </td>
            <td>{{ $investment->loan->originator->name }}</td>
            <td>{{ rateExport($investment->interest_rate_percent) }}</td>
            <td>{{ termFormat($investment->final_payment_date) }}</td>
            <td>{{ $investment->loan->amount }}</td>
            <td>{{ PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel(
                    Carbon\Carbon::parse(showDate($investment->investment_created_at))
                    ) }}</td>
            <td>{{ $investment->amount }}</td>
            <td>{{  $investment->received_amount }}</td>
            <td>{{ $investment->invested_sum }}</td>
            <td>{{ payStatus($investment->loan->payment_status, $investment->loan )}}</td>
            <td>
                @if($investment->loan['buyback']  == 1 )
                    Yes
                @else
                    No
                @endif
            </td>
            <td>{{ strtoupper($currency) }}</td>
        </tr>
    @empty
        <tr>

        </tr>
    @endforelse
    </tbody>
</table>
