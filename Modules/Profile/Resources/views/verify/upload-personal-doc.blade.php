@extends('profile::layouts.app')

@section('title',  'Upload documents - ')

@section('content')
    <div class="row">

        <div class="col-lg-6 mx-auto text-center text-black">
            <div id="formContent" class="w-100" style="max-width: 100%;">
                <div class="row">
                    <div class="col-12">
                        <h2 class="d-block font-weight-bold mt-5 w-100 text-alt-gray">
                            {{ __('common.UploadYourID') }}
                        </h2>
                        <p class="mt-4 d-block w-100 text-left text-black">
                            {{ __('common.YouAreAlmostDoneWeNowNeed') }}
                        </p>
                        <p class="mt-4 d-block w-100 text-left text-black">
                            {{ __('common.ForEuCitizensPleaseUpload') }}
                            <br/>
                            {{ __('common.ForNonEuCitizensPleaseUpload') }}
                            <br/>
                            <br/>
                            {{ __('common.ForEveryoneSelfie') }}
                        </p>
                    </div>
                </div>

                <form method="POST" class="row" action="{{ route('profile.verify.uploadPersonalDocSubmit') }}"
                      autocomplete="off" enctype="multipart/form-data">

                    <div class="form-group w-100 mt-5 row">
                        <div class="col-12 text-left">

                            <label for="document_type_id" class="text-left"> {{ __('common.DocumentType') }}</label>
                            <select id="document_type_id" name="document_type_id" class="form-control w-100 text-black">
                                @foreach($documentTypes as $documentType)
                                    <option
                                        value="{{$documentType->document_type_id}}">{{$documentType->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group w-100 row doc uploadContainer">
                        <div class="col-12">
                            <div class="custom-file">
                                <input type="file" name="document_file[front]" class="custom-file-input w-100"
                                       id="customFile"
                                       size="1024" accept="image/*,application/pdf">
                                <label class="custom-file-label w-100 change-label-text text-left"
                                       for="customFile">{{ __('common.IDFront') }}</label>
                            </div>
                        </div>
                         @if(!empty($errors) && $errors->has('document_file.front'))
                                <div class="row">
                                    <div
                                        class="text-left pl-1 mb-1 bg-danger text-white">{{$errors->first('document_file.front')}}</div>
                                </div>
                            @endif
                        <div class="col-12 IdCard">
                            <div class="custom-file mt-4">
                                <input type="file" name="document_file[back]" class="custom-file-input w-100"
                                       id="customFile"
                                       size="1024" accept="image/*,application/pdf">
                                <label class="custom-file-label w-100 text-left"
                                       for="customFile">{{ __('common.IDBack') }}</label>
                            </div>

                        </div>
                         @if(!empty($errors) && $errors->has('document_file.back'))
                                <div class="row">
                                    <div
                                        class="text-left pl-1 mb-1 bg-danger text-white">{{$errors->first('document_file.back')}}</div>
                                </div>
                            @endif
                        <div class="col-12 selfie">
                            <div class="custom-file mt-4">
                                <input type="file" name="document_file[selfie]" class="custom-file-input w-100"
                                       id="customFile"
                                       size="1024" accept="image/*,application/pdf">
                                <label class="custom-file-label w-100 text-left"
                                       for="customFile">{{ __('common.SelfieFile') }}</label>
                            </div>
                        </div>
                          @if(!empty($errors) && $errors->has('document_file.selfie'))
                                <div class="row">
                                    <div
                                        class="text-left pl-1 mb-1 bg-danger text-white">{{$errors->first('document_file.selfie')}}</div>
                                </div>
                            @endif
                    </div>
                    @csrf

                    <div class="form-group w-100 row">
                        <div class="col-12 mt-3">
                            <input id="form_submit" class="ui teal button w-100" type="submit"
                                   value="{{ __('common.Continue') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

    <script>
        let idCard = {{config('profile.documentTypeIdIdCard')}};
        let IdCardBack = '{{ __('common.IDBack') }}';
        let IDFront = '{{ __('common.IDFront') }}';
        let ChooseFile = '{{ __('common.ChooseFile') }}';

        let fileHtml = '<div class="custom-file mt-4 text-left"><input type="file" name="document_file[back]" class="custom-file-input w-100" id="customFile" size="4080" accept="image/*,application/pdf"><label class="custom-file-label w-100" for="customFile">' + IdCardBack + '</label></div>';
        $('#document_type_id').change(function () {
            if (idCard == $(this).val()) {
                $('.IdCard').html(fileHtml);
                $('.change-label-text').html(IDFront);
            } else {
                $('.IdCard .custom-file').remove();
                $('.change-label-text').html(ChooseFile);
            }
            $('.custom-file-input').val('');
        });
        $('.custom-file-input').on('change', function () {
            //get the file name
            let fullPath = $(this).val();
            let fileName = fullPath.replace(/^.*[\\\/]/, '');
            //replace the "Choose a file" label
            $(this).parent().find('.custom-file-label').html(fileName);
        });
    </script>
@endpush
