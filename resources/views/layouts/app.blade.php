<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CRM: {{ config('app.name', 'CRM') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ assets_version(asset('images/icons/favicon.ico')) }}">

    <link rel="stylesheet" href="{{ assets_version(asset('dist/css/style.min.css')) }}">
    <link rel="stylesheet" href="{{ assets_version(asset('css/search-user-input.css')) }}">
    <link rel="stylesheet" href="{{ assets_version(asset('css/admin-styles.css')) }}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
          integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

 @livewireStyles
@yield('style')


<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="{{ assets_version(asset('js/html5shiv.js')) }}"></script>
    <script src="{{ assets_version(asset('js/respond.min.js')) }}"></script>
    <![endif]-->

</head>
<body>
<!-- preloader area start -->
<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>
<!-- preloader area end -->


<!-- page container area start -->
<div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
     data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">

    @include('theme.header')

    @include('theme.sidebar-menu')

    <div class="page-wrapper">

        @include('theme.breadcrumb')
        <div class="container-fluid">
            @yield('content')
        </div>

        @include('theme.footer')

    </div>

</div>
<!-- page container area end -->

<x-delete-modal/>
<!-- JS scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="{{ assets_version(asset('assets/libs/jquery/dist/jquery.min.js')) }}"></script>
<script src="{{ assets_version(asset('assets/libs/bootstrap/dist/js/bootstrap.min.js')) }}"></script>
<script src="{{ assets_version(asset('dist/js/feather.min.js')) }}"></script>
<script src="{{ assets_version(asset('assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js')) }}"></script>
<script src="{{ assets_version(asset('dist/js/sidebarmenu.js')) }}"></script>
<script src="{{ assets_version(asset('dist/js/custom.min.js')) }}"></script>
<script src="{{ assets_version(asset('dist/js/app-style-switcher.js')) }}"></script>
<script src="{{ assets_version(asset('js/moment.min.js')) }}"></script>
<script src="{{ assets_version(asset('js/daterangepicker.min.js')) }}"></script>
<link href="{{ assets_version(asset('css/daterangepicker.css')) }}" rel="stylesheet">
<script src="{{ assets_version(asset('js/dateRangePicker.js')) }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@livewireScripts
@stack('scripts')
</body>
</html>
