@extends('pages.layouts.app')

@section('title',  'About us - ')

@section('style')
    @parent
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/about-us-styles.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/bootstrap.min.css') }}">
@endsection

@section('content')



    <div class="ui stripe segment two column row" id="hero-image-container">
        <div class="ui middle aligned stackable grid container">
            <div class="row">
                <div class="nine wide left floated column">
                    <h1 class="ui huge header left aligned text-black"
                        style="font-size: 3rem">{{__('static.AboutPageBannerTitle')}}</h1>
                    <h3 class="ui huge header left aligned">{{__('static.AboutPageBannerSecondTitle')}}</h3>
                    <p>{!! trans('static.AboutPageBannerText')!!}</p>
                </div>
                <div class="seven wide column right-image-container">
                </div>
            </div>
        </div>
    </div>

    <div class="ui vertical segment features-container" id="about-us">
        <div class="ui three column stackable center aligned grid container">
            <h1 class="ui huge header left aligned text-black">{{__('static.AboutPageTitle')}}</h1>
        </div>
        <div class="ui four column grid container features-container" id="our-team">
            <div class="column">
                <div class="ui fluid card">
                    <div class="image">
                        <img src="{{ assets_version(url('/') . '/images/team/yonko-chuklev.png') }}">
                    </div>
                    <div class="content">
                        <h4 class="header ui small">{{__('static.AboutPageOurTeamPerson1Name')}}
                             <a target="_blank" class="linkedin-icon" href="https://www.linkedin.com/in/yonko-chuklev-a7733568/">
                                <img src="{{ assets_version(url('/') . '/images/icons/linkedin.png') }}" />
                            </a>
                        </h4>
                        <p>{{__('static.AboutPageOurTeamPerson1Value')}}

                        </p>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="ui fluid card">
                    <div class="image">
                        <img src="{{ assets_version(url('/') . '/images/team/ivaylo-yovkov.png') }}">
                    </div>
                    <div class="content">
                        <h4 class="header ui small">{{__('static.AboutPageOurTeamPerson2Name')}}
                             <a target="_blank" class="linkedin-icon" href="https://www.linkedin.com/in/ivayloyovkov/">
                                <img src="{{ assets_version(url('/') . '/images/icons/linkedin.png') }}" />
                            </a>
                        </h4>
                        <p>{{__('static.AboutPageOurTeamPerson2Value')}}

                        </p>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="ui fluid card">
                    <div class="image">
                        <img src="{{ assets_version(url('/') . '/images/team/mariya-miteva.png') }}">
                    </div>
                    <div class="content">
                        <h4 class="header ui small">{{__('static.AboutPageOurTeamPerson3Name')}}</h4>
                        <p>{{__('static.AboutPageOurTeamPerson3Value')}} </p>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="ui fluid card">
                    <div class="image">
                        <img src="{{ assets_version(url('/') . '/images/team/steliyan-stoyanov.png') }}">
                    </div>
                    <div class="content">
                        <h4 class="header ui small">{{__('static.AboutPageOurTeamPerson4Name')}}</h4>
                        <p>{{__('static.AboutPageOurTeamPerson4Value')}}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="ui vertical segment features-container" id="top-tier">
            <h1 class="ui huge header center aligned">{{__('static.AboutPageBackedBy')}}</h1>
        </div>
        <div class="ui two column stackable center aligned grid container" id="stikcredit-originator">
            <div class="seven wide column middle aligned">
                <a href="https://stikcredit.com/" target="_blank"><img
                            src="{{ assets_version(url('/') . '/images/icons/stikcredit-originator-logo.svg') }}" alt="stikcredit-img"
                            class="ui medium rounded image center aligned middle aligned"></a>
            </div>
            <div class="eight wide column">
                <div class="feature-block">
                    <p class="ui text-left aligned">{!! trans('static.AboutPageMoreInformation')!!} <a
                                href="https://stikcredit.com/" target="_blank">stikcredit.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection
