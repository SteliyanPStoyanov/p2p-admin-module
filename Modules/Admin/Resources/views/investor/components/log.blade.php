<div class="container mw-100">
    <div class="row">
        <div class="col-lg-12 d-inline-block float-left">
            <div class="">
                <form id="logForm" class="form-inline mb-5 mr-0 pr-0"
                      action="{{ route('admin.administrators.list') }}"
                      method="GET">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-4">
                            <input type="text" autocomplete="off" name="createdAt"
                                   class="form-control w-100 singleDataPicker"
                                   value="{{ session($cacheKey . '.createdAt') }}"
                                   placeholder="{{__('common.FilterByCreatedAt')}}">
                        </div>
                        <div class="form-group col-lg-4">
                            <input type="text" autocomplete="off" name="key" class="form-control w-100"
                                   id="key"
                                   value="{{ session($cacheKey . '.key') }}"
                                   placeholder="{{__('common.FilterByKey')}}">
                        </div>
                        <div class="form-group col-lg-4">
                            <input type="text" autocomplete="off" name="new_value" class="form-control w-100"
                                   id="new-value"
                                   value="{{ session($cacheKey . '.new_value') }}"
                                   placeholder="{{__('common.FilterByNewValue')}}">
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-lg-12 mt-4">
                            <x-btn-filter/>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <div>
                        <table class="table">
                            <tr>
                                <th>{{__('common.Key')}}</th>
                                <th>{{__('common.OldValue')}}</th>
                                <th>{{__('common.NewValue')}}</th>
                                <th>{{__('common.DateTime')}}</th>
                                <th>{{__('common.ChangedBy')}}</th>
                            </tr>
                            <tbody id="investorChangeLogTable">
                            @include('admin::investor.components.change-log-list-table')
                            </tbody>

                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script>
          $('.singleDataPicker').daterangepicker({
            autoUpdateInput: false,
            "singleDatePicker": true,
            "autoApply": true,
            locale: {
                format: 'DD.MM.YYYY',
            }
        });
        $('.singleDataPicker').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY'));
        });
        url = document.location.toString();
        if (url.split('#')[1] === 'log') {
            loadSimpleDataGrid('{{ route('admin.investors-change-logs-refresh', $investor->investor_id) }}', $("#logForm"), $("#investorChangeLogTable"));
        }
    </script>
@endpush

