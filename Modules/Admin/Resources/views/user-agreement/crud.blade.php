@extends('layouts.app')
@section('style')
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
@endsection
@section('content')
    <div class="row">
        <form class="col-lg-12" id="emailTemplateForm" method="POST"
              action="{{
                        !empty($template) ?
                    route('admin.user-agreement.update', $template->contract_template_id)
                    : route('admin.user-agreement.store')
                    }}"
              accept-charset="UTF-8">
            <div class="row">
                <div class="col-lg-3 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            {{ admin_csrf_field() }}
                            <div class="form-group">
                                <label for="name">{{ __('common.Name') }}</label>
                                <input class="form-control w-100" type="text" name="name" id="name"
                                       value="{{old('name') ?? ($template->name ?? '')}}">
                            </div>
                            <div class="form-group">
                                <label for="version">{{ __('common.Version') }}</label>
                                <input class="form-control w-100" type="text" name="version" id="version"
                                       value="{{old('version') ?? ($template->version ?? '')}}">
                            </div>
                            <div class="form-group">
                                <label for="type">{{ __('common.Type') }}</label>
                                <select class="form-control w-100" name="type" id="type">
                                    @foreach($types as $type)
                                        <option
                                            value="{{$type}}"
                                            @if(old('type') == $type || (!empty($template) && $template->type == $type))
                                            selected
                                            @endif
                                        >{{$type}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start_date">{{ __('common.StartDate') }}</label>
                                <input class="form-control w-100 singleDataPicker" autocomplete="off" type="text"
                                       name="start_date" id="start_date"
                                       value="{{old('start_date') ?? ($template->start_date ?? '')}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="content"
                                       class="control-label required">{{ __('common.Content') }}</label>
                                <textarea class="form-control editor" name="text"
                                          id="content">
                                        {{old('text') ?? ($template->text ?? '')}}
                            </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-button-bottom-bar
                url="{{route('admin.user-agreement.list')}}"
                saveEditName="{{ !empty($template) ? __('common.Update') : __('common.Create')}}"
                cancelName="{{ __('common.Cancel') }}"
            />
        </form>
    </div>


    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ assets_version(asset('dist/tinymce/tinymce.js')) }}"></script>
    <script>
        $('.singleDataPicker').daterangepicker({
            autoUpdateInput: false,
            "singleDatePicker": true,
            "autoApply": true,
            locale: {
                format: 'YYYY-MM-DD',
            }
        });
        $('.singleDataPicker').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        tinymce.init({
            selector: 'textarea',
            height: '820px',
            plugins: "fullpage"
        });
    </script>
@endpush
