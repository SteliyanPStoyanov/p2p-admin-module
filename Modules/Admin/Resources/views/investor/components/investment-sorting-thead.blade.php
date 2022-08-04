@php
    $sortingArray['investment']['investment_created_at']['yes']= __('common.InvestmentDateTwoLine');
    $sortingArray['investment']['loan_id']= __('common.LoanId');
    $sortingArray['investment']['investment_id']= __('common.InvestmentId');
    $sortingArray['investment']['loan_created_at']['yes']= __('common.ListingDate');
    $sortingArray['investment']['country_name']['yes']= __('common.Country');
    $sortingArray['investment']['type']['yes']= __('common.LoanType');
    $sortingArray['investment']['originator_name']['yes']= __('common.Lender');
    $sortingArray['investment']['loan_remaining_principal']['yes']= __('common.LoanBalance');
    $sortingArray['investment']['interest_rate_percent']['yes']= __('common.InterestRateAI');
    $sortingArray['investment']['final_payment_date']['yes']= __('common.Term');
    $sortingArray['investment']['amount']= __('common.InvestedAmountTable');
    $sortingArray['investment']['invested_sum']['yes']= __('common.OutstandingInvestmentTable');
    $sortingArray['investment']['status']['yes']= __('common.LoanStatusTable');
    $sortingArray['investment']['payment_status']['yes']= __('common.PaymentStatus');
    $sortingArray['investment']['unlisted']['yes']= __('common.ListingStatusTwoLine');
@endphp
<tr>
    @foreach($sortingArray as $table => $columns)
        @foreach($columns as $column => $joinValue)
            @if(is_array($joinValue))
                <th scope="col" class="text-center sorting investment-col-{{$column}}">
                    <input type="text" name="order[{{$column}}]"
                           value="{{session($cacheKey . '.order.'.$column) ?: 'desc'}}">
                    {!! $joinValue['yes'] !!}
                    <i class="fa {{session($cacheKey . '.order.'.$column) ?
                                                            'fa-sort-'.session($cacheKey . '.order.'.$column) : ''}}"
                       aria-hidden="true"></i>
                </th>
            @else
                <th scope="col" class="text-center sorting">
                    <input type="text" name="order[{{$table}}][{{$column}}]"
                           value="{{session($cacheKey . '.order.'.$table.'.'.$column) ?: 'desc'}}">
                    {!!$joinValue !!}
                    <i class="fa {{session($cacheKey . '.order.'.$table.'.'.$column) ?
                                                            'fa-sort-'.session($cacheKey . '.order.'.$table.'.'.$column) : ''}}"
                       aria-hidden="true"></i>
                </th>
            @endif
        @endforeach
    @endforeach

</tr>
