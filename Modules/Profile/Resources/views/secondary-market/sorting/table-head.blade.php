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
    <th
        scope="col" class="center aligned sorting
               {{session($cacheKey . '.order.market_secondary.loan_id') ? 'active-sort' : ''}}"
    >
        <input
            type="text"
            name="order[market_secondary][loan_id]"
            value="{{session($cacheKey . '.order.market_secondary.loan_id') ?: 'desc'}}"
        >
        {{__('common.LoanId')}}
        <i
            class="fa {{session($cacheKey . '.order.market_secondary.loan_id') ?
                    'fa-sort-'.session($cacheKey . '.order.market_secondary.loan_id') : 'fa-sort'}}"
            aria-hidden="true"
        ></i>
    </th>
    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.market_secondary.created_at') ? 'active-sort' : ''}}">
        <input type="text" name="order[market_secondary][created_at]"
               value="{{session($cacheKey . '.order.market_secondary.created_at') ?: 'desc'}}">
        {!! trans('common.InvestmentDateTwoLine') !!}
        <i class="fa {{session($cacheKey . '.order.market_secondary.created_at') ?
                            'fa-sort-'.session($cacheKey . '.order.market_secondary.created_at') : 'fa-sort'}}"
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
                            {{session($cacheKey . '.order.market_secondary.originator_id') ? 'active-sort' : ''}}">
        <input type="text" name="order[market_secondary][originator_id]"
               value="{{session($cacheKey . '.order.market_secondary.originator_id') ?: 'desc'}}">
        {!! trans('common.LoanOriginatorTable') !!}
        <i class="fa {{session($cacheKey . '.order.market_secondary.originator_id') ?
                            'fa-sort-'.session($cacheKey . '.order.market_secondary.originator_id') : 'fa-sort'}}"
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
                            {{session($cacheKey . '.order.loan.payment_status') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][payment_status]"
               value="{{session($cacheKey . '.order.loan.payment_status') ?: 'desc'}}">
        {!! trans('common.LoanPaymentStatusTable')!!}
        <i class="fa {{session($cacheKey . '.order.loan.payment_status') ?
                            'fa-sort-'.session($cacheKey . '.order.loan.payment_status') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>

    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.market_secondary.principal_for_sale') ? 'active-sort' : ''}}">
        <input type="text" name="order[market_secondary][principal_for_sale]"
               value="{{session($cacheKey . '.order.market_secondary.principal_for_sale') ?: 'desc'}}">
        {!! trans('common.AvailableForInvestmentTable')!!}
        <i class="fa {{session($cacheKey . '.order.market_secondary.principal_for_sale') ?
                            'fa-sort-'.session($cacheKey . '.order.market_secondary.principal_for_sale') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>

    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.market_secondary.premium') ? 'active-sort' : ''}}">
        <input type="text" name="order[market_secondary][premium]"
               value="{{session($cacheKey . '.order.market_secondary.premium') ?: 'desc'}}">
        {!! trans('common.DiscountPremiumTable')!!}
        <i class="fa {{session($cacheKey . '.order.market_secondary.premium') ?
                            'fa-sort-'.session($cacheKey . '.order.market_secondary.premium') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>

    <th scope="col" class="center aligned sorting
                            {{session($cacheKey . '.order.market_secondary.price') ? 'active-sort' : ''}}">
        <input type="text" name="order[market_secondary][price]"
               value="{{session($cacheKey . '.order.market_secondary.price') ?: 'desc'}}">
        {!! trans('common.Price')!!}
        <i class="fa {{session($cacheKey . '.order.market_secondary.price') ?
                            'fa-sort-'.session($cacheKey . '.order.market_secondary.price') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned hide-sell-finish-all pl-0 pr-0">
        @include('profile::secondary-market.invest.invest-all-button')
    </th>
</tr>
