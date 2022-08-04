@extends('pages.layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ url('/') }}/css/404_avatar_style.css">
@endsection

@section('content')
    <section class="page_404">
        <div class="ui vertical segment features-container">
            <br/>
            <h3 class="ui header center aligned text-gray">Sorry, your request is too big. Please use smaller files.</h3>
            <br/>
            <a class="ui teal button" href="{{ url()->previous() }}"> Go back</a>
        </div>
    </section>
@endsection

