@php
    $inCartArray = $loansInSecondaryCart->keyBy('secondary_market_id')->toArray();
    $loansInSecondaryCartCount = 0;
    foreach($items as $item){

         if($loansInSecondaryCart->contains('secondary_market_id', $item->market_secondary_id)){
            $loansInSecondaryCartCount ++;
         }
    }
    $allInPage = ($items->count() == $loansInSecondaryCartCount) && $loansInSecondaryCartCount != 0;
    $someInPage = ($items->count() > $loansInSecondaryCartCount) && $loansInSecondaryCartCount != 0;
@endphp


<table class="ui table available-loans-table" id="investTable" style="font-size: 1rem;">
    <thead>
    @include('profile::secondary-market.sorting.table-head')
    </thead>
    <tbody id="investsTable">
    @foreach($items as $item)

        <tr class="dataRow">
            <td class="center aligned country">
                <div class="mobile-table-title">{{__('common.Country')}}</div>
                <div class="mobile-table-content">
                    <img class="country-flag-circle" alt="country-flag"
                         src="{{ url('/') }}/images/countries/{{ mb_strtolower($item->loan->country->name) }}-flag-round-icon-32.png">
                    <div class="country-name-mobile">{{ $item->loan->country->name }}</div>
                </div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanId')}}</div>
                <div class="mobile-table-content">
                    <a href="{{route('profile.invest.view', $item->loan->loan_id)}}"
                       target="_blank" class="table-invest-link">{{ $item->loan->loan_id }}
                    </a>
                </div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.InvestmentDate')}}</div>
                <div class="mobile-table-content">{{showDate($item->created_at)}}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanType')}}</div>
                <div class="mobile-table-content">{{loanType($item->loan->type)}}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanOriginator')}}</div>
                <div class="mobile-table-content">{{ $item->originator->name }}</div>
            </td>
            <td class="center aligned interest-rate">
                <div class="mobile-table-title">{{__('common.InterestRate')}}</div>
                <div class="mobile-table-content">{{ rate($item->loan->interest_rate_percent) }}</div>
            </td>
            <td class="center aligned term-col">
                <div class="mobile-table-title">{{__('common.Term')}}</div>
                <div class="mobile-table-content">{{termFormat($item->loan->final_payment_date)}}</div>
            </td>
            <td class="center aligned loan-payment-status-width">
                <div class="mobile-table-title">{{__('common.LoanPaymentStatus')}}</div>
                <div class="mobile-table-content">{{ payStatus($item->loan->payment_status, $item)}} </div>
            </td>
            <td class="center aligned loan-payment-status-width">
                <div class="mobile-table-title">{{__('common.AvailableForInvestment')}}</div>
                <div class="mobile-table-content">{{ amount($item->principal_for_sale)}} </div>
            </td>
            <td class="center aligned loan-payment-status-width">
                <div class="mobile-table-title">{{__('common.DiscountPremium')}}</div>

                <div class="mobile-table-content"
                     style="color: {{$premiumColor = ($item->premium < 0) ? '#009193' : (($item->premium <= 0) ?: '#FF7E79') }} ;"
                >{{$premiumPlus = ($item->premium > 0) ? '+' : '' }}{{ percentReport($item->premium)}} %
                </div>
            </td>
            <td class="center aligned loan-payment-status-width">
                <div class="mobile-table-title">{{__('common.Price')}}</div>
                <div data-sum="{{ $item->principal_for_sale }}"
                     class="mobile-table-content">{{ amount($item->price)}} </div>
            </td>

            <td class="center aligned  no-box hide-sell-finish" style="padding: .6em 0; min-height: 50px;">
                @if($loansInSecondaryCart->count() >= 1 && $loansInSecondaryCart->contains('secondary_market_id', $item->market_secondary_id))
                    @include('profile::secondary-market.invest.on-card-form-button')
                @else
                    @include('profile::secondary-market.invest.invest-single')
                @endif
            </td>
        </tr>
    @endforeach
    <tr id="pagination-nav" class="position-relative mt-0 invest-pagination">
        <div class="row">
            <td colspan="12">
                <input id="totalLoansCount" value="{{$items->total()}}" type="hidden">
                {{ $items->onEachSide(1)->links() }}
                <form class="card-body float-right pt-1 d-inline-block hide-on-tablet"
                      action="{{ route('profile.invest') }}"
                      method="PUT">
                    @csrf
                    <span class="d-inline-block float-left">Results</span>
                    <select class="form-control d-inline-block float-left w-25 ml-2 pl-2 pr-0 py-0 noClear"
                            style="margin-top:
                -7px" name="limit" id="maxRows">
                        <option
                            @if(session($cacheKey . '.limit') == 10 || empty(session($cacheKey . '.limit')))
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
        </div>
    </tr>

    </tbody>
</table>

