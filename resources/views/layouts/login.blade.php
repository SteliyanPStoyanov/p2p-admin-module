<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CRM') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ assets_version(asset('images/icons/favicon.ico')) }}">

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ assets_version(asset('dist/css/login.css')) }}">
    <link rel="stylesheet" href="{{ assets_version(asset('dist/css/style.min.css')) }}">

</head>
<body>

    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
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
