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
                               {{session($cacheKey . '.order.loan.loan_id') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][loan_id]"
               value="{{session($cacheKey . '.order.loan.loan_id') ?: 'desc'}}">
        {{__('common.LoanId')}}
        <i class="fa {{session($cacheKey . '.order.loan.loan_id') ?
                                    'fa-sort-'.session($cacheKey . '.order.loan.loan_id') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                                   {{session($cacheKey . '.order.loan.lender_issue_date') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][lender_issue_date]"
               value="{{session($cacheKey . '.order.loan.lender_issue_date') ?: 'desc'}}">
        {{__('common.IssueDate')}}
        <i class="fa {{session($cacheKey . '.order.loan.lender_issue_date') ?
                                    'fa-sort-'.session($cacheKey . '.order.loan.lender_issue_date') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                                    {{session($cacheKey . '.order.loan.type') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][type]"
               value="{{session($cacheKey . '.order.loan.type') ?: 'desc'}}">
        {{__('common.LoanType')}}
        <i class="fa {{session($cacheKey . '.order.loan.type') ?
                                    'fa-sort-'.session($cacheKey . '.order.loan.type') : 'fa-sort'}}"
           aria-hidden="true"></i>
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
                                    {{session($cacheKey . '.order.loan.amount') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][amount]"
               value="{{session($cacheKey . '.order.loan.amount') ?: 'desc'}}">
        {!! trans('common.LoanAmountTable')!!}
        <i class="fa {{session($cacheKey . '.order.loan.amount') ?
                                    'fa-sort-'.session($cacheKey . '.order.loan.amount') : 'fa-sort'}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="center aligned sorting
                                    {{session($cacheKey . '.order.loan.amount_available') ? 'active-sort' : ''}}">
        <input type="text" name="order[loan][amount_available]"
               value="{{session($cacheKey . '.order.loan.amount_available') ?: 'desc'}}">
        {!! trans('common.AvailableForInvestmentTable')!!}
        <i class="fa {{session($cacheKey . '.order.loan.amount_available') ?
                                    'fa-sort-'.session($cacheKey . '.order.loan.amount_available') : 'fa-sort'}}"
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
    <th scope="col" class="center aligned no-border hide-sell-finish-all pl-0 pr-0" style="width: 130px">
        @if( $investor->status != \Modules\Common\Entities\Investor::INVESTOR_STATUS_UNREGISTERED)
            <form class="invest-all-form float-right" onsubmit="return investAllFormSubmit($(this));"
                  method="POST"
                  action="{{route('profile.invest.investAll')}}">
                @csrf
                <input class="form-control d-inline-block invest-all-form-amount float-right pr-0"
                       type="number"
                       name="amount"
                       placeholder="{{ __('common.Amount') }}" min="10" step="0.01">
                <button class="ui teal button d-inline-block float-right invest-all-form-submit"
                        type="submit"
                        value=""><i class="fa fa-shopping-bag" aria-hidden="true"></i></button>
                <div style="top: 40px;" class="close-form" onclick="investAllClose($(this));">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </div>
            </form>
            <div id="investAllFormHas"></div>
            <div onclick="return investAllForm()"
                 class="ui teal button invest-all-button">{{__('common.InvestInAll')}}</div>
        @endif
    </th>
</tr>
