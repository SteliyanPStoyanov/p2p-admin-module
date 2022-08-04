<tr>
    <th scope="col" class="center aligned sorting
                        {{session($cacheKey . '.order.loan.country_id') ? 'active-sort' : ''}}"
    >
        <input type="text" name="order[loan][country_id]"
               value="{{session($cacheKey . '.order.loan.country_id') ?: 'desc'}}"
        >
        {{__('common.Country')}}
        <i class="fa {{session($cacheKey . '.order.loan.country_id') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.country_id') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                       {{session($cacheKey . '.order.investment.loan_id') ? 'active-sort' : ''}}">
        <input type="text" name="order[investment][loan_id]"
               value="{{session($cacheKey . '.order.investment.loan_id') ?: 'desc'}}">
        {{__('common.LoanId')}}
        <i class="fa {{session($cacheKey . '.order.investment.loan_id') ?
                            'fa-sort-'.session($cacheKey . '.order.investment.loan_id') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.investment.created_at') ? 'active-sort' : ''}}">
        <input type="text" name="order[investment][created_at]"
               value="{{session($cacheKey . '.order.investment.created_at') ?: 'desc'}}">
        {!! trans('common.InvestmentDateTwoLine') !!}
        <i class="fa {{session($cacheKey . '.order.investment.created_at') ?
                            'fa-sort-'.session($cacheKey . '.order.investment.created_at') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.loan.type') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][type]"
               value="{{session($cacheKey . '.order.loan.type') ?: 'desc'}}">
        {{__('common.LoanType')}}
        <i class="fa {{session($cacheKey . '.order.loan.type') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.type') : 'fa-sort'}}" aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.loan.originator_id') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][originator_id]"
               value="{{session($cacheKey . '.order.loan.originator_id') ?: 'desc'}}">
        {!! trans('common.LoanOriginatorTable') !!}
        <i class="fa {{session($cacheKey . '.order.loan.originator_id') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.originator_id') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.loan.interest_rate_percent') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][interest_rate_percent]"
               value="{{session($cacheKey . '.order.loan.interest_rate_percent') ?: 'desc'}}">
        {!! trans('common.InterestRateTable') !!}
        <i class="fa {{session($cacheKey . '.order.loan.interest_rate_percent') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.interest_rate_percent') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.loan.final_payment_date') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][final_payment_date]"
               value="{{session($cacheKey . '.order.loan.final_payment_date') ?: 'desc'}}">
        {{__('common.Term')}}
        <i class="fa {{session($cacheKey . '.order.loan.final_payment_date') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.final_payment_date') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.loan.amount_available') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][amount_available]"
               value="{{session($cacheKey . '.order.loan.amount_available') ?: 'desc'}}">
        {!! trans('common.LoanAmountTable')!!}
        <i class="fa {{session($cacheKey . '.order.loan.amount_available') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.amount_available') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.investment.amount') ? 'active-sort' : ''}}">
        <input type="text" name="order[investment][amount]"
               value="{{session($cacheKey . '.order.investment.amount') ?: 'desc'}}">
        {!! trans('common.InvestedAmountTable')!!}
        <i class="fa {{session($cacheKey . '.order.investment.amount') ?
                            'fa-sort-'.session($cacheKey . '.order.investment.amount') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th data-toggle="tooltip" data-placement="top" scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.received_amount') ? 'active-sort' : ''}}"
        data-original-title="{{ __('common.ReceivedPaymentsTableTooltipText') }}">
        <input type="text" name="order[received_amount]"
               value="{{session($cacheKey . '.order.received_amount') ?: 'desc'}}">
        {!! trans('common.ReceivedPaymentsTable')!!}
        <i class="fa {{session($cacheKey . '.order.received_amount') ?
                            'fa-sort-'.session($cacheKey . '.order.received_amount') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.invested_sum') ? 'active-sort' : ''}}">
        <input type="text" name="order[invested_sum]"
               value="{{session($cacheKey . '.order.invested_sum') ?: 'desc'}}">
        {!! trans('common.OutstandingInvestmentTable')!!}
        <i class="fa {{session($cacheKey . '.order.invested_sum') ?
                            'fa-sort-'.session($cacheKey . '.order.invested_sum') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting pr-1 pl-1
                            {{session($cacheKey . '.order.loan.payment_status') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][payment_status]"
               value="{{session($cacheKey . '.order.loan.payment_status') ?: 'desc'}}">
        {!! trans('common.LoanPaymentStatusTable')!!}
        <i class="fa {{session($cacheKey . '.order.loan.payment_status') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.payment_status') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned pr-0 pl-0 hide-sell-finish-all
                            {{session($cacheKey . '.sale.loan.listed') ? 'active-sort' : ''}}">
        <button
            style="width: 130px;"
            class="ui teal button btn-filter-submit float-right"
            data-sale-url="{{ route('profile.my-investments.sellMultiple')}}"
            id="sell_all">
            {{__('common.SellAllLoans')}}
        </button>

        @if($loansOnMarket->isNotEmpty() || $loansInCart->isNotEmpty())
            <div class="remove-all-from-cart"  onclick="deleteAllCartLoan(this);">
                <i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i>
            </div>
        @endif
    </th>
</tr>
