<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title') {{ config('app.name') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;300;400;600;700;800&display=swap"
          rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="{{ assets_version(asset('images/icons/favicon.ico')) }}">
    <link rel="stylesheet" href="{{ assets_version(asset('css/font-awesome.min.css')) }}">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="{{ assets_version(asset('css/bootstrap.min.css')) }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/semantic.min.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/main-semantic-styles.css') }}">
    <link rel="stylesheet" href="{{ assets_version(asset('modules/profile/css/profile-style.css')) }}">
    @livewireStyles
@yield('style')

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>

    <script src="{{ assets_version(asset('js/html5shiv.js')) }}"></script>
    <script src="{{ assets_version(asset('js/respond.min.js')) }}"></script>
    <script src="{{ assets_version(asset('/dist/js/sidebar.min.js')) }}"></script>
    <![endif]-->

@if(env('APP_ENV') == 'prod')
    <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start':
                        new Date().getTime(), event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-PG38JZ6');</script>
        <!-- End Google Tag Manager -->
    @endif

</head>
<body>
@if(env('APP_ENV') == 'prod')
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PG38JZ6"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
@endif

<header class="mb-3">
    <div class="main-menu-bootstrap">
        <div class="w-100 container top-header">
            <a href="/profile/overview" class="logo-home column">
                <img class="img-responsive logo-svg" id="logo"
                     src="{{ assets_version(asset('images/icons/logo.svg')) }}" alt="home"/>
            </a>
            <ul class="nav justify-content-end col-lg-10 float-right h-100 align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile.profile.referral') }}">
                        {{ __('common.EarnBonuses') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('profile.dashboard.overview')}}">{{ __('common.Uninvested') }}
                        @if(get_device()->isMobile() == false)
                            <livewire:wallet-uninvested/>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{route('profile.logout')}}" class="nav-link pr-0" onclick="event.preventDefault();
                                document.getElementById('logout-form-2').submit();">
                        {{ __('common.Logout') }}
                    </a>
                    <form id="logout-form-2" action="{{ route('profile.logout') }}" method="POST"
                          style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
        <div class="container-fluid navbar-bg">
            <nav class="navbar navbar-expand-lg navbar-light bg-light container p-0">
                <div class="navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a href="{{route('profile.dashboard.overview')}}"
                           class="nav-link
                        {{ (request()->is('profile/overview*')) ? 'active' : '' }}">
                            {{ __('common.Overview') }}
                        </a>
                        <a href="{{route('profile.invest')}}" class="nav-link
                        {{ (request()->is('profile/invest*')) ? 'active' : '' }}">
                            {{ __('common.Invest') }}
                        </a>
                        <a href="{{route('profile.autoInvest')}}" class="nav-link
                            {{ (request()->is('auto-invest*')) ? 'active' : '' }}
                            ">
                            {{ __('common.AutoInvest') }}
                        </a>
                        <a href="{{route('profile.myInvest')}}" class="nav-link
                        {{ (request()->is('profile/my-invest*')) ? 'active' : '' }}">
                            {{ __('common.MyInvestments') }}
                        </a>
                        <a href="{{route('profile.profile.accountStatement')}}" class="nav-link
                        {{ (request()->is('profile/account*')) ? 'active' : '' }}">
                            {{ __('common.AccountStatement') }}
                        </a>
                        <a href="{{route('profile.deposit')}}" class="nav-link
                        {{ (request()->is('profile/deposit*')) ? 'active' : '' }}
                        {{ (request()->is('profile/withdraw*')) ? 'active' : '' }}">
                            {{ __('common.Deposit/Withdraw') }}
                        </a>
                        <a href="{{route('profile.profile.index')}}" class="nav-link
                        {{ (request()->is('profile/my-profile*')) ? 'active' : '' }}
                        {{ (request()->is('profile/verify*')) ? 'active' : '' }}">
                            {{ __('common.Profile') }}
                        </a>
                    </div>
                </div>

                @if(!request()->is('profile/cart-secondary/cart'))
                    @php
                        $investorId = \Auth::guard('investor')->user()->investor_id;
                    @endphp
                    <livewire:investor-cart :investorId="$investorId"/>
                @endif

            </nav>
        </div>
    </div>
