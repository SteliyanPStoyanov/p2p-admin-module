<table class="table">
    <thead>
    <tr>
        <th scope="col">{{__('common.Name')}}</th>
        <th scope="col">{{__('common.Priority')}}</th>
        <th scope="col">{{__('common.Active')}}</th>
        <th scope="col">{{__('common.CreatedAt')}}</th>
        <th scope="col">{{__('common.CreatedBy')}}</th>
        <th scope="col">{{__('common.UpdatedAt')}}</th>
        <th scope="col">{{__('common.UpdatedBy')}}</th>
        <th scope="col">{{__('common.Actions')}}</th>
    </tr>
    </thead>
    <tbody id="rolesTable">
    @foreach($roles as $role)
        <tr>
            <td>{{ $role->name }}</td>
            <td>{{ $role->priority }}</td>
            <td>{{ $role->active ? __('common.Yes') : __('common.No') }}</td>
            <td>{{ $role->created_at != null ? $role->created_at->format('d-m-Y H:i') : '' }}</td>
            <td>{{ $role->getCreateAdmin() }}</td>
            <td>{{ $role->updated_at != null ? $role->updated_at->format('d-m-Y H:i') : '' }}</td>
            <td>{{ $role->getUpdateAdmin() }}</td>
            <td class="button-div">
                <div class="button-actions">
                    <x-btn-edit url="{{ route('admin.roles.edit', $role->id) }}"/>
                    <x-btn-delete url="{{ route('admin.roles.delete', $role->id) }}"/>
                    @if($role->active)
                        <x-btn-disable url="{{ route('admin.roles.disable', $role->id) }}"/>
                    @else
                        <x-btn-enable url="{{ route('admin.roles.enable', $role->id) }}"/>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="8">
            {{ $roles->links() }}
        </td>
    </tr>
    </tfoot>
</table>
