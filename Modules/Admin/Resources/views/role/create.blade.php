@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/admin-profile-styles.css')) }}">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <style>
        #Admin {
            margin: 0 !important;
        }
    </style>
@endsection

@section('content')
    @if($errors->has)
        @foreach($errors->all as $error)
            <div>{{$error}}</div>
        @endforeach
    @endif

    <div class="row" style="padding-left: 15px;">
        <form class="w-100"
              action="{{ !empty($role) ? route('admin.roles.update', $role->id) : route('admin.roles.store') }}"
              method="POST">
            {{ admin_csrf_field() }}
            <div class="row">
                <div class="col-lg-3 pl-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="control-label required" for="name">{{__('common.Name')}}</label>
                                <input type="text" name="name" id="name" class="form-control"
                                       value="{{ old('name') ?? (!empty($role) ? $role->name : '')}}">
                            </div>
                            <div class="form-group">
                                <label class="control-label required" for="priority">{{__('common.Priority')}}</label>
                                <input type="text" name="priority" id="priority" class="form-control"
                                       value="{{ old('priority') ?? (!empty($role) ? $role->priority : '')}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 pl-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h3 class="float-left">{{__('common.Permissions')}}
                                    </h3>
                                    <div class="float-right">
                                        @if(!empty($role))
                                            <x-button-bottom-bar
                                                url="{{route('admin.roles.list')}}"
                                                saveEditName="{{ __('common.Update') }}"
                                                cancelName="{{ __('common.Cancel') }}"
                                            />
                                        @else
                                            <x-button-bottom-bar
                                                url="{{route('admin.roles.list')}}"
                                                saveEditName="{{ __('common.Create') }}"
                                                cancelName="{{ __('common.Cancel') }}"
                                            />
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-check pl-1">
                                        <input type="checkbox" id="selectAllControllers" class="">
                                        <label for="selectAllControllers"><strong>Select all</strong></label><br>
                                    </div>
                                </div>
                            </div>

                            @foreach($permissionsByGroups as $moduleName => $columns)
                                <div class="mb-4 allCheckBoxCont">
                                    <div class="col-lg-12">
                                        <div class="form-check-inline">
                                            <input id="select{{$moduleName}}"
                                                   type="checkbox"
                                                   name="roles[]"
                                                   data-module="{{$moduleName}}"
                                                   value=""
                                                   class="roleSelector"
                                            >
                                        </div>
                                        <a class="btn btn-collapse" style="width: 180px;" data-toggle="collapse"
                                           href="#{{$moduleName}}" role="button"
                                           aria-expanded="false"
                                           aria-controls="collapseExample">
                                            {{ $moduleName }} <i class="fa fa-play font-10"></i>
                                        </a>
                                    </div>
                                    <div class="row collapsing " id="{{$moduleName}}">
                                        @foreach($columns as $key => $value)
                                            <div class="col-lg-2 mt-3 mb-3">
                                                <div class="w-100">
                                                    <div class="form-check ">
                                                        <input type="checkbox" id="{{ $moduleName.$key }}"
                                                               data-controller="{{ $moduleName.$key }}"
                                                               data-module="{{$moduleName}}"
                                                               class="selectByController classSelector">
                                                        <label
                                                            for="{{ $moduleName.$key }}"
                                                            class="sibling-text"><strong>{{ str_replace('Controller', '', $key)}}</strong></label>
                                                        <i class="toggle-icon-state fa fa-play font-10 mr-2"
                                                           data-module="{{$moduleName.$key}}"
                                                           aria-hidden="true"></i>
                                                    </div>
                                                    <div id="box{{ $moduleName.$key }}" class="hiden-checkbox">
                                                        @foreach($value as $controller)
                                                            <div class="form-check">
                                                                <input
                                                                    data-controller="{{$moduleName.$key}}"
                                                                    data-module="{{$moduleName}}"
                                                                    type="checkbox"
                                                                    id="{{$moduleName.$controller->id }}"
                                                                    name="permissions[]"
                                                                    class="permission"
                                                                    value="{{$controller->id}}"
                                                                    @if( !empty($role) ? $role->permissions->contains($controller) : false )
                                                                    checked="checked"
                                                                    @endif
                                                                >
                                                                <label class="ml-1" style="display: inline;"
                                                                       for="{{ $moduleName.$controller->id }}">
                                                                    {{ $controller->description }}
                                                                </label>

                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ assets_version(asset('dist/js/basic-roles-and-permission-checkbox.js')) }}"></script>
    <script>
        getCheckController();
    </script>
@endpush
