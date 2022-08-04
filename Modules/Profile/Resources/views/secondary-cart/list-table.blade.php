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
            <div class="mobile-table-title">{{__('common.InvestmentDate')}}</div>
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
        <td class="center aligned">
            <div class="mobile-table-title">{{__('common.InterestRateTable')}}</div>
            <div class="mobile-table-content">
                {{ rate($secondaryLoan->getLoan()->interest_rate_percent) }}
            </div>
        </td>
        <td class="center aligned interest-rate">
            <div class="mobile-table-title">{{__('common.Term')}}</div>
            <div class="mobile-table-content">
                {{ termFormat($secondaryLoan->getLoan()->final_payment_date) }}
            </div>
        </td>
        <td class="center aligned">
            <div class="mobile-table-title">{{__('common.OutstandingInvestment')}}</div>
            <div class="mobile-table-content">
                {{ amount($secondaryLoan->getInvestment()->amount) }}
                <input
                    type="hidden"
                    value="{{ $secondaryLoan->getInvestment()->amount }}"
                    class="form-control price"
                    min="0"
                    step="0.01"
                >
            </div>
        </td>
        <td style="position: relative" class="center aligned interest-rate cart-{{$loop->index}}-discount">
            <div class="mobile-table-title">{{__('common.DiscountPremium')}}</div>
            <div class="mobile-table-content">

                <div class="input-group " style="width: 90% ; margin-left: 5%;">
                    <input
                        style="width: 64%;"
                        type="number"
                        step="0.01"
                        value="{{ percentReport($secondaryLoan->getPremium()) }}"
                        class="form-control premium text-center interest-rate-field pr-1"
                        onfocusout="reCalculateForm($(this))"
                    >
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
        </td>

        <!-- Principal for Sale  -->

        <td style="position: relative" class="center aligned interest-rate cart-{{$loop->index}}-principal">

            <div class="mobile-table-title">{{__('common.PrincipalForSale')}}</div>
            <div class="mobile-table-content">
                <div class="input-group" style="width: 90% ; margin-left: 5%;">
                    <div class="input-group-prepend">
                        <span class="input-group-text">€</span>
                    </div>
                    <input
                        style="width: 64%;"
                        type="number"
                        value="{{ amountReport($secondaryLoan->getPrincipalForSale()) }}"
                        class="form-control principal"
                        min="0"
                        step="0.01"
                        onfocusout="reCalculateForm($(this)); return false;"
                    >
                </div>
            </div>
        </td>

        <!-- Sale Price -->

        <td class="center aligned cart-{{$loop->index}}-salePrice interest-rate" style="position: relative;">

            <div class="mobile-table-title">{{__('common.SalePrice')}}</div>
            <div class="mobile-table-content text-center">

                @if($secondaryLoan->getPrice() == 0)
                    {{ amount($secondaryLoan->getPrincipalForSale()) }}
                    <input
                        style="width: 64%; border: none;"
                        type="hidden"
                        value="{{ amountReport($secondaryLoan->getPrincipalForSale()) }}"
                        class="form-control price"
                        min="0"
                        step="0.01"
                        readonly
                    >
                @else
                    {{ amount($secondaryLoan->getPrice()) }}
                    <input
                        style="width: 64%; border: none;"
                        type="hidden"
                        value="{{ amountReport($secondaryLoan->getPrice()) }}"
                        class="form-control price"
                        min="0"
                        step="0.01"
                        readonly
                    >
                @endif

            </div>
            <button
                style="position: absolute; right: -25px; top: 12px; color: red; font-weight: bolder; border: none; background: none;"
                data-loanId="{{ $secondaryLoan->getCartLoanId() }}"
                onClick="deleteCartLoan({{ $secondaryLoan->getCartLoanId() }})"
                aria-hidden="true" data-toggle="tooltip" data-placement="top"
                data-original-title="{{__('common.SaleDeleteTooltip')}}"
            ><i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i>
            </button>
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
    <td colspan="7" class="border-0">

    </td>
    <td class="center aligned font-weight-bolder loan-amount border-0">
        <div class="mobile-table-title">{{__('common.InvestedAmount')}}</div>
        <div class="mobile-table-content pt-3" id="totalPrincipal">{{amount($totalPrincipal)}}</div>
    </td>
    <td class="center aligned font-weight-bolder loan-amount border-0">
        <div class="mobile-table-title">{{__('common.ReceivedPayments')}}</div>
        <div class="mobile-table-content pt-3" id="totalPrice">{{amount($totalPrice)}}</div>
    </td>

</tr>