</header>
<!-- Sidebar Menu -->
<div class="ui vertical sidebar menu">
    <a href="/profile/overview" class="logo-home logo-svg column logo-sidebar">
        <img alt="logo" class="logo-svg-sidebar w-100" style="max-width: 118px"
             src="{{ assets_version(url('/') . '/images/icons/logo.svg') }}"/>
    </a>
    <a class="item" href="{{route('profile.dashboard.overview')}}">{{ __('common.Overview') }}</a>
    <a class="item" href="{{route('profile.invest')}}">{{ __('common.Invest') }}</a>
    <a class="item" href="{{route('profile.autoInvest')}}">{{ __('common.AutoInvest') }}</a>
    <a class="item" href="{{route('profile.myInvest')}}">{{ __('common.MyInvestments') }}</a>
    <a class="item no-box" href="{{route('profile.profile.accountStatement')}}">{{ __('common.AccountStatement') }}</a>
    <a class="item" href="{{route('profile.deposit')}}">{{ __('common.Deposit/Withdraw') }}</a>
    <a class="item" href="{{route('profile.profile.index')}}">{{ __('common.ProfileSetting') }}</a>
    <a class="item sidemenu-bonus" href="{{ route('profile.profile.referral') }}">{{ __('common.EarnBonuses') }}</a>
    <a href="{{route('profile.logout')}}" class="item sidemenu-logout" onclick="event.preventDefault();
                                document.getElementById('logout-form-3').submit();">
        {{ __('common.Logout') }}
    </a>
    <form id="logout-form-3" action="{{ route('profile.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>

