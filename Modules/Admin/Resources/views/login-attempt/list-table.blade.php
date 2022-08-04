<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Id')}}</th>
        <th scope="col">{{__('common.DateTime')}}</th>
        <th scope="col">{{__('common.Email')}}</th>
        <th scope="col">{{__('common.IP')}}</th>
        <th scope="col">{{__('common.Browser')}}</th>
        <th scope="col">{{__('common.Active')}}</th>
        <th scope="col">
            <a href="#"
               data-href="{{ route('admin.login-attempt.delete-all') }}" role="button"
               aria-pressed="true" title="Delete" data-toggle="modal" data-target="#confirm-delete"
               class="btn btn-danger btn-extra-sm float-right"
               button-type="delete">
                <span>{{__('common.ClearAll')}} <i aria-hidden="true" class="fa fa-trash"></i> </span>
            </a>
        </th>
    </tr>
    </thead>

    <tbody id="table">
    @foreach ($loginAttempts as $loginAttempt)
        <tr>
            <td>{{ $loginAttempt->id }}</td>
            <td>{{ $loginAttempt->datetime }}</td>
            <td>{{ $loginAttempt->email}}</td>
            <td>{{ $loginAttempt->ip }}</td>
            <td>{{ $loginAttempt->device }}</td>
            <td>{{ $loginAttempt->active ? __('common.Yes') : __('common.No') }}</td>
            <td>
                <a href="#" style="float: left; margin-left: 10px;"
                   data-href="{{route('admin.login-attempt.remove', $loginAttempt->id)}}" role="button"
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
            {{ $loginAttempts->links() }}
        </td>
    </tr>
    </tfoot>
</table>
