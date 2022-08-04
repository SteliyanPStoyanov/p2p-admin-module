<div class="modal fade check-modal" id="modal-{{$investor->investor_id}}" tabindex="-1" role="dialog"
     aria-labelledby="modal-{{$investor->investor_id}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content pb-3">
            <div class="modal-header">
                <h5 class="modal-title">
                    <strong>{{__('common.Investor') . ' #'.$investor->investor_id}} - {{$investor->fullName()}}</strong>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <b>{{__('common.Referral')}}</b>
                </div>
                <div style="max-height: 390px; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col" class="text-center">
                                    {{__('common.Id')}}
                                </th>
                                <th scope="col" class="text-center">
                                    {{__('common.Names')}}
                                </th>
                                <th scope="col" class="text-center">
                                    {{__('common.Invested')}}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($referrals as $referral)
                                <tr>
                                    <td class="text-center">
                                        <a target="_blank"
                                           href="{{route('admin.investors.overview', $referral->investor_id)}}"> {{$referral->investor_id}}</a>
                                    </td>
                                    <td class="text-center">
                                        {{$referral->referral_names}}
                                    </td>
                                    <td class="text-center">
                                        {{amount($referral->wallet()->invested)}}
                                    </td>
                                </tr>

                            @empty

                                {{__('common.NoRecords')}}
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
