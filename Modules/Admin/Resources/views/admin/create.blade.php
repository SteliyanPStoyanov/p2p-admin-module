@extends('layouts.app')

@section('style')
    <link rel="stylesheet"
          href="{{ assets_version(asset('css/admin-profile-styles.css')) }}">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

@endsection

@section('content')
    @if($errors->has)
        @foreach($errors->all as $error)
            <div>{{$error}}</div>
        @endforeach
    @endif
    <div class="row" style="padding-left: 15px;">
        <form method="POST"
              action="{{ route('admin.administrators.store') }}"
              accept-charset="UTF-8" class="col-12" enctype='multipart/form-data'>
            {{ admin_csrf_field() }}
            <div class="row">
                <div class="col-lg-3 pl-0">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="first_name" class="control-label required">{{ __('common.Name') }}</label>
                                <input class="form-control" required="required" minlength="2" maxlength="30"
                                       name="first_name" type="text"
                                       id="first_name">
                            </div>
                            <div class="form-group">
                                <label for="middle_name"
                                       class="control-label required">{{ __('common.MiddleName') }}</label>
                                <input class="form-control" required="required" minlength="2" maxlength="30"
                                       name="middle_name" type="text"
                                       id="middle_name">
                            </div>
                            <div class="form-group">
                                <label for="last_name" class="control-label required">{{ __('common.LastName') }}</label>
                                <input class="form-control" required="required" minlength="2" maxlength="30"
                                       name="last_name" type="text"
                                       id="last_name">
                            </div>
                            <div class="form-group">
                                <label for="phone" class="control-label required">{{ __('common.Phone') }}</label>
                                <input class="form-control" required="required" minlength="10" maxlength="10"
                                       name="phone" type="text"
                                       id="phone">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="email" class="control-label required">{{ __('common.Email') }}</label>
                                <input class="form-control" required="required" minlength="2" maxlength="30"
                                       name="email" type="text"
                                       id="email">
                            </div>
                            <div class="form-group">
                                <label for="username"
                                       class="control-label required">{{ __('common.AdminUserName') }}</label>
                                <input class="form-control" required="required" minlength="2" maxlength="30"
                                       name="username" type="text"
                                       id="username">
                            </div>
                            <div class="form-group">
                                <label for="password"
                                       class="control-label required">{{ __('common.Password') }}</label>
                                <input class="form-control" required="required" minlength="2" maxlength="30"
                                       name="password" type="password"
                                       id="password">
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation"
                                       class="control-label required">{{ __('common.AdminPwdConfirm') }}</label>
                                <input class="form-control" required="required" minlength="2" maxlength="30"
                                       name="password_confirmation" type="password"
                                       id="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>
                @if($me->canChangeRoles())
                    <div class="card w-100">
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <h4 class="card-title float-left">{{__('common.Permissions')}} </h4>
                                    </div>
                                </div>
                                @foreach($roles as $k=>$role)
                                    @php
                                        $module = str_replace(' ', '', $role->name);
                                    @endphp
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <div class="form-check-inline">
                                                <input id="select{{$module}}"
                                                       type="radio"
                                                       name="roles[]"
                                                       data-module="{{$module}}"
                                                       value="{{$role->id}}"
                                                       class="roleSelector"
                                                >
                                            </div>
                                            <a class="btn btn-collapse" data-toggle="collapse"
                                               href="#{{$module}}"
                                               role="button" aria-expanded="false"
                                               aria-controls="collapseExample">
                                                {{ $role->name }} <i class="fa fa-play font-10"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row collapsing" id="{{$module}}">
                                        @foreach($groups[$role->name] as $key=>$permissions)
                                            <div class="mt-5 col-lg-2">
                                                <div class="w-100">
                                                    <div class="form-check">
                                                        <input type="checkbox" id="{{$module.$key }}"
                                                               data-controller="{{ $module.$key }}"
                                                               data-module="{{$module}}"
                                                               class="selectByController classSelector">
                                                        <label
                                                            for="{{$module.$key }}"
                                                            class="sibling-text"><strong>{{ str_replace('Controller', '', $key)}}</strong>

                                                        </label>
                                                        <i class="toggle-icon-state fa fa-play font-10 mr-2"
                                                           data-module="{{$module.$key}}"
                                                           aria-hidden="true"></i>
                                                    </div>
                                                    <div id="box{{ $module.$key }}"
                                                         class="hiden-checkbox">
                                                        @foreach($permissions as $permission)
                                                            <div class="form-check">
                                                                <input
                                                                    data-controller="{{$module.$key}}"
                                                                    data-module="{{$module}}"
                                                                    type="checkbox"
                                                                    id="{{ $role->name . $permission->id }}"
                                                                    name="permissions[]"
                                                                    class="permission"
                                                                    value="{{$permission->id}}"
                                                                >
                                                                <label class="ml-1" style="display: inline;"
                                                                       for="{{ $role->name . $permission->id }}">
                                                                    {{ $permission->description }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <hr class="my-4">
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <x-button-bottom-bar
                url="{{route('admin.administrators.list')}}"
                saveEditName="{{ __('common.Create') }}"
                cancelName="{{ __('common.Cancel') }}"
            />
        </form>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ assets_version(asset('dist/js/role-permission-checkbox.js')) }}"></script>
@endpush
