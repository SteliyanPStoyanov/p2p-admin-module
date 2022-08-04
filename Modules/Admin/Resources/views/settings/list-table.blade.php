<table class="table">
    <thead>
    <tr>
        <th scope="col" class="tableHeader">{{ __('common.Name') }}</th>
        <th scope="col" class="tableHeader">{{ __('common.Description') }}</th>
        <th scope="col" class="tableHeader">{{ __('common.SettingsDefaultValue') }}</th>
        <th scope="col" class="tableHeader">{{ __('common.Active') }}</th>
        <th scope="col" class="tableHeader">{{__('common.CreatedAt')}}</th>
        <th scope="col" class="tableHeader">{{__('common.CreatedBy')}}</th>
        <th scope="col" class="tableHeader">{{__('common.UpdatedAt')}}</th>
        <th scope="col" class="tableHeader">{{__('common.UpdatedBy')}}</th>
        <th scope="col" class="tableHeader">{{ __('common.Actions') }}</th>
    </tr>
    </thead>
    <tbody id="settingsTable">
    @foreach($settings as $setting)
        <tr>
            <td>{{ $setting->name }}</td>
            <td>{{ $setting->description }}</td>
            <td>{{ $setting->default_value }}</td>
            <td>{{ $setting->active ? __('common.Yes') : __('common.No') }}</td>
            <x-timestamps :model="$setting"/>
            <td class="button-div">
                <div class="button-actions">

                    <x-btn-edit
                        url="{{ route('admin.settings.edit', $setting->setting_key) }}"/>
                    <x-btn-delete
                        url="{{ route('admin.settings.delete', $setting->setting_key) }}"/>
                    @if($setting->active)
                        <x-btn-disable
                            url="{{ route('admin.settings.disable', $setting->setting_key) }}"/>
                    @else
                        <x-btn-enable
                            url="{{ route('admin.settings.enable', $setting->setting_key) }}"/>
                    @endif
                </div>

            </td>
        </tr>
    @endforeach

    </tbody>
    <tfoot>
    <tr id="pagination-nav">
        <td colspan="10">
            {{ $settings->links() }}
        </td>
    </tr>
    </tfoot>
</table>
