@php
$transactionEntity =  \Modules\Common\Entities\Transaction::class;
@endphp
<div class="col-lg-8 trans-details text-black account-statement-details">
    <h3 class="mt-5 mb-2 text-black">{{__('common.TransactionSummary')}}</h3>


    @if($summary['walletBalance'] and count($summary['transaction']) > 0)
        <div class="row border-bottom pb-2 pt-2">

            <div class="col-lg-5 pl-0">
                {{__('common.OpeningBalance')}} {{showDate($summary['walletBalance']['start']['date']) }}
            </div>
            <div class="col-lg-3 offset-4 pr-0">
                <div class="edit-profile w-100 text-right pr-0">
                    {{amount($summary['walletBalance']['start']['uninvested'])}}
                </div>
            </div>
        </div>
        @php
            $total = 0;
        @endphp

        @foreach($summary['transaction'] as $label => $row)
            @php
                if (
                    $row->type == $transactionEntity::TYPE_WITHDRAW ||
                    $row->type == $transactionEntity::TYPE_INVESTMENT ||
                    $row->type == $transactionEntity::TYPE_SECONDARY_MARKET_BUY
                ) {
                    $total = bcsub($total, floatval($row->sum));
                } else {
                    $total = bcadd($total, floatval($row->sum));
                }
            @endphp

            <div class="row border-bottom pb-2 pt-2 ">
                <div class="col-lg-5 pl-0">
                    {{ $label }}
                </div>

                <div class="col-lg-3 offset-4 pr-0">
                    <div
                        @if(
                            $row->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                            $row->sum < 0
                        )
                            class="edit-profile w-100 text-right pr-0 direction-{{\Modules\Common\Entities\Transaction::DIRECTION_IN}}"
                        @elseif(
                                $row->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                                $row->sum > 0
                        )
                            class="edit-profile w-100 text-right pr-0 direction-{{\Modules\Common\Entities\Transaction::DIRECTION_OUT}}"
                        @else
                            class="edit-profile w-100 text-right pr-0 direction-{{$row->direction}}"
                        @endif
                    >
                        {{ amount(abs($row->sum))}}
                    </div>
                </div>
            </div>

        @endforeach

        @if(!empty($summary['walletBalance']['end']))

            <div class="row pb-2 pt-2">
                <div class="col-lg-5 pl-0">
                    {{__('common.ClosingBalance')}} {{showDate($summary['walletBalance']['end']['date']) }}
                </div>
                <div class="col-lg-3 offset-4 pr-0">
                    <div class="edit-profile w-100 text-right pr-0">
                        {{amount($summary['walletBalance']['end']['uninvested'])}}
                    </div>
                </div>
            </div>
        @else
            <div class="row pb-2 pt-2">
                <div class="col-lg-5 pl-0">
                    {{__('common.ClosingBalance')}} {{showDate($summary['walletBalance']['end']['date']) }}
                </div>
                <div class="col-lg-3 offset-4 pr-0">
                    <div class="edit-profile w-100 text-right pr-0">
                        {{$total != 0 ? amount($summary['walletBalance']['end']['uninvested'] - $total) : amount($summary['walletBalance']['end']['uninvested'])}}
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="row border-bottom pb-2 pt-2">

            <div class="col-lg-5 pl-0">
                {{__('common.OpeningBalance')}} {{showDate($summary['walletBalance']['start']['date']) }}
            </div>
            <div class="col-lg-3 offset-4 pr-0">
                <div class="edit-profile w-100 text-right pr-0">
                    {{amount($summary['walletBalance']['start']['uninvested'])}}
                </div>
            </div>
        </div>
        <div class="row border-bottom pb-2 pt-2">
            {{__('common.NoTransactionsFound')}}
        </div>
        <div class="row pb-2 pt-2">
            <div class="col-lg-5 pl-0">
                {{__('common.ClosingBalance')}} {{showDate($summary['walletBalance']['end']['date']) }}
            </div>
            <div class="col-lg-3 offset-4 pr-0">
                <div class="edit-profile w-100 text-right pr-0">
                    {{amount($summary['walletBalance']['end']['uninvested'])}}
                </div>
            </div>
        </div>
    @endif
</div>
