<table>
    <thead>
    <tr>
        <th style="height: 40px; padding: 5pt 0;">Date</th>
        <th>Transaction ID</th>
        <th>Details</th>
        <th>Amount</th>
    </tr>
    </thead>
    <tbody>
    @forelse($accountStatements as $transaction)
        <tr>
            <td
                @if($transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM)
                style="padding: 2pt 0; height: 40px;"
                @else
                style="padding: 2pt 0; height: 20px;"
                @endif
            >
                {{ PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel(
                    Carbon\Carbon::parse($transaction->created_at)
                    ) }}
            </td>
            <td>{{$transaction->transaction_id}}</td>
            <td> @php
                    if(!empty($transaction->loan_id)){
                        echo 'Loan '. $transaction->loan_id . ' - ';
                    }
                @endphp
                {{ \Modules\Common\Entities\Transaction::getLabelForKey($transaction->type)}}
                @if($transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM)
                    <br>
                    @if($transaction->premium > 0)
                         +
                    @endif
                    {{ $transaction->premium }}% trans. ID {{$transaction->sm_transaction_id}}
                @endif
            </td>
            <td>
                @if($transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM)
                    @if(
                        $transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                        $transaction->direction == \Modules\Common\Entities\Transaction::DIRECTION_IN &&
                        $transaction->sum < 0
                    )
                        {{ -abs($transaction->sum)}}
                    @elseif(
                        $transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                        $transaction->direction == \Modules\Common\Entities\Transaction::DIRECTION_IN &&
                        $transaction->sum > 0
                    )
                        +{{ abs($transaction->sum) }}
                    @elseif(
                        $transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                        $transaction->direction == \Modules\Common\Entities\Transaction::DIRECTION_OUT && // buyer
                        $transaction->sum < 0
                    )
                        +{{ abs($transaction->sum) }}
                    @elseif(
                        $transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                        $transaction->direction == \Modules\Common\Entities\Transaction::DIRECTION_OUT &&
                        $transaction->sum > 0
                    )
                        {{ -abs($transaction->sum)}}
                    @elseif(
                        $transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                        $transaction->sum == 0
                    )
                        {{ abs($transaction->sum)}}
                    @endif

                @else
                    {{($transaction->direction == 'in') ? -abs($transaction->sum) : +abs($transaction->sum) }}
                @endif
            </td>
        </tr>
    @empty
        <tr>

        </tr>
    @endforelse
    </tbody>
</table>
