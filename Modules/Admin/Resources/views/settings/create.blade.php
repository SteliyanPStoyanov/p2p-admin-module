@extends('layouts.app')

@section('content')
    @if($errors->has)
        @foreach($errors->all as $error)
            <div>{{$error}}</div>
        @endforeach
    @endif
    <div class="row" style="padding-left: 15px;">

        <form class="w-100"
              action="{{ !empty($setting) ? route('admin.settings.update', $setting->setting_key) : route('admin.settings.store') }}"
              method="POST">
            {{ admin_csrf_field() }}
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="control-label required" for="name">{{__('common.Name')}}</label>
                                <input type="text" name="name" id="name" class="form-control"
                                       value="{{!empty($setting) ? $setting->name : ''}}">
                            </div>
                            <div class="form-group">
                                <label class="control-label required"
                                       for="description">{{__('common.Description')}}</label>
                                <input type="text" name="description" id="description" class="form-control"
                                       value="{{ old('description') ?? (!empty($setting) ? $setting->description : '')}}">
                            </div>
                            <div class="form-group">
                                <label class="control-label required"
                                       for="default_value">{{__('common.DefaultValue')}}</label>
                                <input type="text" name="default_value" id="default_value" class="form-control"
                                       value="{{old('default_value') ?? (!empty($setting) ? $setting->default_value : '')}}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 btns-form-panel">
                    @if(!empty($setting))
                        <x-button-bottom-bar
                            url="{{route('admin.settings.list')}}"
                            saveEditName="{{ __('common.Update') }}"
                            cancelName="{{ __('common.Cancel') }}"
                        />
                    @else
                        <x-button-bottom-bar
                            url="{{route('admin.settings.list')}}"
                            saveEditName="{{ __('common.Create') }}"
                            cancelName="{{ __('common.Cancel') }}"
                        />
                    @endif
                </div>
            </div>
        </form>

    </div>
@endsection
