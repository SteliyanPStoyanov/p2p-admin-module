@extends('pages.layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/404_avatar_style.css') }}">
@endsection

@section('content')
    <section class="page_404">
        <div class="ui vertical segment features-container">
            <img class="ui large rounded image image_404" src="{{ assets_version(url('/') . '/images/404.svg') }}"/>
            <br/>
            <h3 class="ui header center aligned text-gray">The page you are looking for does not exist!</h3>
            <br/>
            <a class="ui teal button" href="{{ url('/') }}"> Go back home</a>
        </div>
    </section>
@endsection

