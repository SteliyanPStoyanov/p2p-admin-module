<table>
    <tbody>
        <tr>
            <td>{{ ucfirst ($reportType) }} Investments Report</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Settlement date</td>
            <td>{{ $data->date }}</td>
        </tr>
        <tr>
            <td>Lender Group</td>
            <td>{{ $data->originatorName }}</td>
        </tr>
        <tr>
            <td>Values</td>
            <td>{{ $data->currency }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Net settlement</td>
            <td>{{ amountReport($data->net_settlement) }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Opening investments as at {{ formatDate($data->from,'d/m/Y') }}</td>
            <td>{{ amountReport($data->open_balance) }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Investments in loans</td>
            <td>{{ amountReport($data->total_invested_amount) }}</td>
        </tr>
        <tr>
            <td>Principal rebuy</td>
            <td>{{ amountReport($data->rebuy_principal) }}</td>
        </tr>
        <tr>
            <td>Principal payment</td>
            <td>{{ amountReport($data->repaid_principal) }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Closing investments as at {{ formatDate($data->to,'d/m/Y') }}</td>
            <td>{{ amountReport($data->close_balance) }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Interest on principal rebuy</td>
            <td>{{ amountReport($data->rebuy_interest) }}</td>
        </tr>
        <tr>
            <td>Interest payment</td>
            <td>{{ amountReport($data->repaid_interest) }}</td>
        </tr>
        <tr>
            <td>Late interest on rebuy</td>
            <td>{{ amountReport($data->rebuy_late_interest) }}</td>
        </tr>
        <tr>
            <td>Late interest payment</td>
            <td>{{ amountReport($data->repaid_late_interest) }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Stats</td>
            <td></td>
        </tr>
        <tr>
            <td>Net increase/decrease in investments</td>
            <td>{{ amountReport($data->net_invested_amount) }}</td>
        </tr>
        <tr>
            <td>Number of investments made</td>
            <td>{{ $data->investments_count }}</td>
        </tr>
        <tr>
            <td>Average investment</td>
            <td>{{ amountReport($data->avg_investment) }}</td>
        </tr>
        <tr>
            <td>Uninvested funds</td>
            <td>{{ amountReport($data->univested_funds) }}</td>
        </tr>
    </tbody>
</table>
