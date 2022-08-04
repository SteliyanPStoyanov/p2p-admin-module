@php
$stringNo = html_entity_decode("&lt;strong style=&quot;color: rgb(217, 217, 217); line-height: 14px; font-size:23px;&quot;&gt;-&lt;/strong&gt;");
@endphp
<table class="ui table available-loans-table" id="investTable">
    <thead>
    <tr>
        <th scope="col" class="center aligned">{{__('common.Date')}}</th>
        <th scope="col" class="center aligned">{{__('common.Principal')}}</th>
        <th scope="col" class="center aligned">{{__('common.Interest')}}</th>
        <th scope="col" class="center aligned">{{__('common.Total')}}</th>
        <th scope="col" class="center aligned">{{__('common.Status')}}</th>
        <th scope="col" class="center aligned">{{__('common.PaymentDate')}}</th>
    </tr>
    </thead>
    <tbody id="investsTable">
    @foreach($installments as $installment)
        <tr>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.Date')}}</div>
                <div class="mobile-table-content">{{ showDate($installment->due_date)}}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.Principal')}}</div>
                <div class="mobile-table-content">
                    {!!  paidBeforeListing($installment) ? $stringNo : amount($installment->principal) !!}
                </div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.Interest')}}</div>
                <div class="mobile-table-content">
                    {!! paidBeforeListing($installment) ? $stringNo : amount($installment->interest) !!}
                </div>
            </td>
            <td class="text-nowrap center aligned">
                <div class="mobile-table-title">{{__('common.Total')}}</div>
                <div class="mobile-table-content">
                    {!! paidBeforeListing($installment) ? $stringNo : amount($installment->total) !!}
                </div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.Status')}}</div>
                <div class="mobile-table-content">{{ paymentStatus($installment->payment_status) }}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.PaymentDate')}}</div>
                <div class="mobile-table-content">
                    {{paidBeforeListing($installment) ? __('common.PaidBeforeListing') : showDate($installment->paid_at)}}
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
