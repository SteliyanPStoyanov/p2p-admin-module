@extends('profile::layouts.app')

@section('style')
    <link href="{{ assets_version(asset('vendor/filepond/filepond.css'))}}" rel="stylesheet"/>
@endsection

@section('title',  'Verify your company - ')

@section('content')
    <div class="row">

        <div class="col-lg-6 mx-auto text-center text-black">
            <div id="formContent" class="w-100" style="max-width: 100%;">
                <div class="row">
                    <div class="col-12">
                        <h2 class="d-block font-weight-bold mt-5 w-100 text-alt-gray">
                            Verify your company
                        </h2>
                        <p class="mt-4 d-block w-100 text-left text-black mb-2">
                            This is the last step. To verify your company please upload the following list of
                            documents:
                        </p>
                        <p class="mt-4 d-block w-100 text-left text-black mb-5">
                            • Company registration document.<br>
                            • Registered address (unless stated in the registration document).<br>
                            • Document specifying you as the rightful representative of the company.<br>
                            • First name, last name, personal ID code and date of birth of each
                            shareholder.
                        </p>
                    </div>
                </div>
                @if (session('fail'))
                    <div class="col-12">
                        <div class="p-1 my-4 bg-danger text-left">{{session('fail')}}</div>
                    </div>
                @endif
                <form method="POST" class="row" action="{{ route('profile.verify.uploadCompanyDoc') }}"
                      autocomplete="off" enctype="multipart/form-data">
                    <div class="form-group w-100 row doc uploadContainer">
                        <div class="col-12">
                            <input type="file" name="document_file[]" id="companyUpload">
                        </div>
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
    <script src="{{ assets_version(asset('vendor/filepond/filepond-plugin-file-validate-size.js'))}}"></script>
    <script src="{{ assets_version(asset('vendor/filepond/filepond-plugin-file-validate-type.js'))}}"></script>
    <script src="{{ assets_version(asset('vendor/filepond/filepond.js'))}}"></script>
    <script>
        FilePond.registerPlugin(
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType,
        );
        const inputElement = document.querySelector('input[id="companyUpload"]');
        const pond = FilePond.create(inputElement, {
            acceptedFileTypes: ['image/*', 'application/pdf'],
            allowMultiple: true,
            maxFileSize: '10MB',
            server: {
                load: (source, load, error, progress, abort, headers) => {
                }

            },
            // files array
            files: {!! $files !!}
        });
        FilePond.setOptions({
            server: {

                process: '{{route('file.pond.store')}}',
                revert: '{{route('file.pond.remove')}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                remove: (source, load, error) => {
                    // Should somehow send `source` to server so server can remove the file with this source
                    $.ajax({
                        type: 'get',
                        url: '{{ route('file.pond.removeOldFile')}}',
                        data: {data: source},
                        success: function (data) {
                            $('#table-myInvests').html(data);
                        },
                    });
                    // Can call the error method if something is wrong, should exit after
                    error('oh my goodness');

                    // Should call the load method when done, no parameters required
                    load();
                }
            }
        });

    </script>
@endpush
