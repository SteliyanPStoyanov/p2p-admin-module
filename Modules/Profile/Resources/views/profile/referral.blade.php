@extends('profile::layouts.app')

@section('title',  'Refer a friend - ')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('modules/profile/css/referral-style.css')) }}">
@endsection

@section('content')
    <div class="pusher">
        <div class="ui stripe segment two column row" id="refer-a-friend-image-container">
            <div class="ui middle aligned stackable grid container">
                <div class="row">
                    <div class="seven wide left floated column bottom aligned hide-on-tablet" style="margin-right: 3rem; margin-top: 3rem">
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
                <div class="six wide column middle aligned" id="how-img-container">
                    <div class="col-lg-12" style="margin: auto; ">
                        <div class="jumbotron">
                            <h4>{{ __('common.YourReferralLink') }} </h4>
                            <input class="form-control referral mb-3 ref-link" maxlength="30" name="referralHash"
                                   type="text" value="{{route('profile.hash',$profileHashLink->referral_hash)}}"
                                   id="referralHash" readonly>
                            <a class="ui teal button" href="#" role="button" id="copyReferral" onClick="return false;"
                               data-toggle="tooltip" data-placement="bottom"
                               data-original-title="Copied">
                                {{ __('common.CopyLink') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-12" style="margin: auto;">
                        @if (session('fail'))
                            <div class="w-100">
                                <div
                                        class="p-1 mb-1 bg-danger text-white w-100 rounded-lg">{{session('fail')}}</div>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="w-100">
                                <div class="p-1 mb-1 bg-success text-white">{{session('success')}}</div>
                            </div>
                        @endif

                    </div>
                    <div class=" col-lg-12" style="margin: auto;">
                        <div class="jumbotron">
                            <h4> {{ __('common.InviteViaEmail') }}</h4>
                            <form method="POST" class="row" action="{{ route('profile.profile.sendReferralLink') }}"
                                  autocomplete="off">
                                @csrf
                                <input class="form-control" required="required" minlength="2" maxlength="30"
                                       name="email"
                                       type="email" value="" id="email"
                                       placeholder="{{ __('common.EnterEmailAddress') }}">
                                <input class="form-control" required="required" minlength="2" maxlength="30"
                                       name="referral_id"
                                       value="{{$investor->investor_id}}" style="display: none;">
                                <button class="ui teal button mt-3"
                                        role="button"
                                        href="#how-does-it-work-container">{{ __('common.SendEmail') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui vertical segment features-container mbottom-10" id="want-to-know-more">
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
    </div>
    @push('scripts')
        <script>
            jQuery(document).ready(function ($) {
                $(".scroll").click(function (event) {
                    event.preventDefault();
                    $('html,body').animate({scrollTop: $(this.hash).offset().top}, 500);
                });
            });
        </script>
    @endpush


@endsection

@push('scripts')
    <script>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip('dispose');
        });
        $(document).ready(function () {
            $('#copyReferral').click(function (e) {
                e.preventDefault()
                $(this).siblings('input.referral').select();
                document.execCommand("copy");
                $(this).parent().find('[data-toggle="tooltip"]').tooltip('show');
                window.setTimeout(function () {
                    $('[data-toggle="tooltip"]').tooltip('dispose');
                }, 1000);
            })
        });
    </script>
@endpush
