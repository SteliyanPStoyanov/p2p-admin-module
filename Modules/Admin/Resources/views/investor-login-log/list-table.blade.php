<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Id')}}</th>
        <th scope="col">{{__('common.InvestorId')}}</th>
        <th scope="col">{{__('common.IP')}}</th>
        <th scope="col">{{__('common.Browser')}}</th>
        <th scope="col">{{__('common.Active')}}</th>
        <th scope="col">{{__('common.DateTime')}}</th>
        <th scope="col">{{__('common.Actions')}}</th>
    </tr>
    </thead>

    <tbody id="table">
    @foreach ($investorLoginLogs as $investorLoginLog)
        <tr>
            <td>{{ $investorLoginLog->investor_login_log_id }}</td>
            <td><a target="_blank"
                   href="{{route('admin.investors.overview',$investorLoginLog->investor_id) }}">#{{$investorLoginLog->investor_id}}</a>
            </td>
            <td>{{ $investorLoginLog->ip }}</td>
            <td>{{ $investorLoginLog->device }}</td>
            <td>{{ $investorLoginLog->active ? __('common.Yes') : __('common.No') }}</td>
            <td>{{ $investorLoginLog->created_at }}</td>
            <td>
                <a href="#" style="float: left; margin-left: 10px;"
                   data-href="{{route('admin.investor-login-log.remove', $investorLoginLog->investor_login_log_id)}}"
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
            {{ $investorLoginLogs->links() }}
        </td>
    </tr>
    </tfoot>
</table>
