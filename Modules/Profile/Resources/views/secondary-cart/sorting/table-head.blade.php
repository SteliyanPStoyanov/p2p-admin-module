<tr>
    <th scope="col"
        class="center aligned {{session($cacheKey . '.sale.loan.listed') ? 'active-sort' : ''}}">
        {{__('common.Country')}}
    </th>
    <!-- Loan ID -->
    <th scope="col" class="center aligned ">
        {!! trans('common.LoanId') !!}
    </th>

    <!-- Investment Date -->
    <th scope="col" class="center aligned ">
        {!! trans('common.InvestmentDate') !!}
    </th>

    <!-- Loan Type -->
    <th scope="col" class="center aligned ">
        {!! trans('common.LoanType') !!}
    </th>

    <!-- Loan Originator -->
    <th scope="col" class="center aligned">
        {!! trans('common.LoanOriginatorTable') !!}
    </th>

    <!-- Interest Rate -->
    <th scope="col" class="center aligned ">
        {!! trans('common.InterestRateTable') !!}
    </th>

    <!-- Term -->
    <th scope="col" class="center aligned ">
        {!! trans('common.Term') !!}
    </th>

    <!-- Outstanding Investment -->
    <th scope="col" class="center aligned interest-rate">
        {!! trans('common.OutstandingInvestment') !!}
        <br>
        <span> <i class="fa fa-info-circle secondary-market-cart-tooltip"
                  aria-hidden="true" data-toggle="tooltip" data-placement="top"
                  data-original-title="{{__('common.OutstandingInvestmentTooltip')}}"></i></span>
    </th>

    <!-- Discount/Premium % -->
    <th scope="col" class="center aligned interest-rate">
        {!! trans('common.DiscountPremium') !!}
        <br>
        <span> <i class="fa fa-info-circle secondary-market-cart-tooltip"
                  aria-hidden="true" data-toggle="tooltip" data-placement="top"
                  data-original-title="{{__('common.DiscountPremiumTooltip')}}"></i></span>
    </th>

    <!-- Principal for Sale  -->
    <th scope="col" class="center aligned interest-rate">
        {!! trans('common.PrincipalForSale') !!}
        <br>
        <span> <i class="fa fa-info-circle secondary-market-cart-tooltip"
                  aria-hidden="true" data-toggle="tooltip" data-placement="top"
                  data-original-title="{{__('common.PrincipalForSaleTooltip')}}"></i></span>
    </th>

    <!-- Sale Price -->
    <th scope="col" class="center aligned interest-rate">
        {!! trans('common.SalePrice') !!}
        <br>
        <span> <i class="fa fa-info-circle secondary-market-cart-tooltip"
                  aria-hidden="true" data-toggle="tooltip" data-placement="top"
                  data-original-title="{{__('common.SalePriceTooltip')}}"></i></span>
    </th>
</tr>
