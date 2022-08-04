@extends('pages.layouts.app')

@section('title',  'How it works - ')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/how-it-works-styles.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/bootstrap.min.css') }}">
@endsection

@section('content')

    <div class="ui vertical segment features-container" id="how-it-works-container">
        <h2 class="ui header center aligned text-black">{{__('static.HowItWorksPageTitle')}}</h2>
        <div class="ui stackable center aligned grid container">
            <p class="nine wide column">{{__('static.HowItWorksPageText')}}</p>
        </div>
    </div>

    <div class="ui vertical segment features-container" id="how-it-works-features">
        <div class="ui three column stackable center aligned grid container">
            <div class="row">
                <div class="column text-left">
                    <h4 class="ui large header">{{__('static.HowItWorksPageFeature1Title')}}</h4>
                    <p>{{__('static.HowItWorksPageFeature1Text')}}</p>
                </div>
                <div class="column text-left">
                    <h4 class="ui large header">{{__('static.HowItWorksPageFeature2Title')}}</h4>
                    <p>{{__('static.HowItWorksPageFeature2Text')}}</p>
                </div>
                <div class="column text-left">
                    <h4 class="ui large header">{{__('static.HowItWorksPageFeature3Title')}}</h4>
                    <p>{{__('static.HowItWorksPageFeature3Text')}}</p>
                </div>
            </div>

        </div>
        <div class="ui vertical segment features-container" id="boost-your-sales">
            <div class="ui two column stackable center aligned grid container">
                <div class="column">
                    <div class="feature-block">
                        <h3 class="ui large header left aligned">{{__('static.HowItWorksPageImage1Title')}}</h3>
                        <p class="ui left aligned">{!! trans('static.HowItWorksPageImage1Text') !!}</p>
                    </div>
                </div>
                <div class="column">
                    <img src="{{ assets_version(url('/') . '/images/how-it-works/rocket.svg') }}" alt="secure-img"
                         class="ui medium rounded image">
                </div>
            </div>
        </div>
        <div class="ui vertical segment features-container" id="your-money-is-safe">
            <div class="ui two column stackable center aligned grid container">
                <div class="column">
                    <img src="{{ assets_version(url('/') . '/images/how-it-works/safe.svg') }}" alt="secure-img"
                         class="ui medium rounded image">
                </div>
                <div class="column">
                    <div class="feature-block">
                        <h3 class="ui large header left aligned">{{__('static.HowItWorksPageImage2Title')}}</h3>
                        <p class="ui left aligned">{!! trans('static.HowItWorksPageImage2Text') !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
