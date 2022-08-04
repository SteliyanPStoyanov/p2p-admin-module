{{--desktop version--}}

<table class="ui table loans-table-desktop available-loans-table">
    <thead>
    <tr>
        <th class="center aligned">{!! trans('static.HomePageAvailableLoansTableCountry')!!}</th>
        <th class="center aligned">{!! trans('static.HomePageAvailableLoansTableLoanID')!!}</th>
        <th class="center aligned">{!! trans('static.HomePageAvailableLoansTableIssueDate')!!}</th>
        <th class="center aligned">{!! trans('static.HomePageAvailableLoansTableLoanType')!!}</th>
        <th class="center aligned">{!! trans('static.HomePageAvailableLoansTableLoanOriginator')!!}</th>
        <th class="center aligned">{!! trans('static.HomePageAvailableLoansTableInterestRate')!!}</th>
        <th class="center aligned">{!! trans('static.HomePageAvailableLoansTableTerm')!!}</th>
        <th class="center aligned">{!! trans('static.HomePageAvailableLoansTableLoanAmount')!!}</th>
        <th class="center aligned">{!! trans('static.HomePageAvailableLoansTableAvailable')!!}</th>
        <th class="center aligned no-border"></th>
    </tr>
    </thead>
    <tbody>

    @foreach($loans as $loan)

        <tr>
            <td class="center aligned">
                <img class="country-flag-circle" alt="bulgaria-flag"
                     src="{{ assets_version(url('/') . '/images/countries/' . mb_strtolower($loan->country->name) . '-flag-round-icon-32.png') }}">
                <div class="center aligned table-country-name">{{ $loan->country->name }}</div>
            </td>
            <td class="center aligned">
                <a target="_blank" class="link-color" href="{{route('profile.invest.view' ,$loan->loan_id)}}">{{ $loan->loan_id }}</a>
            </td>
            <td class="center aligned">
                {{ \Carbon\Carbon::parse($loan->lender_issue_date)->format('d.m.Y') }}
            </td>
            <td class="center aligned">
                {{ loanType($loan->type) }}
            </td>
            <td class="center aligned">{{ $loan->originator->name }}</td>
            <td class="center aligned">{{ number_format($loan->interest_rate_percent, 1) }}%</td>
            <td class="center aligned">
                {{termFormat($loan->final_payment_date)}}
            </td>
            <td class="center aligned">{{ amount($loan->amount) }}</td>
            <td class="center aligned">{{ amount($loan->amount_available) }}</td>
            <td class="center aligned no-border no-box"><a
                    class="ui teal button"
                    href="{{route('profile.invest.view' ,$loan->loan_id)}}">{{__('static.HomePageAvailableLoansTableInvestButton')}}</a>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>

{{--mobile version--}}

<table class="ui table loans-table-mobile available-loans-table">

    <tbody class="mobile-table-body">
    @foreach($loans as $loan)
        <tr>
            <td class="left floated left aligned">{{__('static.HomePageAvailableLoansTableCountry')}}</td>
            <td class="right floated right aligned"><img class="country-flag-circle" alt="bulgaria-flag"
                                                         src="{{ assets_version(url('/') . '/images/countries/' . mb_strtolower($loan->country->name) . '-flag-round-icon-32.png') }}">
                <div class="right floated right aligned table-country-name">{{ $loan->country->name }}</div></td>
        </tr>
        <tr>
            <td class="left floated left aligned">{{__('static.HomePageAvailableLoansTableLoanID')}}</td>
            <td class="right floated right aligned"><a target="_blank" class="link-color"
                                                       href="{{route('profile.invest.view' ,$loan->loan_id)}}">{{ $loan->loan_id }}</a>
            </td>
        </tr>
        <tr>
            <td class="left floated left aligned">{{__('static.HomePageAvailableLoansTableIssueDate')}}</td>
            <td class="right floated right aligned">
                {{ \Carbon\Carbon::parse($loan->lender_issue_date)->format('d.m.Y') }}
            </td>
        </tr>
        <tr>
            <td class="left floated left aligned">{{__('static.HomePageAvailableLoansTableLoanType')}}</td>
            <td class="right floated right aligned">{{loanType($loan->type)}}</td>
        </tr>
        <tr>
            <td class="left floated left aligned">{{__('static.HomePageAvailableLoansTableLoanOriginatorMobile')}}</td>
            <td class="right floated right aligned">{{ $loan->originator->name  }}</td>
        </tr>
        <tr>
            <td class="left floated left aligned">{{__('static.HomePageAvailableLoansTableLoanAmountMobile')}}</td>
            <td class="right floated right aligned">{{ amount($loan->amount) }}</td>
        </tr>
        <tr>
            <td class="left floated left aligned">{{__('static.HomePageAvailableLoansTableInterestRateMobile')}}</td>
            <td class="right floated right aligned">{{ $loan->interest_rate_percent  }} %</td>
        </tr>
        <tr>
            <td class="left floated left aligned">{{__('static.HomePageAvailableLoansTableTerm')}}</td>
            <td class="right floated right aligned">
               {{termFormat($loan->final_payment_date)}}
            </td>
        </tr>
        <tr>
            <td class="left floated left aligned">{{__('static.HomePageAvailableLoansTableAvailableMobile')}}</td>
            <td class="right floated right aligned">{{ amount($loan->amount_available) }}</td>
        </tr>
        <tr class="mobile-homepage-btn-invest">
            <td class="right floated right aligned"><a
                    class="ui teal button"
                    href="{{route('profile.invest.view' ,$loan->loan_id)}}">{{__('static.HomePageAvailableLoansTableInvestButton')}}</a>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
