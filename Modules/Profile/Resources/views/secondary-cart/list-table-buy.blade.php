@php
    $totalPrincipal = 0;
    $totalPrice = 0;
@endphp

@foreach($loans->all() as $secondaryLoan)

    <tr class="dataRow" id="dataRow-{{$secondaryLoan->getCartLoanId()}}" data-row="{{$secondaryLoan->getCartLoanId()}}">
        <td class="center aligned country">
            <div class="mobile-table-title">{{__('common.Country')}}</div>
            <div class="mobile-table-content">
                <img class="country-flag-circle" alt="country-flag"
                     src="{{ url('/') }}/images/countries/{{ mb_strtolower($secondaryLoan->getLoan()->country->name) }}-flag-round-icon-32.png">
                <div class="country-name-mobile">{{ $secondaryLoan->getLoan()->country->name }}</div>
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('common.LoanId')}}</div>
            <div class="mobile-table-content">

                <a href="{{route('profile.invest.view', $secondaryLoan->getLoan()->loan_id)}}"
                   target="_blank" class="table-invest-link">{{ $secondaryLoan->getLoan()->loan_id }}</a></div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('common.IssueDate')}}</div>
            <div class="mobile-table-content">
                {{ showDate($secondaryLoan->getInvestment()->created_at) }}
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('common.LoanType')}}</div>
            <div class="mobile-table-content">
                {{ loanType($secondaryLoan->getLoan()->type) }}
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('common.LoanOriginator')}}</div>
            <div class="mobile-table-content">
                {{ $secondaryLoan->getOriginator()->name }}
            </div>
        </td>
        <td class="center aligned interest-rate">
            <div class="mobile-table-title">{{__('common.InterestRate')}}</div>
            <div class="mobile-table-content">
                {{ rate($secondaryLoan->getLoan()->interest_rate_percent) }}
            </div>
        </td>
        <td class="center aligned term-col">
            <div class="mobile-table-title">{{__('common.Term')}}</div>
            <div class="mobile-table-content text-nowrap">
                {{ termFormat($secondaryLoan->getLoan()->final_payment_date) }}
            </div>
        </td>
        <td class="center aligned term-col">
            <div class="mobile-table-title">{{__('common.LoanStatus')}}</div>
            <div class="mobile-table-content">
                {{ ucwords($secondaryLoan->getLoan()->payment_status) }}
            </div>
        </td>

        <td class="center aligned loan-amount">
            <div class="mobile-table-title">{{__('common.OutstandingPrincipal')}}</div>
            <div class="mobile-table-content">
                {{ amount($secondaryLoan->getLoan()->amount_available) }}

            </div>
        </td>

        <td class="center aligned interest-rate">
            <div class="mobile-table-title">{!! __('common.AvailableForInvestment') !!}</div>
            <div class="mobile-table-content">
                {{ amount($secondaryLoan->getMarketSecondary()->principal_for_sale) }}
                <input
                    type="hidden"
                    value="{{ $secondaryLoan->getMarketSecondary()->principal_for_sale }}"
                    class="form-control price"
                    min="0"
                    step="0.01"
                >
            </div>
        </td>
        <td class="center aligned interest-rate">
            <div class="mobile-table-title">{{__('common.Price')}}</div>
            <div class="mobile-table-content">
                <span class="text-nowrap">{{ amount($secondaryLoan->getPrice()) }}</span> <br>
                 <input
                        type="hidden"
                        value="{{ abs($secondaryLoan->getPremium()) }}"
                        class="form-control price"
                        min="0"
                        step="0.01"
                    >
                <span style="color: {{$premiumColor = ($secondaryLoan->getPremium() < 0) ? '#009193'
                                        : (($secondaryLoan->getPremium() <= 0) ?: '#FF7E79') }} ;"
                >{{ percentReport(abs($secondaryLoan->getPremium()) , ($secondaryLoan->getPremium() < 0) ? '-' : '+' ) }}
                    %</span>
            </div>
        </td>
        <td class="center aligned interest-rate cart-{{$loop->index}}-principal" style="position: relative;">
            <div class="mobile-table-title">{{__('common.InvestmentAmount')}}</div>
            <div class="mobile-table-content">
                <div class="input-group" style="width: 90% ; margin-left: 5%;">
                    <div class="input-group-prepend">
                        <span class="input-group-text">€</span>
                    </div>
                    <input onkeyup="return reCalculateBuyForm();" onchange="return reCalculateBuyForm();"
                           style="width: 64%;"
                           type="number"
                           value="{{ amountReport($secondaryLoan->getPrincipalForSale()) }}"
                           class="form-control premium text-center p-0"
                           min="0.0"
                           max="{{$secondaryLoan->getPrincipalForSale()}}"
                           step="0.1"
                    >
                </div>
            </div>
            <button
                style="position: absolute; right: -135px; top: 18px; color: red; font-weight: bolder; border: none; background: none;"
                data-loanId="{{ $secondaryLoan->getCartLoanId() }}"
                onClick="deleteCartLoan({{ $secondaryLoan->getCartLoanId() }})"
                aria-hidden="true" data-toggle="tooltip" data-placement="top"
                data-original-title="{{__('common.SaleDeleteTooltip')}}"
            ><i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i>
            </button>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('common.AssignmentAgreement')}}</div>
            <div class="mobile-table-content">
                <a href="{{route('profile.invest.assignment-agreement.template', $secondaryLoan->getLoan()->loan_id)}}"
                   style="cursor: pointer;"
                   target="_blank">
                    <i class="fa fa-file-text-o" aria-hidden="true" style="font-size: 20px; color: #0070C0;"></i>
                </a>
            </div>
        </td>
    </tr>
    @php
        $totalPrincipal += $secondaryLoan->getPrincipalForSale();
        $totalPrice += $secondaryLoan->getPrice();
    @endphp
@endforeach

<tr class="my-investment-total">
    <td class="border-0">

    </td>
    <td class="center aligned font-weight-bolder border-0">
        {{$loans->count()}}
        loan{{$loans->count() > 1 ? 's' : ''}}
    </td>
    <td colspan="8" class="border-0">

    </td>
    <td class="center aligned font-weight-bolder loan-amount border-0">
        <div class="mobile-table-title">{{__('common.InvestedAmount')}}</div>
        <div class="mobile-table-content pt-3 text-nowrap" id="totalPrincipal">{{amount($totalPrice)}}</div>
    </td>
    <td class="center aligned font-weight-bolder loan-amount border-0">
        <div class="mobile-table-title">{{__('common.ReceivedPayments')}}</div>
        <div class="mobile-table-content pt-3 text-nowrap" id="totalPriceBuy">{{amount($totalPrincipal)}}</div>
    </td>
    <td colspan="1" class="border-0">
    </td>
</tr>

