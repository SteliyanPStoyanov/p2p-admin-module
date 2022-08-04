@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    {{--Main Table--}}
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="cronTable">
                            @include('admin::crons.list-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--End Main table--}}
@endsection
