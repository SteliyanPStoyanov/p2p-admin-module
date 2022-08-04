<table class="table">
                            <thead>
                            <tr>
                                <th scope="col">{{__('common.Name')}}</th>
                                <th scope="col">{{__('common.Type')}}</th>
                                <th scope="col">{{__('common.Active')}}</th>
                                <th scope="col" class="tableHeader">{{__('common.CreatedAt')}}</th>
                                <th scope="col" class="tableHeader">{{__('common.CreatedBy')}}</th>
                                <th scope="col" class="tableHeader">{{__('common.UpdatedAt')}}</th>
                                <th scope="col" class="tableHeader">{{__('common.UpdatedBy')}}</th>
                                <th scope="col">{{__('common.Actions')}}</th>
                            </tr>
                            </thead>
                            <tbody id="emailTemplateTable">

                            @foreach($emailTemplates as $template)

                                <tr>
                                    <td>{{ $template->key }}</td>
                                    <td>{{ $template->type }}</td>
                                    <td>{{ $template->active ? __('common.Yes') : __('common.No') }}</td>
                                    <x-timestamps :model="$template"/>
                                    <td class="button-div">
                                        <div class="button-actions">
                                            <x-btn-edit
                                                url="{{ route('communication.emailTemplate.edit', $template->email_template_id) }}"/>
                                            <x-btn-delete
                                                url="{{ route('communication.emailTemplate.delete', $template->email_template_id) }}"/>
                                            @if($template->active)
                                                <x-btn-disable
                                                    url="{{ route('communication.emailTemplate.disable', $template->email_template_id) }}"/>
                                            @else
                                                <x-btn-enable
                                                    url="{{ route('communication.emailTemplate.enable', $template->email_template_id) }}"/>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr id="pagination-nav">
                                <td colspan="10">
                                    {{ $emailTemplates->links() }}
                                </td>
                            </tr>
                            </tfoot>
                        </table>
