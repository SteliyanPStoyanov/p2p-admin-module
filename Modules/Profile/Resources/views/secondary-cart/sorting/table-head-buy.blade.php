<tr>
    <th scope="col"
        class="center aligned">
        {{__('common.Country')}}
    </th>
    <!-- Loan ID -->
    <th scope="col" class="center aligned text-nowrap">
        <input type="hidden" name="type3" value="sell">
        {!! trans('common.LoanId') !!}
    </th>

    <!-- Date Of Issue -->
    <th scope="col" class="center aligned">
        {!! trans('common.IssueDate') !!}
    </th>

    <!-- Loan Type -->
    <th scope="col" class="center aligned">
        {!! trans('common.LoanType') !!}
    </th>

    <!-- Loan Originator -->
    <th scope="col" class="center aligned">
        {!! trans('common.LoanOriginatorTable') !!}
    </th>

    <!-- Interest Rate -->
    <th scope="col" class="center aligned">
        {!! trans('common.InterestRateTable') !!}
    </th>

    <!-- Term -->
    <th scope="col" class="center aligned">
        {!! trans('common.Term') !!}
    </th>

    <!-- Loan Status -->
    <th scope="col" class="center aligned">
        {!! trans('common.LoanStatus') !!}
    </th>

    <!-- Outstanding Principal -->
    <th scope="col" class="center aligned interest-rate">
        {!! trans('common.OutstandingPrincipal') !!}
        <br>
        <span> <i class="fa fa-info-circle"
                  style="color: #c9c9c9; margin-left: 5px; cursor: pointer;"
                  aria-hidden="true" data-toggle="tooltip" data-placement="top"
                  data-original-title="{{__('common.OutstandingInvestmentTooltip')}}"></i></span>
    </th>


    <!-- Available for investment  -->
    <th scope="col" class="center aligned interest-rate">
        {!! trans('common.AvailableForInvestmentTable') !!}
        <br>
        <span> <i class="fa fa-info-circle"
                  style="color: #c9c9c9; margin-left: 5px; cursor: pointer;"
                  aria-hidden="true" data-toggle="tooltip" data-placement="top"
                  data-original-title="{{__('common.PrincipalForSaleTooltip')}}"></i></span>
    </th>

    <!-- Price  -->
    <th scope="col" class="center aligned interest-rate">
        {!! trans('common.Price') !!}
        <br>
        <span> <i class="fa fa-info-circle"
                  style="color: #c9c9c9; margin-left: 5px; cursor: pointer;"
                  aria-hidden="true" data-toggle="tooltip" data-placement="top"
                  data-original-title="{{__('common.PrincipalForSaleTooltip')}}"></i></span>
    </th>

    <!-- Investment amount -->
    <th scope="col" class="center aligned interest-rate">
        {!! trans('common.InvestmentAmount') !!}
        <br>
        <span> <i class="fa fa-info-circle"
                  style="color: #c9c9c9; margin-left: 5px; cursor: pointer;"
                  aria-hidden="true" data-toggle="tooltip" data-placement="top"
                  data-original-title="{{__('common.SalePriceTooltip')}}"></i></span>
    </th>
     <th scope="col" class="center aligned">
      {!! trans('common.AssignmentAgreementTable') !!}
     </th>
</tr>
