@extends('layouts.app')
@section('style')
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
@endsection
@section('content')
    <div class="row" style="padding-left: 15px;">


        <div class="col-lg-3">
            <form id="emailTemplateForm" method="POST"
                  action="{{
                        !empty($emailTemplate) ?
                    route('communication.emailTemplate.update', $emailTemplate->email_template_id)
                    : route('communication.emailTemplate.store')
                    }}"
                  accept-charset="UTF-8">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title" class="control-label required">{{ __('common.Name') }}</label>
                            <input class="form-control" required="required" minlength="2" maxlength="30"
                                   name="title" type="text"
                                   value="{{ old('title') ?? ($emailTemplate->title ?? '')}}"
                                   id="title">
                        </div>
                        <div class="form-group">
                            <label for="description"
                                   class="control-label required">{{ __('common.Description') }}</label>
                            <input class="form-control" required="required" minlength="2" maxlength="30"
                                   name="description" type="text"
                                   value="{{ old('description') ?? ($emailTemplate->description ?? '')}}"
                                   id="description">
                        </div>
                        <div class="form-group">
                            <label for="body"
                                   class="control-label required">{{ __('common.Body') }}</label>
                            <input class="form-control" required="required" minlength="2" maxlength="30"
                                   name="body" type="text"
                                   value="{{ old('body') ?? ($emailTemplate->body ?? '')}}"
                                   id="body">
                        </div>
                    </div>
                </div>

                <x-button-bottom-bar
                    url="{{route('admin.emailTemplate.list')}}"
                    saveEditName="{{ !empty($emailTemplate) ? __('common.Update') : __('common.Create')}}"
                    cancelName="{{ __('common.Cancel') }}"
                />
            </form>
            @if(!empty($emailTemplate))

                <div class="card">
                    <div class="card-body">
                        <form method="POST"
                              action="{{route('communication.email.sendEmail')}}"
                              accept-charset="UTF-8" class="col-12">
                            @csrf
                            <input class="form-control" required="required" minlength="1" maxlength="30"
                                   name="email_template_id" type="hidden"
                                   value="{{$emailTemplate->email_template_id}}">
                            <div class="form-group">
                                <label for="investor_id"
                                       class="control-label required">{{ __('common.InvestorId') }}</label>
                                <input class="form-control" required="required" minlength="1" maxlength="30"
                                       name="investor_id" type="text"
                                       id="investorId">
                            </div>
                            <div class="form-group mb-5">
                                <label for="email"
                                       class="control-label required">{{ __('common.Email') }}</label>
                                <select name="email" id="email"
                                        class="form-control live-search-city show-tick"
                                >
                                    @foreach(config('communication.test_emails') as $email)
                                        <option
                                            value="{{$email}}">{{$email}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" value="false" name="download"
                                    class="btn btn-success default-btn-last">{{ __('common.Send') }}</button>
                        </form>
                    </div>
                </div>

            @endif
        </div>


        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="content"
                               class="control-label required">{{ __('common.Content') }}</label>
                        <textarea form="emailTemplateForm" class="form-control" name="text"
                                  id="content">
                            {{ old('text') ?? ($emailTemplate->text ?? '')}}

                                </textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('dist/tinymce/tinymce.js') }}"></script>
    <script>
        let height = '500px';
        let templateId = '{{$emailTemplate->email_template_id ?? ''}}';
        if (templateId) {
            height = '820px';
        }
        $('.live-search-city').selectpicker();

        tinymce.init({
            selector: 'textarea',
            plugins: 'fullpage',
            height: height,
        });

    </script>
@endpush
