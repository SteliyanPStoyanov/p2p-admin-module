<table>
    <tbody>
    <tr>
        <th>Loan ID</th>
        <th>Afranga Amount</th>
        <th>Amount Available</th>
        <th>Amount Invested</th>
        <th>Bought Percent</th>
        <th>Problem description</th>
    </tr>
    @foreach($data as $item)
        <tr>
            <th>{{$item->loan_id}}</th>
            <th>{{$item->amount_afranga}}</th>
            <th>{{$item->amount_available}}</th>
            <th>{{$item->amount_invested}}</th>
            <th>{{$item->percent}}</th>
            <th>{{$item->reason}}</th>
        </tr>
    @endforeach
    </tbody>
</table>
