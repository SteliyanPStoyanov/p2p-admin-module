@extends('pages.layouts.app')

@section('title',  'Refer a friend - ')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/affiliate-styles.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/bootstrap.min.css') }}">
@endsection

@section('content')

    <div class="ui stripe segment two column row" id="refer-a-friend-image-container">
        <div class="ui middle aligned stackable grid container">
            <div class="row">
                <div class="seven wide left floated column bottom aligned" style="margin-right: 3rem; margin-top: 3rem">
                    <img src="{{ assets_version(url('/') . '/images/homepage/refer-a-friend-affiliate-img.jpg') }}" class="ui huge rounded image">
                </div>
                <div class="six wide left floated column top aligned refer-a-friend-text-container" style="margin-top: -5rem">
                    <h2 class="ui huge header text-black">{{__('static.AffiliatePageTitle')}}</h2>
                    <h3 class="ui huge header text-green">{{__('static.AffiliatePageSubTitle')}}</h3>
                    <p>{{__('static.AffiliatePageText1')}}
                        <strong>€500</strong>{{__('static.AffiliatePageText2')}}
                    </p>
                    <a class="ui teal large button scroll"
                       href="#how-does-it-work-container">{{__('static.AffiliatePageButtonCTA')}}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="ui vertical segment features-container" id="how-does-it-work-container">
        <div class="ui two column stackable grid container">
            <div class="eight wide column" id="how-info-container">
                <h1 class="ui huge header">{{__('static.AffiliatePageHowItWorksTitle')}}</h1>
                <div class="feature-block-container">
                    <div class="feature-block step">
                        <p class="ui yellow circular label left aligned floated">1</p>
                        <h3 class="ui header floated text-dark-gray">{{__('static.AffiliatePageHowItWorks1Title')}}</h3>
                    </div>
                    <p>
                        {{__('static.AffiliatePageHowItWorks1Text')}}
                    </p>
                </div>
                <div class="feature-block-container">
                    <div class="feature-block step">
                        <p class="ui yellow circular label left aligned floated">2</p>
                        <h3 class="ui header floated text-dark-gray">{{__('static.AffiliatePageHowItWorks2Title')}}</h3>
                    </div>
                    <p>
                        {{__('static.AffiliatePageHowItWorks2Text')}}
                    </p>
                </div>
                <div class="feature-block-container">
                    <div class="feature-block step">
                        <p class="ui yellow circular label left aligned floated">3</p>
                        <h3 class="ui header floated text-dark-gray">{{__('static.AffiliatePageHowItWorks3Title')}}</h3>
                    </div>
                    <p>
                        {{__('static.AffiliatePageHowItWorks3Text')}}
                    </p>
                </div>
            </div>
            <div class="five wide column" id="how-img-container">
                <div class="feature-block">
                    <img src="{{ assets_version(url('/') . '/images/icons/watering-plant.svg') }}" class="ui small rounded image centered" id="watering-can-img">
                </div>
                <p class="text-green">{{__('static.AffiliatePageHowItWorksImageText')}}</p>
            </div>
        </div>
    </div>

    <div class="ui vertical segment features-container" id="want-to-know-more">
        <div class="ui three column stackable left aligned grid container">
            <h1 class="ui huge header center aligned">{{__('static.AffiliatePageKnowMoreTitle')}}</h1>
            <div class="ui styled accordion">
                <div class="title active">
                    <i class="dropdown icon"></i>
                    {{__('static.AffiliatePageKnowMore1Title')}}
                </div>
                <div class="content active">
                    <p>{!! trans('static.AffiliatePageKnowMore1Text')!!}</p>
                </div>
                <div class="title">
                    <i class="dropdown icon"></i>
                    {{__('static.AffiliatePageKnowMore2Title')}}
                </div>
                <div class="content">
                    <p>{{__('static.AffiliatePageKnowMore2Text')}}</p>
                </div>
                <div class="title">
                    <i class="dropdown icon"></i>
                    {{__('static.AffiliatePageKnowMore3Title')}}
                </div>
                <div class="content">
                    <p>{{__('static.AffiliatePageKnowMore3Text')}}</p>
                </div>
                <div class="title">
                    <i class="dropdown icon"></i>
                    {{__('static.AffiliatePageKnowMore4Title')}}
                </div>
                <div class="content">
                    <p>{{__('static.AffiliatePageKnowMore4Text')}}</p>
                </div>
                <div class="title">
                    <i class="dropdown icon"></i>
                    {{__('static.AffiliatePageKnowMore5Title')}}
                </div>
                <div class="content">
                    <p>{{__('static.AffiliatePageKnowMore5Text')}}</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script nonce="affiliatePage">
            jQuery(document).ready(function ($) {
                $(".scroll").click(function (event) {
                    event.preventDefault();
                    $('html,body').animate({scrollTop: $(this.hash).offset().top}, 500);
                });
            });
        </script>
    @endpush

@endsection
