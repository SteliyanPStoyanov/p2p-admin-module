@foreach($loans as $loan)
    <tr>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('static.HomePageAvailableLoansTableCountry')}}</div>
            <div class="mobile-table-content">
                <img class="country-flag-circle" alt="bulgaria-flag"
                     src="{{ assets_version(url('/') . '/images/countries/' . mb_strtolower($loan->country->name) . '-flag-round-icon-32.png') }}">
                <div class="center aligned table-country-name">{{ $loan->country->name }}</div>
            </div>

        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('static.HomePageAvailableLoansTableLoanID')}}</div>
            <div class="mobile-table-content">
                <a target="_blank" class="table-invest-link" href="{{route('profile.invest.view' ,$loan->loan_id)}}">{{ $loan->loan_id
                }}</a>
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('static.HomePageAvailableLoansTableIssueDate')}}</div>
            <div class="mobile-table-content">
                {{showDate($loan->lender_issue_date) }}
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('static.HomePageAvailableLoansTableLoanType')}}</div>
            <div class="mobile-table-content">
                {{loanType($loan->type)}}
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('static.HomePageAvailableLoansTableLoanOriginatorMobile')}}</div>
            <div class="mobile-table-content">
                {{ $loan->originator->name }}
            </div>
        </td>

        <td class="center aligned">
            <div class="mobile-table-title">{{__('static.HomePageAvailableLoansTableInterestRateMobile')}}</div>
            <div class="mobile-table-content">
                {{ rate($loan->interest_rate_percent) }}
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('static.HomePageAvailableLoansTableTerm')}}</div>
            <div class="mobile-table-content">
                {{termFormat($loan->final_payment_date)}}
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('static.HomePageAvailableLoansTableLoanAmountMobile')}}</div>
            <div class="mobile-table-content">
                {{ amount($loan->amount) }}
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('static.HomePageAvailableLoansTableAvailableMobile')}}</div>
            <div class="mobile-table-content">
                {{ amount($loan->amount_available) }}
            </div>
        </td>
        <td class="center aligned no-box no-border">
            <div class="mobile-table-content">
                <a
                        class="ui teal button"
                        href="{{route('profile.invest.view' ,$loan->loan_id)}}">{{__('static.HomePageAvailableLoansTableInvestButton')}}</a>
            </div>
        </td>
    </tr>
@endforeach

<tr id="pagination-nav" class="position-relative mt-0 invest-pagination-front-end">
    <td colspan="10" style="padding: 2rem 0">
        <input id="totalLoansCount" value="{{$loans->total()}}" type="hidden">
        {{ $loans->onEachSide(1)->links() }}
        <form class="card-body float-right w-100 investForm hide-on-tablet"
              action="{{ route('invest') }}"
              method="PUT">
            @csrf
            <span class="d-inline-block float-left">Results</span>
            <select class="form-control d-inline-block float-left w-50 ml-2 pl-1 pr-0 py-0 noClear" style="margin-top:
                -7px" name="limit" id="maxRows">
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
            </select>
        </form>
    </td>
</tr>



