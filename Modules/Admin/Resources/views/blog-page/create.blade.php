@extends('layouts.app')

@section('style')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css"
          integrity="sha512-xmGTNt20S0t62wHLmQec2DauG9T+owP9e6VU8GigI0anN7OXLip9i7IwEhelasml2osdxX71XcYm6BQunTQeQg=="
          crossorigin="anonymous"/>

    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    <div class="row" style="padding-left: 15px;">
        <form method="POST" class="col-lg-12"
              action="{{route('admin.blog-page.store')}}"
              accept-charset="UTF-8" enctype='multipart/form-data'>
            {{ admin_csrf_field() }}
            <div class="row">
                <div class="col-lg-3 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title" class="control-label required">{{__('common.BlogPageTitle')}}</label>
                                <input class="form-control" required="required" minlength="2"
                                       maxlength="50" name="title"
                                       type="text" id="name">
                            </div>

                            <div class="form-group">
                                <label for="date"
                                       class="control-label">{{__('common.Date')}}</label>
                                <input type="text" autocomplete="off" class="form-control w-100 singleDataPicker"
                                       value="{{ ($blogPage->date ?? '')}}"
                                       name="date" id="date">
                            </div>
                            <div class="form-group">
                                <label for="tags"
                                       class="control-label">{{__('common.BlogTags')}}</label>
                                <input data-role="tagsinput" class="form-control tagsinput" name="tags"
                                       type="text"
                                       id="blogPageTags">
                            </div>

                            <div class="input-group mb-2">
                                <div class="custom-file">
                                    <label for="image" class="custom-file-label">{{ __('common.Image') }}</label>
                                    <input type="file" name="images[]" multiple="" class="custom-file-input form-control"
                                           id="customFile">
                                </div>
                            </div>

                            <input type="hidden" name="administrator_id"
                                   value="{{\Auth::user()->administrator_id}}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="content"
                                       class="control-label required">{{__('common.BlogContent')}}</label>
                                <textarea class="form-control" minlength="2" name="content"
                                          cols="20" rows="12" id="blogContent"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 btns-form-panel">
                    <x-button-bottom-bar
                        url="{{route('admin.blog-page.list')}}"
                        saveEditName="{{ __('common.Create') }}"
                        cancelName="{{ __('common.Cancel') }}"
                    />
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"
            integrity="sha512-VvWznBcyBJK71YKEKDMpZ0pCVxjNuKwApp4zLF3ul+CiflQi6aIJR+aZCP/qWsoFBA28avL5T5HA+RE+zrGQYg=="
            crossorigin="anonymous"></script>

    <script src="{{ assets_version(asset('dist/tinymce/tinymce.js')) }}"></script>
    <script>
        $('.singleDataPicker').daterangepicker({
            autoUpdateInput: false,
            autoApply: true,
            "singleDatePicker": true,
            locale: {
                format: 'DD.MM.YYYY',
            }
        });
        $('.singleDataPicker').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY'));
        });

        tinymce.init({
            selector: 'textarea',
            height: '400px',
            plugins: "fullpage"
        });
        $('.tagsinput').tagsinput({
            allowDuplicates: false,
            trimValue: true,
            confirmKeys: [9, 13, 32, 44, 188, 190]
        });

    </script>
@endpush