<div class="pusher">
    <div class="ui vertical masthead center aligned segment" id="header-container">
        <div class="ui container" id="top-header">
            <div class="ui large secondary pointing menu img-no-border two column row">
                <a href="/profile/overview" class="logo-home column">
                    <img alt="logo" class="logo-svg" src="{{ assets_version(url('/') . '/images/icons/logo.svg') }}">
                </a>
                {{-- for mobile --}}
                <div class="right item logo-home column">
                    <a href="{{route('profile.dashboard.overview')}}">
                        @if(get_device()->isMobile() == true)
                            <livewire:wallet-uninvested/>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <div class="ui container center aligned column" id="nav-container">
            <div class="center aligned ui large secondary pointing menu secondary-menu-container">
                <a class="toc item">
                    <i class="sidebar icon"></i>
                </a>
            </div>
        </div>
    </div>
    @if(\Request::route()->getName() == 'profile.profile.referral')
        <div class="container-fluid px-0">
            <div class="row w-100 justify-content-center">
                <div id="errorHandlerAjax" class="col-6">
                </div>
            </div>
            @yield('content')
        </div>
    @else
        <div class="container content-container">
            <div class="row w-100 justify-content-center">
                <div id="errorHandlerAjax" class="col-6">
                </div>
            </div>
            @yield('content')
        </div>
    @endif
    <div class="ui inverted vertical footer segment" id="footer-inverted">
        <div class="ui container">
            <div class="ui stackable inverted equal height stackable grid">
                <div class="four wide column">
                    <a href="/profile/overview" class="logo-home column logo-svg">
                        <img alt="white-logo" src="{{ assets_version(url('/') . '/images/icons/logo-white-xs.png') }}"/>
                    </a>
                    <div class="ui inverted link list">
                        <a href="/profile/help" class="item"><i class="question circle icon"></i> Help</a>
                        <p class="company-info">Stik Credit JSC<br>Reg. No. 202557159<br>13 B Oborishte Sq.
                            <br>9700 Shumen, Bulgaria</p>
                    </div>
                </div>
                <div class="three wide column">
                    <h4 class="ui inverted header">{{ __('static.MenuHelp') }}</h4>
                    <div class="ui inverted help-links link list">
                        <a href="{{ url('/') }}/profile/help" class="item">{{ __('static.MenuGettingStarted') }}</a>
                        <a href="{{ url('/') }}/profile/help#Investing"
                           class="item">{{ __('static.HelpPageTabInvesting') }}</a>
                        <a href="{{ url('/') }}/profile/help#AboutLoans"
                           class="item">{{ __('static.HelpPageTabAboutLoans') }}</a>
                        <a href="{{ url('/') }}/profile/help#DepositWithdrawals"
                           class="item">{{ __('static.HelpPageTabDepositWithdrawals') }}</a>
                        <a href="tel:+359-889-405-572" class="item"><i class="phone icon"></i> +359-889-405-572</a>
                        <a href="mailto:support@afranga.com" class="item"><i class="mail icon"></i>
                            support@afranga.com</a>
                    </div>
                </div>
                <div class="three wide column footer-right-column">
                    <h4 class="ui inverted header">{{ __('static.MenuCompany') }}</h4>
                    <div class="ui inverted link list">
                        <a href="/about-us" class="item">{{ __('static.MenuAboutUs') }}</a>
                        {{--                        <a href="/blog" class="item">{{ __('static.MenuBlog') }}</a>--}}
                        <a href="/profile/my-profile-referral" class="item">{{ __('static.MenuAffiliate') }}</a>
                        <a href="/user-agreement" class="item">{{ __('static.MenuUserAgreement') }}</a>
                        <a href="/privacy-policy" class="item">{{ __('static.MenuPrivacyPolicy') }}</a>
                        <a href="{{route('referAFriend')}}" class="item">{{ __('static.MenuReferAFriendTC') }}</a>
                    </div>
                </div>
                <div class="three wide column">
                    <h4 class="ui inverted header">{{ __('static.MenuInvest') }}</h4>
                    <div class="ui inverted link list">
                        <a href="/how-it-works" class="item">{{ __('static.MenuHowItWorks') }}</a>
                        <a href="/loan-originators" class="item">{{ __('static.MenuLoanOriginators') }}</a>
                        <a href="{{ url('/') }}/docs/pdf/Fees.pdf" target="_blank"
                           class="item">{{ __('static.MenuFees') }}</a>
                    </div>
                </div>
                <div class="three wide column footer-right-column">
                    <h4 class="ui inverted header">{{ __('static.MenuFollowUs') }}</h4>
                    <div class="ui inverted link list">
                        <a href="https://www.facebook.com/afranga/" class="item" target="_blank" rel="noreferrer">Facebook</a>
                        <a href="https://twitter.com/afranga1" class="item" target="_blank" rel="noreferrer">Twitter</a>
                        <a href="https://www.linkedin.com/company/afranga/" class="item" target="_blank"
                           rel="noreferrer">Linkedin</a>
                    </div>
                </div>

            </div>
            <div class="ui vertical segment features-container">
                <div class="ui three column stackable center aligned grid container">
                    <p class="copyright-text"><a href="/" class="footer-anchor"> &#169; 2021
                            Afranga</a> {{ __('static.CopyrightTextInfo') }} <a href="/cookie-policy"
                                                                                class="footer-anchor">{{ __('static.CopyrightCookiePolicy') }}</a>.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip({container: '.pusher'});
    });
    $(document)
        .ready(function () {

            // create sidebar and attach to menu open
            $('.ui.sidebar')
                .sidebar('attach events', '.toc.item')
            ;
            $('.ui.accordion')
                .accordion()
            ;
            $('.menu .item')
                .tab()
            ;
            $('.help-links .item').click(function () {
                setTimeout(function () {
                    location.reload();
                    $('html,body').scrollTop(200);
                }, 400);
            });

        })
    ;

    $(window).scroll(function () {    // this will work when your window scrolled.
        const height = $(window).scrollTop();  //getting the scrolling height of window
        let is_desktop = false;

        if ($('.secondary.pointing.menu .toc.item').css('display') == 'none') {
            is_desktop = true;
        }

        if (is_desktop == true) {
            if (height > 1 && innerWidth > 767) {
                $(".main-menu-bootstrap").addClass("position-fixed w-100 fixed-top bg-white");
                $(".content-container").css("margin-top", 117)
                $('[data-toggle="tooltip"]').tooltip({container: '.pusher', placement: 'bottom'});
            } else {
                $(".main-menu-bootstrap").removeClass("position-fixed w-100 fixed-top bg-white");
                $(".content-container").css("margin-top", 0)
            }
        }
    });
</script>

@stack('scripts')
@livewireScripts
<script>
    let refRoute = '';
    @if(request()->is('profile/invest*'))
        refRoute = '{{ route('profile.invest.refresh') }}';
    @endIf
        @if(request()->is('profile/auto-invest*'))
        refRoute = '{{ route('profile.autoInvest.refresh') }}';
    @endIf

    window.setTimeout(function () {
        liveWire();
    }, 1000);

    function liveWire() {
        Livewire.emit('postAdded');
    }
    function liveWireLoanReload() {
        Livewire.emit('loanAdd');
    }
    window.addEventListener('invest-status', event => {
        window.setTimeout(function () {
            liveWire();
            if (refRoute) {
                if (window.localStorage.getItem('market') !== 'secondaryMarket') {
                    loadSimpleDataGrid(refRoute, $("#investForm"), $("#table-invests"), false, 0, false, true);
                }
            }
        }, event.detail.waitTime);
    })
</script>
</body>
</html>
