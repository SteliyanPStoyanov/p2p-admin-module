<table>
    <thead>
    <tr>
        <th colspan="2" style="background-color: #afd095">Bonus to Investor</th>
        <th colspan="2" style="background-color: #ffe994;">Bonus from Referral</th>
        <th>Amount</th>
        <th>Date</th>
        <th>Handled</th>
        <th>Bank Account Investor</th>
        <th>Bank Account Referral</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $bonus)
        <tr>
            <th style="background-color: #afd095">{{'ID#'.$bonus->investor_id}} </th>
            <th style="background-color: #afd095">{{$bonus->investor->fullName()}}</th>
            <th style="background-color: #ffe994;">{{'ID#'.$bonus->from_investor_id}} </th>
            <th style="background-color: #ffe994;">{{$bonus->investorReferral->fullName()}}</th>
            <th>{{amount($bonus->amount)}}</th>
            <th>{{showDate($bonus->date)}}</th>
            <th>{{$bonus->handled}}</th>
            <th style="@if(empty($bonus->investor->getMainBankAccountId())) color:red; @endif">
                {{$bonus->investor->investor_id}}
                {{$bonus->investor->getMainBankAccountId() ? 'have bank account' : 'no bank account' }}
            </th>
            <th style="@if(empty($bonus->investorReferral->getMainBankAccountId())) color:red; @endif">
                {{$bonus->investorReferral->investor_id}}
                {{$bonus->investorReferral->getMainBankAccountId() ? 'have bank account' : 'no bank account' }}
            </th>
        </tr>
    @endforeach
    </tbody>
</table>
