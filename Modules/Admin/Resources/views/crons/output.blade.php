@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <h3><strong>{{ __('common.Command') }}</strong>: {{ $command->getNameForDb() }}</h3>
                    <p><strong>OUTPUT:</strong></p>
                    <textarea cols="30" rows="10" disabled> {{ $output }}</textarea>
                    <br>
                    <br>
                    <a href="{{ route('admin.crons.list') }}" class="btn btn-success">{{__('common.GoBack')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
