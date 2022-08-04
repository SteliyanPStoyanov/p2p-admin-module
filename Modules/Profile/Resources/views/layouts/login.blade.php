<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;300;400;600;700;800&display=swap"
          rel="stylesheet">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ assets_version(asset('images/icons/favicon.ico')) }}">

    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/semantic.min.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/main-semantic-styles.css') }}">
    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ assets_version(asset('dist/css/style.min.css')) }}">
    <link rel="stylesheet" href="{{ assets_version(asset('dist/css/profile-login.css')) }}">

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

    @yield('style')

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

<!--[if lt IE 8]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a
        href="http://browsehappy.com/">upgrade
        your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- preloader area end -->

    <div>
        @yield('content')
    </div>

    <script src="{{ assets_version(asset('assets/libs/jquery/dist/jquery.min.js')) }}"></script>
    <script src="{{ assets_version(asset('assets/libs/bootstrap/dist/js/bootstrap.min.js')) }}"></script>
    @stack('scripts')
</body>
</html>
