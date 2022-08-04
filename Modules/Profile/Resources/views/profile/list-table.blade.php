@include('profile::profile.summary-table')
<div class="col-lg-12 float-left ">
    <a id="exportBtn" class="mb-3">{{__('common.DownloadSelectedTransactions')}}</a>
</div>
@if ($transactions->count() > 0)
    @php
        $transactionEntity =  \Modules\Common\Entities\Transaction::class;
    @endphp
    <div class="col-lg-8 trans-details text-black account-statement-details mb-5">
        <h3 class="mt-5 mb-2 text-black">{{__('common.Details')}}</h3>
        <div class="table-responsive" id="accountStatementTable">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col" class="left aligned pl-2">
                        {{__('common.Date')}}
                    </th>
                    <th scope="col" class="center aligned details-title">
                        {{__('common.Details')}}
                    </th>

                    <th scope="col" class="aligned pr-0" style="text-align: right !important;">
                        @if(session($cacheKey . '.type') == $transactionEntity::PAY_TYPE_INTEREST)
                            {{__('common.Interest')}}

                        @elseif(session($cacheKey . '.type') == $transactionEntity::PAY_TYPE_PRINCIPAL)
                            {{__('common.Principal')}}
                        @else
                            {{__('common.Amount')}}
                        @endif
                    </th>

                </tr>
                </thead>
                <tbody id="investsTable">

                @foreach($transactions as $transaction)
                    <tr>
                        <td>
                            {{showDate($transaction->created_at) }}
                        </td>
                        <td class="details-content">
                            @if(isset($transaction->secondary_market_id) && $transaction->secondary_market_id)
                                {{__('common.TransactionID')}}
                                - {{$transaction->transaction_id}}

                                @if(!empty($transaction->loan_id))
                                    Loan <a target="_blank" href="{{route('profile.invest.view', $transaction->loan_id)}}">{{$transaction->loan_id}}</a>
                                @endif

                                {{ $transaction->details }}
                                @if($transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM)
                                    <br>
                                    @if($transaction->premium > 0)
                                         +
                                    @endif
                                    {{ $transaction->premium }}% trans. ID {{$transaction->sm_transaction_id}}
                                @endif
                            @else
                                {{__('common.TransactionID')}}
                                - {{$transaction->transaction_id}}
                                @php
                                    if(!empty($transaction->loan_id)){
                                        echo ' - Loan <a target="_blank" href="'.route('profile.invest.view' ,$transaction->loan_id).'">'. $transaction->loan_id .'</a>';
                                    }
                                @endphp
                                - {{ $transactionEntity::getLabelForKey($transaction->type)}}
                            @endif
                        </td>
                        <td>
                            <div
                                style="text-align: right !important;"
                                @if(
                                        $transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                                        $transaction->direction == \Modules\Common\Entities\Transaction::DIRECTION_IN &&
                                        $transaction->sum < 0
                                )
                                        class="mobile-table-content direction-{{\Modules\Common\Entities\Transaction::DIRECTION_IN}}"
                                @elseif(
                                        $transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                                        $transaction->direction == \Modules\Common\Entities\Transaction::DIRECTION_IN &&
                                        $transaction->sum > 0
                                )
                                        class="mobile-table-content direction-{{\Modules\Common\Entities\Transaction::DIRECTION_OUT}}"
                                @elseif(
                                        $transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                                        $transaction->direction == \Modules\Common\Entities\Transaction::DIRECTION_OUT && // buyer
                                        $transaction->sum < 0
                                )
                                        class="mobile-table-content direction-{{\Modules\Common\Entities\Transaction::DIRECTION_OUT}}"
                                @elseif(
                                        $transaction->type == \Modules\Common\Entities\Transaction::TYPE_SECONDARY_MARKET_PREMIUM &&
                                        $transaction->direction == \Modules\Common\Entities\Transaction::DIRECTION_OUT &&
                                        $transaction->sum > 0
                                )
                                        class="mobile-table-content direction-{{\Modules\Common\Entities\Transaction::DIRECTION_IN}}"
                                @else
                                        class="mobile-table-content direction-{{$transaction->direction}} | {{$transaction->transaction_id}}"
                                @endif
                            >
                                {{amount(abs($transaction->sum))}}

                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr id="pagination-nav" class="position-relative mt-0 invest-pagination">
                    <td colspan="10">
                        {{ $transactions->onEachSide(1)->links() }}
                        <form
                            class="card-body float-right pt-1 d-inline-block"
                            action="{{ route('profile.invest') }}"
                            method="PUT">
                            @csrf
                            <span class="d-inline-block float-left">Results</span>
                            <select class="form-control d-inline-block float-left w-25 ml-2 pl-2 pr-0 py-0 noClear"
                                    name="limit"
                                    id="maxRows"
                                    style="margin-top:-7px">
                                <option
                                    @if(session($cacheKey . '.limit') == 10)
                                    selected
                                    @endif
                                    class="paginationValueLimit" value="10">10
                                </option>
                                <option
                                    @if(session($cacheKey . '.limit') == 25)
                                    selected
                                    @endif
                                    class="paginationValueLimit" value="25">25
                                </option>
                                <option
                                    @if(session($cacheKey . '.limit') == 50)
                                    selected
                                    @endif
                                    class="paginationValueLimit" value="50">50
                                </option>
                                <option
                                    @if(session($cacheKey . '.limit') == 100)
                                    selected
                                    @endif
                                    class="paginationValueLimit" value="100">100
                                </option>
                                <option
                                    @if(session($cacheKey . '.limit') == 250)
                                    selected
                                    @endif
                                    class="paginationValueLimit" value="250">250
                                </option>
                            </select>
                        </form>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif
