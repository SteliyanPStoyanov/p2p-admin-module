<table>
    <tbody>
    <tr>
        <td>Investor ID</td>
        <td>First Name</td>
        <td>Last Name</td>
        <td class="yellow" style="background-color: #ffe994;">Deposited</td>
        <td class="yellow" style="background-color: #ffe994;">Wallet Deposited</td>
        <td class="green" style="background-color: #afd095">Invested Total</td>
        <td class="green" style="background-color: #afd095">Wallet Invested</td>
        <td class="green" style="background-color: #afd095">Outstanding Principal Investor Plans</td>
        <td class="green" style="background-color: #afd095">Actual Invested Amount</td>
        <td>Repaid/Rebuy</td>
        <td>Wallet Income</td>
        <td>Repaid principal</td>

        <td>SM Investment</td>
        <td>SM Investment Premium</td>
        <td>SM Sale</td>
        <td>SM Sale Premium</td>

        <td class="aquamarine" style="background-color: #7FFFD4">Bonus</td>
        <td class="aquamarine" style="background-color: #7FFFD4">Wallet Bonus</td>
        <td class="orange" style="background-color: #ffaa95">Balance</td>
        <td class="orange" style="background-color: #ffaa95">Wallet Uninvested</td>
        <td>Withdraw</td>
        <td>Wallet Withdraw</td>
        <td class="purple" style="background-color: #dd7dd6">Wallet Total Amount</td>
        <td class="purple" style="background-color: #dd7dd6">Actual Total Amount</td>
    </tr>
    @foreach($data as $item)
        <tr>
            <td>{{ $item['investor_id'] }}</td>
            <td>{{ $item['first_name'] }}</td>
            <td>{{ $item['last_name'] }}</td>
            <td class="yellow" style="background-color: #ffe994;">{{ $item['deposited'] }}</td>
            <td class="yellow" style="background-color: #ffe994;">{{ $item['wallet_deposited'] }}</td>
            <td class="green" style="background-color: #afd095">{{ $item['invested'] }}</td>
            <td class="green" style="background-color: #afd095">{{ $item['wallet_invested'] }}</td>
            <td class="green" style="background-color: #afd095">{{ $item['outstanding_principal'] }}</td>
            <td class="green" style="background-color: #afd095">{{ $item['actual_invested_amount'] }}</td>
            <td>{{ $item['repayments'] }}</td>
            <td>{{ $item['wallet_income'] }}</td>
            <td>{{ $item['repaid_principal'] }}</td>

            <td>{{ $item['secondary_market_buy'] }}</td>
            <td>{{ $item['secondary_market_buy_premium'] }}</td>
            <td>{{ $item['secondary_market_sell'] }}</td>
            <td>{{ $item['secondary_market_sell_premium'] }}</td>

            <td class="aquamarine" style="background-color: #7FFFD4">{{ $item['bonus'] }}</td>
            <td class="aquamarine" style="background-color: #7FFFD4">{{ $item['wallet_bonus'] }}</td>
            <td class="orange" style="background-color: #ffaa95">{{ $item['balance'] }}</td>
            <td class="orange" style="background-color: #ffaa95">{{ $item['wallet_uninvested'] }}</td>
            <td>{{ $item['withdrawed'] }}</td>
            <td>{{ $item['wallet_withdraw'] }}</td>
            <td class="purple" style="background-color: #dd7dd6">{{ $item['wallet_total_amount'] }}</td>
            <td class="purple" style="background-color: #dd7dd6">{{ $item['actual_total_amount'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
