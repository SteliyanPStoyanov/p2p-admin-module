<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Standard Meta -->
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="Content-Security-Policy" content="
    script-src 'self' *.googletagmanager.com *.hotjar.com  *.facebook.net snap.licdn.com https://www.google-analytics.com https://ssl.google-analytics.com https://www.googleadservices.com https://googleads.g.doubleclick.net https://www.google.com https://tagmanager.google.com
    'nonce-homePage' 'nonce-helpPage' 'nonce-headPage' 'nonce-investPage' 'nonce-affiliatePage';
    style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://tagmanager.google.com https://fonts.googleapis.com https://fonts.googleapis.com;
    font-src 'self' https://fonts.gstatic.com 'unsafe-inline' data:;
    object-src 'none';
    block-all-mixed-content;
    base-uri 'self';
    img-src: https://www.google-analytics.com https://ssl.gstatic.com https://www.gstatic.com;
    connect-src: https://www.google-analytics.com;
">
    <meta http-equiv="content-language" content="en">
    <base href="{{ url('/') }}"/>
    <meta name="description"
          content="{{ __('static.SiteDescription') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="{{ assets_version(url('/') . '/images/icons/fav-img.png') }}">
    <link rel="icon" type="image/png" href="{{ assets_version(url('/') . '/images/icons/fav-img.png') }}">
    <link rel="shortcut icon" href="{{ assets_version(url('/') . '/images/icons/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{assets_version(asset('css/font-awesome.min.css'))}}">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;300;400;600;700;800&display=swap"
          rel="stylesheet">
    <script src="{{ assets_version(url('/') . '/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ assets_version(url('/') . '/css/sidebar.min.css')  }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/semantic.min.css') }}">

    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/main-semantic-styles.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/layout-styles.css') }}">
    @yield('style')

    <script src="{{ assets_version(url('/') . '/dist/js/semantic.min.js') }}"></script>
    <script src="{{ assets_version(url('/') . '/dist/js/sidebar.min.js') }}"></script>

    <script nonce="headPage" type="application/ld+json"> {
                "@context": "http://schema.org",
                "@type": "FinancialService",
                "name": "Afranga",
                "description": "A safe marketplace for investing in loans. Generate passive income and earn up to 18% per annum on your savings. ",
                "logo": "https://afranga.com/images/icons/logo.svg",
                "telephone": "tel:+359-889-405-572",
                "email": "mailto:support@afranga.com",
                "url": "https://afranga.com/",
                "sameAs": [
                    "https://twitter.com/afranga1",
                    "https://www.facebook.com/afranga",
                    "https://www.linkedin.com/company/69504404/"
                ],
                "image": "https://afranga.com/images/homepage/hero-image.png",
                "priceRange": "$-$$$",
                "address": [
                    {
                        "@type": "PostalAddress",
                        "streetAddress": "13 B Oborishte Sq.",
                        "addressLocality": "Shumen",
                        "postalCode": "9700",
                        "addressCountry": "Bulgaria"
                    }
                ]
            } </script>

    <!-- Site Properties -->
    <title>@yield('title')afranga | {{ __('static.SiteTitle') }}</title>

    <script nonce="headPage">
        $(window).scroll(function () {    // this will work when your window scrolled.
            const height = $(window).scrollTop();  //getting the scrolling height of window
            if (height > 100) {
                $(".masthead").addClass('hidden');
                $(".main-fixed").removeClass("hidden");
            } else {
                $(".main-fixed").addClass("hidden");
                $(".masthead").removeClass("hidden");
            }
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
            });
        $('#bottomFormSubmission').submit(function () {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                'event': 'bottomFormSubmission'
            });
        })
    </script>
    @if(env('APP_ENV') == 'prod')
    <!-- Google Tag Manager -->
        <script nonce="headPage">(function (w, d, s, l, i) {
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
    <noscript nonce="headPage">
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PG38JZ6"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
@endif

<!-- Sticky Menu -->
<div class="ui large top fixed hidden main-fixed menu">
    <div class="ui container">
        <a href="/" class="logo-home logo-svg column">
            <img alt="logo" src="{{ assets_version(url('/') . '/images/icons/logo.svg') }}"/>
        </a>
        <div id="fixed-main-menu">
            <a class="item" href="/how-it-works">{{ __('static.MenuHowItWorks') }}</a>
            <a class="item" href="/invest">{{ __('static.MenuInvest') }}</a>
            <a class="item" href="/loan-originators">{{ __('static.MenuLoanOriginators') }}</a>
            <a class="item" href="/help">{{ __('static.MenuHelp') }}</a>
        </div>
        <div class="right menu">
            <div class="item">
                <a class="ui button basic no-box" href="/login">{{ __('static.MenuLogin') }}</a>
            </div>
            <div class="item">
                <a class="ui teal button" href="/register">{{ __('static.MenuCreateAccount') }}</a>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar Menu -->
<div class="ui vertical sidebar menu">
    <a href="/" class="logo-home logo-svg column logo-sidebar">
        <img alt="logo" class="logo-svg-sidebar logo-svg" src="{{ assets_version(url('/') . '/images/icons/logo.svg') }}"/>
    </a>
    <a class="item" href="/how-it-works">{{ __('static.MenuHowItWorks') }}</a>
    <a class="item" href="/invest">{{ __('static.MenuInvest') }}</a>
    <a class="item" href="/loan-originators">{{ __('static.MenuLoanOriginators') }}</a>
    <a class="item" href="/help">{{ __('static.MenuHelp') }}</a>
    <a class="item sidemenu-login" href="/login">{{ __('static.MenuLogin') }}</a>
    <a class="item sidemenu-register" href="/register">{{ __('static.MenuRegister') }}</a>
</div>

<!-- Page Contents -->
<div class="pusher">

    <div class="ui vertical masthead center aligned segment" id="header-container">
        <div class="ui container" id="top-header">
            <div class="ui large secondary pointing menu img-no-border two column row">
                <a href="/" class="logo-home column">
                    <img alt="logo" class="logo-svg" src="{{ assets_version(url('/') . '/images/icons/logo.svg') }}"/>
                </a>
                <div class="right item logo-home column">
                    <a class="ui basic no-box button desktop-login" href="/login">{{ __('static.MenuLogin') }}</a>
                    <a class="ui basic button mobile-login" href="/login">{{ __('static.MenuLogin') }}</a>
                    <a class="ui teal button desktop-register" href="/register">{{ __('static.MenuCreateAccount') }}</a>
                </div>
            </div>
        </div>

        <div class="ui container center aligned column" id="nav-container">
            <div class="center aligned ui large secondary pointing menu secondary-menu-container">
                <a class="toc item">
                    <i class="sidebar icon"></i>
                </a>
                <a class="item pull-left" href="/how-it-works">{{ __('static.MenuHowItWorks') }}</a>
                <a class="item" href="/invest">{{ __('static.MenuInvest') }}</a>
                <a class="item" href="/loan-originators">{{ __('static.MenuLoanOriginators') }}</a>
                <a class="item" href="/help">{{ __('static.MenuHelp') }}</a>
            </div>
        </div>
    </div>

    <div class="row w-100 justify-content-center">
        <div id="errorHandlerAjax" class="col-6">
        </div>
    </div>
    @yield('content')
    <form method="POST" action="{{ route('profile.register') }}" autocomplete="off" id="bottomFormSubmission">
        <div class="ui input" id="create-account-input">
            <h4 class="ui huge header center aligned text-gray">{{__('static.HomePageStartInvestingToday')}}</h4>
            <input
                    type="text"
                    placeholder="{{__('static.HomePageHeroEmailPlaceholder')}}" name="email"
                    required>
            @csrf
            @if (session('fail'))
                <div class="home-page-footer-error">
                    <div style="width: 100% !important; "
                         class="mb-1 d-block mt-1 p-1 mx-auto bg-danger-error footer-email-error"
                    >{{session('fail')}}</div>
                </div>
            @endif
            <button type="submit"
                    class="ui teal button text-center none-border">{{__('static.MenuCreateAccount')}}</button>
        </div>
    </form>
</div>
<div class="ui inverted vertical footer segment" id="footer-inverted">
    <div class="ui container">
        <div class="ui stackable inverted equal height stackable grid">
            <div class="four wide column">
                <a href="/" class="logo-home column logo-svg">
                    <img alt="white-logo" src="{{ assets_version(url('/') . '/images/icons/logo-white-xs.png') }}"/>
                </a>
                <div class="ui inverted link list">
                    <p class="company-info">Stik Credit JSC<br>Reg. No. 202557159<br>13 B Oborishte Sq.
                        <br>9700 Shumen, Bulgaria</p>
                </div>
            </div>

            <div class="three wide column help-footer-link">
                <h4 class="ui inverted header">{{ __('static.MenuHelp') }}</h4>
                <div class="ui inverted link list">
                    <a href="/help" class="item">{{ __('static.MenuGettingStarted') }}</a>
                    <a href="/help#Investing" class="item">{{ __('static.HelpPageTabInvesting') }}</a>
                    <a href="/help#AboutLoans" class="item">{{ __('static.HelpPageTabAboutLoans') }}</a>
                    <a href="/help#DepositWithdrawals" class="item">{{ __('static.HelpPageTabDepositWithdrawals') }}</a>
                    <a href="tel:+359-889-405-572" class="item"><i class="phone icon"></i> +359-889-405-572</a>
                    <a href="mailto:support@afranga.com" class="item"><i class="mail icon"></i>
                        support@afranga.com</a>
                </div>
            </div>
            <div class="three wide column footer-right-column">
                <h4 class="ui inverted header">{{ __('static.MenuCompany') }}</h4>
                <div class="ui inverted link list">
                    <a href="/about-us" class="item">{{ __('static.MenuAboutUs') }}</a>
{{--                    <a href="/blog" class="item">{{ __('static.MenuBlog') }}</a>--}}
                    <a href="refer-a-friend" class="item">{{ __('static.MenuAffiliate') }}</a>
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
                    <a href="https://www.facebook.com/afranga/" class="item" target="_blank"
                       rel="noreferrer">Facebook</a>
                    <a href="https://twitter.com/afranga1" class="item" target="_blank" rel="noreferrer">Twitter</a>
                    <a href="https://www.linkedin.com/company/afranga/" class="item" target="_blank" rel="noreferrer">Linkedin</a>
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

@stack('scripts')
</body>

</html>
