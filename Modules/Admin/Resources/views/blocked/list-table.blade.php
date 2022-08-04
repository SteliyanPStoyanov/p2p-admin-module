<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Id')}}</th>
        <th scope="col">{{__('common.Reason')}}</th>
        <th scope="col">{{__('common.Email')}}</th>
        <th scope="col">{{__('common.IP')}}</th>
        <th scope="col">{{__('common.Browser')}}</th>
        <th scope="col">{{__('common.Active')}}</th>
        <th scope="col">{{__('common.BlockedAt')}}</th>
        <th scope="col">{{__('common.BlockedTill')}}</th>
        <th scope="col">
            <a href="#"
               data-href="{{ route('admin.blocked-ip.delete-all') }}" role="button"
               aria-pressed="true" title="Delete" data-toggle="modal" data-target="#confirm-delete"
               class="btn btn-danger btn-extra-sm float-right"
               button-type="delete">
                <span>{{__('common.ClearAll')}} <i aria-hidden="true" class="fa fa-trash"></i> </span>
            </a>
        </th>
    </tr>
    </thead>

    <tbody id="table">
    @foreach ($blockedIps as $blockIp)
        <tr>
            @php
                $relation = $blockIp->getRelatedRecord();
                $email = $relation->email ?? '';
                $device = $relation->device ?? '';
            @endphp
            <td>{{ $blockIp->id }}</td>
            <td>{{ $blockIp->reason }}</td>
            <td>{{ $blockIp->email }}</td>
            <td>{{ $blockIp->ip }}</td>
            <td>{{ $device }}</td>
            <td>{{ $blockIp->active ? __('common.Yes') : __('common.No') }}</td>
            <td>{{ formatDate($blockIp->created_at, 'd.m.Y H:i') }}</td>
            <td>{{ formatDate($blockIp->blocked_till, 'd.m.Y H:i') }}</td>
            <td>
                <a href="#" style="float: left; margin-left: 10px;"
                   data-href="{{route('admin.blocked-ip.remove', $blockIp->id)}}"
                   role="button"
                   aria-pressed="true" title="Delete" data-toggle="modal" data-target="#confirm-delete"
                   class="btn btn-danger btn-extra-sm"
                   button-type="delete">
                    <span><i aria-hidden="true" class="fa fa-trash"></i></span>
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>

    <tfoot>
    <tr id="pagination-nav">
        <td colspan="10">
            {{ $blockedIps->links() }}
        </td>
    </tr>
    </tfoot>
</table>
