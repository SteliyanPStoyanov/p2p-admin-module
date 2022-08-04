@extends('pages.layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/404_avatar_style.css') }}">
@endsection

@section('content')
    <section class="page_404">
        <div class="ui vertical segment features-container">
            <br/>
            <h3 class="ui header center aligned text-gray">Sorry, session expired. Please refresh an try again</h3>
            <br/>
            <a class="ui teal button" href="{{ url('/') }}"> Go back home</a>
        </div>
    </section>
@endsection

