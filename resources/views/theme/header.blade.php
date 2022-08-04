@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

<header class="topbar" data-navbarbg="skin6">
    <nav class="navbar top-navbar navbar-expand-md">
        <div class="navbar-header" data-logobg="skin6">
            <!-- This is for the sidebar toggle which is visible on mobile only -->
            <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i
                    class="ti-menu ti-close"></i></a>
            <!-- ============================================================== -->
            <!-- Logo -->
            <!-- ============================================================== -->
            <div class="navbar-brand">
                <!-- Logo icon -->
                <a href="{{route('admin.dashboard')}}">
                    <b class="logo-icon">
                        <!-- Dark Logo icon -->
                        <img class="img-fluid" src="{{ assets_version(asset('images/icons/logo.png')) }}" alt="homepage"
                             class="dark-logo"/>
                    </b>
                    <!--End Logo icon -->
                </a>
            </div>
            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Toggle which is visible on mobile only -->
            <!-- ============================================================== -->
            <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
               data-toggle="collapse" data-target="#navbarSupportedContent"
               aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i
                    class="ti-more"></i></a>
        </div>
        <!-- ============================================================== -->
        <!-- End Logo -->
        <!-- ============================================================== -->

        <div class="navbar-collapse collapse" id="navbarSupportedContent">
            <!-- ============================================================== -->
            <!-- toggle and nav items -->
            <!-- ============================================================== -->
        @include('theme.notifications')
        <!-- ============================================================== -->
            <!-- Right side toggle and nav items -->
            <!-- ============================================================== -->
            <ul class="navbar-nav float-right">
                <!-- Search bar -->
                <li class="nav-item d-none d-md-block">

                    <a class="nav-link" href="javascript:void(0)">
                        <form>
                            @csrf
                            <div class="customize-input" style="min-width: 150px;">
                                <input autocomplete="off" type="text" id="search" name="search"
                                       placeholder="{{ __('common.Search') }}..."
                                       required
                                       class="form-control custom-shadow custom-radius border-0 bg-white">
                                <i class="form-control-icon" data-feather="search"></i>
                            </div>
                        </form>

                        <div id="clients" class="list-group list-group-item clients-list" style="display: none;">
                        </div>
                    </a>
                </li>

                <!-- Profile and logout -->
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            <img src="{{ url(Auth::user()->getAvatarPath()) }}"
                                 alt="user"
                                 class="rounded-circle"
                                 width="40">

                            <span class="ml-2 d-none d-lg-inline-block"><span>{{ __('common.Hello') }},</span> <span
                                    class="text-dark">{{ Auth::user()->twoNames }}</span> <i
                                    data-feather="chevron-down"
                                    class="svg-icon"></i></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                            <a class="dropdown-item"
                               href="{{ route('admin.administrators.edit', Auth::user()->getAuthIdentifier()) }}">
                                <i data-feather="user" class="svg-icon mr-2 ml-1"></i>
                                {{ __('common.ProfileSetting') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i data-feather="mail" class="svg-icon mr-2 ml-1"></i>
                                {{ __('common.Messages') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                <i data-feather="power" class="svg-icon mr-2 ml-1"></i>
                                {{ __('common.Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ admin_csrf_field() }}
                            </form>
                    </li>
                @endauth

            </ul>
        </div>
    </nav>
</header>
