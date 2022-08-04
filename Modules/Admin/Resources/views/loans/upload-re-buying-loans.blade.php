@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection
@section('content')
    <div class="row" id="container-row">
        <div class="col-lg-8">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div class="row">
                        <div id="btns-panel">
                            <form method="POST" enctype="multipart/form-data"
                                  action="{{ route('admin.re-buying-loans.store') }}">
                                {{ admin_csrf_field() }}
                                <div class="input-group mb-2 justify-content-flex-end">
                                    <div class="custom-file wm-30">
                                        <label for="import_file" class="custom-file-label w-100"
                                               style="left:auto; text-align: left">
                                            {{ __('common.ImportUnlistedLoans') }}
                                        </label>
                                        <input class="custom-file-input" name="import_file" type="file"
                                               id="import_file">
                                    </div>
                                    <button
                                        class="btn btn-success default-btn-last"
                                        type="submit"
                                        style="margin-left: 1%">
                                        {{__('common.Import')}}
                                    </button>

                                </div>
                            </form>
                        </div>
                    </div>


                    <div class="table-responsive">
                        <div id="table-upload-loans">
                            @include('admin::loans.components.upload-re-buying-loans-list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
