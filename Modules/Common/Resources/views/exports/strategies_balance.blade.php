<table>
    <tbody>
    <tr>
        <td>Investor ID</td>
        <td>Strategy ID</td>
        <td>Strategy Name</td>
        <td>Reinvest</td>
        <td>Max Portfolio Size</td>
        <td class="grey" style="background-color: #b3cac7;">Portfolio Size</td>
        <td>Total Invested</td>
        <td class="yellow" style="background-color: #f6ecc7">Total Received</td>
        <td>Total Invested Investments</td>
        <td class="yellow" style="background-color: #f6ecc7">Total Received Inv Installments</td>
        <td class="grey" style="background-color: #b3cac7;">Total Outstanding Inv Installments</td>
        <td>Lost Installments Payments</td>
        <td>Totally Fine</td>
    </tr>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['investor_id'] }}</td>
            <td>{{ $item['strategy_id'] }}</td>
            <td>{{ $item['strategy_name'] }}</td>
            <td>{{ $item['reinvest'] }}</td>
            <td>{{ $item['max_portfolio_size'] }}</td>
            <td class="grey" style="background-color: #b3cac7;">{{ $item['portfolio_size'] }}</td>
            <td>{{ $item['total_invested'] }}</td>
            <td class="yellow" style="background-color: #f6ecc7">{{ $item['total_received'] }}</td>
            <td>{{ $item['total_invested_investments'] }}</td>
            <td class="yellow" style="background-color: #f6ecc7">{{ $item['total_received_inv-installments'] }}</td>
            <td class="grey" style="background-color: #b3cac7;">{{ $item['total_outstanding_inv-installments'] }}</td>
            <td>{{ $item['lost_installments_payments'] }}</td>
            @if($item['totally_fine'] == 'TRUE')
                <td>{{ $item['totally_fine'] }}</td>
            @else
                <td class="red" style="background-color: #ff6d6d;">{{ $item['totally_fine'] }}</td>
            @endif

        </tr>
    @endforeach
    </tbody>
</table>
