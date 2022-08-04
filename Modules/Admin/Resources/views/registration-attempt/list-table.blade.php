<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Id')}}</th>
        <th scope="col">{{__('common.DateTime')}}</th>
        <th scope="col">{{__('common.Email')}}</th>
        <th scope="col">{{__('common.IP')}}</th>
        <th scope="col">{{__('common.Browser')}}</th>
        <th scope="col">{{__('common.Active')}}</th>
        <th scope="col" style="min-width: 120px;">
            <a href="#"
               data-href="{{ route('admin.registration-attempt.delete-all') }}" role="button"
               aria-pressed="true" title="Delete" data-toggle="modal" data-target="#confirm-delete"
               class="btn btn-danger btn-extra-sm float-right"
               button-type="delete">
                <span>{{__('common.ClearAll')}} <i aria-hidden="true" class="fa fa-trash"></i> </span>
            </a>
        </th>
    </tr>
    </thead>

    <tbody id="table">
    @foreach ($registrationAttempts as $registrationAttempt)
        <tr>
            <td>{{ $registrationAttempt->id }}</td>
            <td>{{ $registrationAttempt->datetime }}</td>
            <td>{{ $registrationAttempt->email}}</td>
            <td>{{ $registrationAttempt->ip }}</td>
            <td>{{ $registrationAttempt->device }}</td>
            <td>{{ $registrationAttempt->active ? __('common.Yes') : __('common.No') }}</td>

            <td class="text-center">
                <a href="#" style="float: left; margin-left: 10px;"
                   data-href="{{route('admin.registration-attempt.remove', $registrationAttempt->id)}}" role="button"
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
            {{ $registrationAttempts->links() }}
        </td>
    </tr>
    </tfoot>
</table>
