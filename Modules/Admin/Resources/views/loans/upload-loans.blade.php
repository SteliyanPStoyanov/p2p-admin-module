@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection
@section('content')
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div id="btns-panel">
                                <form method="POST" enctype="multipart/form-data"
                                      action="{{ route('admin.upload-loan-document') }}">
                                    {{ admin_csrf_field() }}
                                    <div class="input-group mb-2 justify-content-flex-end">
                                        <div class="custom-file wm-30 ">
                                            <label for="import_file" class="custom-file-label w-100"
                                                   style="left:auto; text-align: left">
                                                {{ __('common.ImportLoans') }}
                                            </label>
                                            <input class="custom-file-input" name="import_file" type="file"
                                                   id="import_file">
                                        </div>
                                        <button
                                            class="btn btn-success default-btn-last"
                                            type="submit"
                                            style="margin-left: 1%"
                                            name="ImportSite"
                                            value="ImportSite"
                                        >
                                            {{__('common.ImportSite')}}
                                        </button>
                                        <button
                                            class="btn btn-success default-btn-last"
                                            type="submit"
                                            style="margin-left: 1%"
                                            name="ImportOffice"
                                            value="ImportOffice"
                                        >
                                            {{__('common.ImportOffice')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div id="table-upload-loans">
                            @include('admin::loans.components.upload-loans-list')
                        </div>
                    </div>
                    <div id="executeResponse">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(".addToAfranga").on('submit', function (event) {
            event.preventDefault();
            $.ajax({
                type: 'post',
                url: $(this).attr('action'),
                data: $(this).serialize(),

                success: function (data) {
                    $('#executeResponse').html(data);
                },
                error: function (jqXHR) {
                    let errorHandler = $("#errorHandlerAjax");

                    let errors = '<div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">\n';
                    errors += jqXHR.responseJSON.message;
                    errors += '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                        '    <span aria-hidden="true">&times;</span>\n' +
                        '  </button>\n' +
                        '</div>';
                    errorHandler.html(errors);
                }
            });
        });
    </script>
@endpush
