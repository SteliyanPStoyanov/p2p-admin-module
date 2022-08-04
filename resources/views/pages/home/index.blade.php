@extends('pages.layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/homepage-styles.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/bootstrap.min.css') }}">
@endsection

@section('content')

    <div class="ui stripe segment two column row" id="hero-image-container">
        <div class="ui middle aligned stackable grid container">
            <div class="row">
                <div class="nine wide left floated column">
                    <img src="{{ assets_version(url('/') . '/images/homepage/hero-image.png') }}" alt="woman-hero-image"
                         class="ui huge rounded image">
                </div>
                <div class="seven wide column hero-text-container">
                    <h2 class="ui huge header">{{__('static.HomePageHeroTitle')}}</h2>
                    <h1 class="ui medium header">{{__('static.HomePageHeroSubTitle')}}</h1>
                    <form method="POST" action="{{ route('profile.register') }}" autocomplete="off"
                          id="topFormSubmission">
                        @csrf
                        @if (session('fail'))
                            <div>
                                <div class="p-1 mb-1 bg-danger-error">{{session('fail')}}</div>
                            </div>
                        @endif
                        <div class="ui action left input">
                            <input type="text" placeholder="{{__('static.HomePageHeroEmailPlaceholder')}}" name="email"
                                   required>
                            <input type="submit" value="{{__('static.MenuCreateAccount')}}"
                                   class="ui teal button">
                        </div>
                    </form>
                    <p>{{__('static.HomePageHeroText')}}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="ui vertical segment features-container" id="why-us-container">
        <h3 class="ui huge header center aligned">{{__('static.HomePageWhyUsTitle')}}</h3>
        <div class="ui three column stackable center aligned grid container">
            <div class="column">
                <div class="feature-block">
                    <img src="{{ assets_version(url('/') . '/images/icons/high-returns.svg') }}" alt="high-returns-img"
                         class="ui tiny rounded image">
                    <h4 class="ui header">{{__('static.HomePageWhyUsFeature1')}}</h4>
                </div>
                <p>{{__('static.HomePageWhyUsFeature1Text')}}</p>
            </div>
            <div class="column">
                <div class="feature-block">
                    <img src="{{ assets_version(url('/') . '/images/icons/secure.svg') }}" alt="secure-img" class="ui tiny rounded image">
                    <h4 class="ui header">{{__('static.HomePageWhyUsFeature2')}}</h4>
                </div>
                <p>{{__('static.HomePageWhyUsFeature2Text')}}

                 <a data-toggle="tooltip" data-placement="top"
                     data-original-title="{{__('common.AllLoansListedOnAfranga')}}"
                    style="cursor: pointer; text-decoration: underline;">
                     {{__('common.WantToKnowMore')}}
                </a>
                </p>

            </div>
            <div class="column">
                <div class="feature-block">
                    <img src="{{ assets_version(url('/') . '/images/icons/shared-risk.svg') }}" alt="shared-risk-img"
                         class="ui tiny rounded image">
                    <h4 class="ui header">{{__('static.HomePageWhyUsFeature3')}}</h4>
                </div>
                <p>{{__('static.HomePageWhyUsFeature3Text')}}</p>
            </div>
        </div>
    </div>

    <div class="ui vertical segment features-container" id="no-fees-container">
        <h3 class="ui huge header center aligned">{{__('static.HomePageNoFeesTitle')}}</h3>
        <div class="ui three column stackable center aligned grid container">
            <p>{{__('static.HomePageNoFeesText')}}</p>
        </div>
    </div>
    <div class="ui vertical segment features-container available-loans-container">
        <h3 class="ui huge header center aligned">{{__('static.HomePageAvailableLoansTitle')}}</h3>
        <div id="tableSt" style="text-align: center">
            @include('pages.home.list-table')
        </div>
        <a class="ui gray basic small button" href="/invest"
           id="see-all-btn">{{__('static.HomePageAvailableLoansTableSeeAllButton')}}</a>
    </div>
@endsection
@push('scripts')
    <script nonce="homePage" src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>
    <script nonce="homePage">
        $('#topFormSubmission').submit(function () {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                'event': 'topFormSubmission'
            });
        })
          $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

    </script>
@endpush
