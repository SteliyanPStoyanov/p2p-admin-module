<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.InvestorReferralNames')}}</th>
        <th scope="col">{{__('common.InvestorEmail')}}</th>
        <th scope="col">{{__('common.ReferralCount')}}</th>
        <th scope="col">{{__('common.ReferralsDeposit')}}</th>
        <th scope="col">{{__('common.ReferralsInvested')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($investorReferrals as $investor)
        <td>{{$investor->investor_names}}</td>
        <td>{{$investor->email}}</td>
        <td><a class="show-referral" data-investorId="{{$investor->investor_id}}"
               href="">
                {{$investor->referrals_count}}
            </a>
        </td>
        <td>{{$investor->referrals_deposit}} &euro;</td>
        <td>{{$investor->invested_total}} &euro;</td>
    </tbody>
    <input type="hidden"  value="{{$investor->investor_id}}">
    @endforeach
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="10">
            {{ $investorReferrals->links() }}
        </td>
    </tr>
    </tfoot>
</table>
