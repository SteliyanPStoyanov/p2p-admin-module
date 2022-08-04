@php
    $onMarketArray = $loansOnMarket->keyBy('investment_id')->toArray();
    $inCartArray = $loansInCart->keyBy('investment_id')->toArray();
@endphp
<table class="ui table available-loans-table" id="myInvestmentsTable">
    <thead>
    @include('profile::my-invest.sorting.table-head')
    </thead>
    <tbody>
    @foreach($investments as $investment)
        @php
            $loan = $investment->loan;
        @endphp
        <tr class="dataRow">
            <td class="center aligned country" style="padding: .6em 0.5em">
                <div class="mobile-table-title">{{__('common.Country')}}</div>
                <div class="mobile-table-content">
                    <img class="country-flag-circle" alt="country-flag"
                         src="{{ assets_version(url('/') . '/images/countries/' . mb_strtolower($investment->loan->country->name) . '-flag-round-icon-32.png') }}">
                    <div class="country-name-mobile">{{ $investment->loan->country->name }}</div>
                </div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanId')}}</div>
                <div class="mobile-table-content">
                    <a href="{{route('profile.invest.view', $loan->loan_id)}}"
                       target="_blank" class="table-invest-link">{{ $loan->loan_id }}</a></div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.InvestmentDate')}}</div>
                <div class="mobile-table-content">{{showDate($investment->investment_created_at)}}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanType')}}</div>
                <div class="mobile-table-content">{{loanType($investment->type)}}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanOriginator')}}</div>
                <div class="mobile-table-content">{{ $loan->originator->name }}</div>
            </td>
            <td class="center aligned interest-rate">
                <div class="mobile-table-title">{{__('common.InterestRate')}}</div>
                <div class="mobile-table-content">{{ rate($investment->interest_rate_percent) }}</div>
            </td>
            <td class="center aligned term-col">
                <div class="mobile-table-title">{{__('common.Term')}}</div>
                <div class="mobile-table-content">{{termFormat($investment->final_payment_date)}}</div>
            </td>
            <td class="center aligned loan-amount">
                <div class="mobile-table-title">{{__('common.LoanAmount')}}</div>
                <div class="mobile-table-content">{{amount($loan->amount)}}</div>
            </td>
            <td class="center aligned loan-amount">
                <div class="mobile-table-title">{{__('common.InvestedAmount')}}</div>
                <div class="mobile-table-content">{{amount($investment->amount)}}</div>
            </td>
            <td class="center aligned term-col">
                <div class="mobile-table-title">{{__('common.ReceivedPayments')}}</div>
                <div class="mobile-table-content">{{amount($investment->received_amount)}}</div>
            </td>
            <td class="center aligned loan-amount">
                <div class="mobile-table-title">{{__('common.OutstandingInvestment')}}</div>
                <div class="mobile-table-content"
                     data-sum="{{ $investment->invested_sum }}">{{amount($investment->invested_sum)}}</div>
            </td>
            <td class="center aligned loan-payment-status-width pr-1 pl-1">
                <div class="mobile-table-title">{{__('common.LoanPaymentStatus')}}</div>
                <div class="mobile-table-content">{{ payStatus($loan->payment_status, $loan)}} </div>
            </td>
            @if(session($cacheKey . '.loan.status') != \Modules\Common\Entities\Loan::STATUS_REPAID)
                <td class="hide-sell-finish" style="padding: 5px 0">
                    @if($loansOnMarket->contains('investment_id', $investment->investment_id))
                        @include('profile::my-invest.sale.on-market-form-button')
                    @elseif($loansInCart->contains('investment_id', $investment->investment_id))
                        @include('profile::my-invest.sale.on-card-form-button')
                    @else
                        @include('profile::my-invest.sale.sale-form-button')
                    @endif
                </td>
            @endif
        </tr>
    @endforeach
    @if(!empty($investmentsTotalSum))
        <tr class="my-investment-total" style="height: 40px;">
            <td class="left aligned font-weight-bolder">
                Total:
            </td>
            <td colspan="7">
            </td>
            <td class="center aligned font-weight-bolder loan-amount no-wrap">
                <div class="mobile-table-title">{{__('common.InvestedAmount')}}</div>
                <div class="mobile-table-content">{{amount($investmentsTotalSum->sum('amount'))}}</div>
            </td>
            <td class="center aligned font-weight-bolder loan-amount no-wrap" style="padding: 0 10px !important;">
                <div class="mobile-table-title">{{__('common.ReceivedPayments')}}</div>
                <div class="mobile-table-content">{{amount($investmentsTotalSum->sum('received_amount'))}}</div>
            </td>
            <td class="center aligned font-weight-bolder loan-amount no-wrap">
                <div class="mobile-table-title">{{__('common.OutstandingInvestment')}}</div>
                <div class="mobile-table-content">{{amount($investmentsTotalSum->sum('invested_sum'))}}</div>
            </td>
            <td @if(session($cacheKey . '.loan.status') != \Modules\Common\Entities\Loan::STATUS_REPAID) colspan="2" @endif></td>
        </tr>
    @endif
    <tr id="pagination-nav" class="position-relative mt-0 myinvest-pagination">
        <td colspan="13">
            <input id="totalLoansView" value="{{$investments->total()}}" type="hidden">
            <input id="totalLoansCount" value="{{$totalInvestments}}" type="hidden">
            <input id="loanStatusSell" value="{{session($cacheKey . '.loan.status')}}" type="hidden">

            {{ $investments->onEachSide(1)->links() }}
            <form class="card-body float-right pt-1  hide-on-tablet"
                  action="{{ route('profile.invest') }}"
                  method="PUT">
                @csrf
                <span class="d-inline-block float-left">Results</span>
                {{-- multiple filter --}}
                <select class="form-control d-inline-block float-left w-25 ml-2 pl-2 pr-0 py-0 noClear" name="limit"
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
                {{-- multiple filter --}}
            </form>
        </td>
    </tr>

    </tbody>
</table>
