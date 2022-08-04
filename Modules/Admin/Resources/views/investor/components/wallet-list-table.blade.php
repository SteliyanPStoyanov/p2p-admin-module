<table class="table">
    <tr>
        <th scope="col">{{__('common.TransactionID')}}</th>
        <th scope="col">{{__('common.DateTime')}}</th>
        <th scope="col">{{__('common.Amount')}}</th>
        <th scope="col">{{__('common.Type')}}</th>
        <th scope="col">{{__('common.From')}}</th>
        <th scope="col">{{__('common.To')}}</th>
        <th scope="col">{{__('common.Details')}}</th>
    </tr>
    <tbody id="investorWalletTable">
    @foreach($investorTransactions as $investorTransaction)
        <tr>
            <td>{{ $investorTransaction->transaction_id }}</td>
            <td>{{ $investorTransaction->created_at }}</td>
            <td>{{ amount($investorTransaction->amount) }}</td>
            <td>{{ \Modules\Common\Entities\Transaction::getAdminLabel($investorTransaction->type) }}</td>
            <td>{{ $investorTransaction->from }}</td>
            <td>{{ $investorTransaction->to }}</td>
            <td>
                @php

                if (!empty($investorTransaction->loan_id)) {
                    if ($investorTransaction->type == \Modules\Common\Entities\Transaction::TYPE_INSTALLMENT_REPAYMENT) {
                        $details = preg_replace(
                             '/#(\d+), loan #(\d+)/',
                             "<a target=\"_blank\" href=\"/admin/loans/" . $investorTransaction->loan_id . "#investor-instalments\">#$1</a>, loan <a target=\"_blank\" href=\"/admin/loans/" . $investorTransaction->loan_id . "\">#$2</a>",
                             $investorTransaction->details
                             );
                    } else {
                        $details = preg_replace(
                             '/#(\d+)/',
                             "<a target=\"_blank\" href=\"/admin/loans/" . $investorTransaction->loan_id . "\">#$1</a>",
                             $investorTransaction->details
                             );
                    }
                } else {
                   $details = $investorTransaction->details;
                }

                @endphp
                {!! $details !!}
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="10">
            {{ $investorTransactions->links() }}
        </td>
    </tr>
    </tfoot>
</table>
