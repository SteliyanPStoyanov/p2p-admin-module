@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="administratorForm" class="form-inline card-body"
                      action="{{ route('admin.administrators.list') }}"
                      method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2">
                            <input name="name" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByName')}}"
                                   value="{{ session($cacheKey . '.name') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="phone" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByPhone')}}"
                                   value="{{ session($cacheKey . '.phone') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input name="email" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByEmail')}}"
                                   value="{{ session($cacheKey . '.email') }}">
                        </div>
                        <div class="form-group col-lg-2">
                            <x-select-active active="{{ session($cacheKey . '.active') }}"/>
                        </div>
                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="createdAt" class="form-control w-100"
                                   id="createdAt"
                                   value="{{ session($cacheKey . '.createdAt') }}"
                                   placeholder="{{__('common.FilterByCreatedAt')}}">
                        </div>
                        <div class="form-group col-lg-2">
                            <input type="text" autocomplete="off" name="updatedAt"
                                   class="form-control w-100" id="updatedAt"
                                   value="{{ session($cacheKey . '.updatedAt') }}"
                                   placeholder="{{__('common.FilterByUpdatedAt')}}">
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-lg-12 mt-4">
                            <x-btn-filter/>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div id="btns-panel">
                        <x-btn-create url="{{ route('admin.administrators.create') }}" name="{{ __('common.Create')
                        }}"/>
                    </div>
                    <div class="table-responsive">
                        <div id="table-admins">
                            @include('admin::admin.list-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script>
        loadSimpleDataGrid('{{ route('admin.administrators.refresh') }}', $("#administratorForm"), $("#table-admins"));
    </script>
@endpush
