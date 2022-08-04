
<table class="ui table available-loans-table" id="investTable" style="font-size: 1rem;">
    <thead>
    @include('profile::invest.sorting.table-head')
    </thead>
    <tbody id="investsTable">
    @foreach($loans as $loan)
        <tr>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.Country')}}</div>
                <div class="mobile-table-content">
                    <img class="country-flag-circle" alt="bulgaria-flag"
                         src="{{ assets_version(url('/') . '/images/countries/' . mb_strtolower($loan->country->name) . '-flag-round-icon-32.png') }}">
                    <div class="country-name-mobile">{{ $loan->country->name }}</div>
                </div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanId')}}</div>
                <div class="mobile-table-content">
                    <a href="{{route('profile.invest.view' ,$loan->loan_id)}}"
                       target="_blank" class="table-invest-link">{{ $loan->loan_id }}</a></div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.IssueDate')}}</div>
                <div class="mobile-table-content">{{showDate($loan->lender_issue_date)}}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanType')}}</div>
                <div class="mobile-table-content">{{loanType($loan->type)}}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanOriginator')}}</div>
                <div class="mobile-table-content">{{ $loan->originator->name }}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.InterestRate')}}</div>
                <div class="mobile-table-content">
                    {{ rate($loan->interest_rate_percent) }}
                </div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.Term')}}</div>
                <div class="mobile-table-content">{{termFormat($loan->final_payment_date)}}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanAmount')}}</div>
                <div class="mobile-table-content">{{ amount($loan->amount) }}
                </div>
            </td>
            <td class="center aligned amount-available">
                <div class="mobile-table-title">{{__('common.AvailableForInvestment')}}</div>
                <div class="mobile-table-content">{{ amount($loan->amount_available) }}</div>
            </td>
            <td class="center aligned">
                <div class="mobile-table-title">{{__('common.LoanPaymentStatus')}}</div>
                <div class="mobile-table-content">{{  ucfirst($loan->payment_status) }}
                </div>
            </td>
            <td class="center aligned no-border no-box">
                <div onclick="investSingleForm($(this));"
                     class="invest-button-form ui teal button">
                    {{__('common.Invest')}}
                </div>
                <form class="invest-form single-invest-button hide-some-element"
                      action="{{route('profile.invest.invest',$loan->loan_id)}}"
                      onsubmit="return investFormSubmit($(this));">
                    <input type="number" name="amount" class="form-control single-amount"
                           placeholder="Amount" min="10" step="0.01">
                    <input type="hidden" class="loan_id" name="loan_id" value="{{$loan->loan_id}}">
                    <button class="ui teal button" type="submit">
                        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
                    </button>
                    <div class="close-form" style="position: absolute; right: -20px; top: 10px;"
                         onclick="investSingleFormClose($(this));">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </form>
            </td>
        </tr>
    @endforeach

    <tr id="pagination-nav" class="position-relative mt-0 invest-pagination">
        <div class="row">
            <td colspan="12">
                <input id="totalLoansCount" value="{{$loans->total()}}" type="hidden">
                {{ $loans->onEachSide(1)->links() }}

                {{-- multiple filter --}}

                {{-- multiple filter --}}
                <form class="card-body float-right pt-1 d-inline-block hide-on-tablet"
                      action="{{ route('profile.invest') }}"
                      method="PUT">
                    @csrf
                    <span class="d-inline-block float-left">Results</span>
                    <select class="form-control d-inline-block float-left w-25 ml-2 pl-2 pr-0 py-0 noClear" style="margin-top:
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
