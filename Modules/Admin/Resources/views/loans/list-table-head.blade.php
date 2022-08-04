<tr>
    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][country_id]"
               value="{{session($cacheKey . '.order.loan.country_id') ?: 'desc'}}">
        {{__('common.Country')}}
        <i class="fa {{session($cacheKey . '.order.loan.country_id') ? 'fa-sort-'.session($cacheKey . '.order.loan.country_id') : ' '}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][loan_id]"
               value="{{session($cacheKey . '.order.loan.loan_id') ?: 'desc'}}">
        {{__('common.LoanId')}}
        <i class="fa {{session($cacheKey . '.order.loan.loan_id') ? 'fa-sort-'.session($cacheKey . '.order.loan.loan_id') : ' '}}"
           aria-hidden="true"></i>
    </th>


    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][created_at]"
               value="{{session($cacheKey . '.order.loan.created_at') ?: 'desc'}}">
        {{__('common.ListingDate')}}
        <i class="fa {{session($cacheKey . '.order.loan.created_at') ? 'fa-sort-'.session($cacheKey . '.order.loan.created_at') : ' '}}"
           aria-hidden="true"></i>
    </th>


    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][type]"
               value="{{session($cacheKey . '.order.loan.type') ?: 'desc'}}">
        {{__('common.LoanType')}}
        <i class="fa {{session($cacheKey . '.order.loan.type') ? 'fa-sort-'.session($cacheKey . '.order.loan.type') : ' '}}"
           aria-hidden="true"></i>
    </th>

    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][lender_id]"
               value="{{session($cacheKey . '.order.loan.lender_id') ?: 'desc'}}">
        {{__('common.Lender')}}
        <i class="fa {{session($cacheKey . '.order.loan.lender_id') ? 'fa-sort-'.session($cacheKey . '.order.loan.lender_id') : ' '}}"
           aria-hidden="true"></i>
    </th>

    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][amount]"
               value="{{session($cacheKey . '.order.loan.amount') ?: 'desc'}}">
        {{__('common.LoanAmount')}}
        <i class="fa {{session($cacheKey . '.order.loan.amount') ? 'fa-sort-'.session($cacheKey . '.order.loan.amount') : ' '}}"
           aria-hidden="true"></i>
    </th>


    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][amount_available]"
               value="{{session($cacheKey . '.order.loan.amount_available') ?: 'desc'}}">
        {{__('common.OutstandingAmount')}}
        <i class="fa {{session($cacheKey . '.order.loan.amount_available') ? 'fa-sort-'.session($cacheKey . '.order.loan.amount_available') : ' '}}"
           aria-hidden="true"></i>
    </th>


    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][interest_rate_percent]"
               value="{{session($cacheKey . '.order.loan.interest_rate_percent') ?: 'desc'}}">
        {{__('common.InterestRate')}}
        <i class="fa {{session($cacheKey . '.order.loan.interest_rate_percent') ? 'fa-sort-'.session($cacheKey . '.order.loan.interest_rate_percent') : ' '}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][period]"
               value="{{session($cacheKey . '.order.loan.period') ?: 'desc'}}">
        {{__('common.Term')}}
        <i class="fa {{session($cacheKey . '.order.loan.period') ? 'fa-sort-'.session($cacheKey . '.order.loan.period') : ' '}}"
           aria-hidden="true"></i>
    </th>

    <th scope="col" class="text-center sorting">
        <input type="text" name="order[invested_sum]"
               value="{{session($cacheKey . '.order.invested_sum') ?: 'desc'}}">
        {{__('common.InvestedAmount')}}
        <i class="fa {{session($cacheKey . '.order.invested_sum') ? 'fa-sort-'.session($cacheKey . '.order.invested_sum') : ' '}}"
           aria-hidden="true"></i>
    </th>

    <th scope="col" class="text-center sorting">
        <input type="text" name="order[invested_percent]"
               value="{{session($cacheKey . '.order.invested_percent') ?: 'desc'}}">
        {{__('common.PercentFunded')}}
        <i class="fa {{session($cacheKey . '.order.invested_percent') ? 'fa-sort-'.session($cacheKey . '.order.invested_percent') : ' '}}"
           aria-hidden="true"></i>
    </th>

    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][status]"
               value="{{session($cacheKey . '.order.loan.status') ?: 'desc'}}">
        {{__('common.LoanStatus')}}
        <i class="fa {{session($cacheKey . '.order.loan.status') ? 'fa-sort-'.session($cacheKey . '.order.loan.status') : ' '}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="text-center sorting">
        <input type="text"
               @if(session($cacheKey . '.status') === \Modules\Common\Entities\Loan::STATUS_FINISH)
               name="order[loan][final_payment_status]"
               @else
               name="order[loan][payment_status]"
               @endif

               value="{{session($cacheKey . '.order.loan.payment_status') ?: 'desc'}}">
        {{__('common.PaymentStatus')}}
        <i class="fa {{session($cacheKey . '.order.loan.payment_status') ? 'fa-sort-'.session($cacheKey . '.order.loan.payment_status') : ' '}}"
           aria-hidden="true"></i>
    </th>
    <th scope="col" class="text-center sorting">
        <input type="text" name="order[loan][unlisted]"
               value="{{session($cacheKey . '.order.loan.unlisted') ?: 'desc'}}">
        {{__('common.ListingStatus')}}
        <i class="fa {{session($cacheKey . '.order.loan.unlisted') ? 'fa-sort-'.session($cacheKey . '.order.loan.unlisted') : ' '}}"
           aria-hidden="true"></i>
    </th>
</tr>
