@extends('pages.layouts.app')

@section('title',  'Privacy policy - ')

@section('style')
    @parent
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/bootstrap.min.css') }}">
 @endsection

@section('content')
    {!! $content !!}
@endsection
