<table class="table">
                            <thead>
                            <tr>
                                <th scope="col">{{__('common.Name')}}</th>
                                <th scope="col">{{__('common.Type')}}</th>
                                <th scope="col">{{__('common.Version')}}</th>
                                <th scope="col">{{__('common.Active')}}</th>
                                <th scope="col" class="tableHeader">{{__('common.CreatedAt')}}</th>
                                <th scope="col" class="tableHeader">{{__('common.CreatedBy')}}</th>
                                <th scope="col" class="tableHeader">{{__('common.UpdatedAt')}}</th>
                                <th scope="col" class="tableHeader">{{__('common.UpdatedBy')}}</th>
                                <th scope="col">{{__('common.Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody id="emailTemplateTable">

                            @foreach($templates as $template)

                                <tr>
                                    <td>{{ $template->name }}</td>
                                    <td>{{ $template->type }}</td>
                                    <td>{{ $template->version }}</td>
                                    <td>{{ $template->active ? __('common.Yes') : __('common.No') }}</td>
                                    <x-timestamps :model="$template"/>
                                    <td class="button-div">
                                        <div class="button-actions">
                                            <x-btn-edit
                                                url="{{ route('admin.user-agreement.edit', $template->contract_template_id) }}"/>
                                            <x-btn-delete
                                                url="{{ route('admin.user-agreement.delete', $template->contract_template_id) }}"/>
                                            @if(!$template->isActive())
                                                <x-btn-enable
                                                    url="{{ route('admin.user-agreement.enable', $template->contract_template_id) }}"/>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr id="pagination-nav">
                                <td colspan="10">
                                    {{ $templates->links() }}
                                </td>
                            </tr>
                            </tfoot>
                        </table>
