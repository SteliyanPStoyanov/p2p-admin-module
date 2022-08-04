@foreach($transactions as $transaction)
    <tr>
        <td>{{ $transaction->transaction_id }}</td>
        <td>{{ $transaction->created_at != null ? showDate($transaction->created_at, 'H:i') : '' }}</td>
        <td>{{ $transaction->amount }}</td>
        <td>{{ $transaction->from }}</td>
        <td>{{ $transaction->to }}</td>
        <td>{{\Modules\Common\Entities\Transaction::getAdminLabel($transaction->type)}} </td>
        <td class="col-sm-auto">
            @php
                if (!empty($transaction->loan_id)) {
                    if ($transaction->type == \Modules\Common\Entities\Transaction::TYPE_INSTALLMENT_REPAYMENT) {
                        $details = preg_replace(
                             '/#(\d+), loan #(\d+)/',
                             "<a target=\"_blank\" href=\"/admin/loans/" . $transaction->loan_id . "#investor-instalments\">#$1</a>, loan <a target=\"_blank\" href=\"/admin/loans/" . $transaction->loan_id . "\">#$2</a>",
                             $transaction->details
                             );
                    } else {
                        $details = preg_replace(
                             '/#(\d+)/',
                             "<a target=\"_blank\" href=\"/admin/loans/" . $transaction->loan_id . "\">#$1</a>",
                             $transaction->details
                             );
                    }
                } else {
                   $details = $transaction->details;
                }

            @endphp
            {!! $details !!}
        </td>
    </tr>
@endforeach
<tr id="pagination-nav">
    <td colspan="10">
        {{ $transactions->links() }}
    </td>
</tr>
