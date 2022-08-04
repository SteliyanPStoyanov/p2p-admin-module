<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Name')}}</th>
        <th scope="col">{{__('common.Phone')}}</th>
        <th scope="col">{{__('common.Email')}}</th>
        <th scope="col">{{__('common.AdminUserName')}}</th>
        <th scope="col">{{__('common.Active')}}</th>
        <th scope="col">{{__('common.CreatedAt')}}</th>
        <th scope="col">{{__('common.CreatedBy')}}</th>
        <th scope="col">{{__('common.UpdatedAt')}}</th>
        <th scope="col">{{__('common.UpdatedBy')}}</th>
        <th scope="col">{{__('common.Actions')}}</th>
    </tr>
    </thead>
    <tbody id="administratorsTable">
    @foreach($administrators as $administrator)
        <tr>
            <td>{{ $administrator->first_name }} {{ $administrator->middle_name }} {{ $administrator->last_name }} </td>
            <td>{{ $administrator->phone }}</td>
            <td>{{ $administrator->email }}</td>
            <td>{{ $administrator->username }}</td>
            <td>{{ $administrator->active ? __('common.Yes') : __('common.No') }}</td>
            <x-timestamps :model="$administrator"/>
            <td class="button-div">
                <div class="button-actions">
                    <x-btn-edit
                        url="{{ route('admin.administrators.edit', $administrator->administrator_id) }}"/>
                    <x-btn-delete
                        url="{{ route('admin.administrators.delete', $administrator->administrator_id) }}"/>
                    @if($administrator->active)
                        <x-btn-disable
                            url="{{ route('admin.administrators.disable', $administrator->administrator_id) }}"/>
                    @else
                        <x-btn-enable
                            url="{{ route('admin.administrators.enable', $administrator->administrator_id) }}"/>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="10">
            {{ $administrators->links() }}
        </td>
    </tr>
    </tfoot>
</table>
