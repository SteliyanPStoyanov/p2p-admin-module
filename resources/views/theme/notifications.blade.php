<ul class="navbar-nav float-left mr-auto ml-3 pl-1 w-100">
    <!-- Notification -->
    <li class="nav-item dropdown flex-grow-0">
        @php
            $numResults =
            (bool) session('fail') + (bool) session('errors') + (bool) session('success') + (bool) session('info');
        @endphp
        <a class="nav-link dropdown-toggle pl-md-3 position-relative" href="javascript:void(0)"
           id="bell" role="button" data-toggle="dropdown" aria-haspopup="true"
           aria-expanded="true">
            <span><i data-feather="bell" class="svg-icon"></i></span>
            <span class="badge
            @if(session()->has('success'))
                badge-success
            @elseif(session()->has('info'))
                badge-info
            @elseif(session()->has('fail') || session()->has('errors'))
                badge-danger
            @else
                badge-pink
            @endif
                notify-no rounded-circle">
                {{ $numResults }}
            </span>
        </a>
        <div class="dropdown-menu dropdown-menu-left mailbox animated bounceInDown">
            <ul class="list-style-none">
                <li>
                    <div class="message-center notifications position-relative">

                        @if (session('fail'))

                            <div href="javascript:void(0)"
                                 class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                <div class="btn btn-danger rounded-circle btn-circle"><i
                                        data-feather="airplay" class="text-white"></i></div>
                                <div class="d-inline-block v-middle pl-2">
                                    <h6 class="message-title mb-0 mt-1">Fail</h6>
                                    <span class="font-12 text-nowrap d-block text-muted">
                                                    {{ session('fail') }}
                                                </span>
                                </div>
                            </div>

                        @endif

                        @if (session('errors'))

                            <div href="javascript:void(0)"
                                 class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                <div class="btn btn-danger rounded-circle btn-circle"><i
                                        data-feather="airplay" class="text-white"></i></div>
                                <div class="d-inline-block v-middle pl-2">
                                    <h6 class="message-title mb-0 mt-1">Error</h6>
                                    <span class="font-12 text-nowrap d-block text-muted">
                                                    {{ session('errors') }}
                                                </span>
                                </div>
                            </div>

                        @endif

                        @if (session('success'))
                            <div href="javascript:void(0)"
                                 class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                            <span class="btn btn-success text-white rounded-circle btn-circle"><i
                                                    data-feather="calendar" class="text-white"></i></span>
                                <div class="d-inline-block v-middle pl-2">
                                    <h6 class="message-title mb-0 mt-1">Success</h6>
                                    <span class="font-12 text-nowrap d-block text-muted text-truncate">
                                                    {{ session('success') }}
                                                </span>
                                </div>
                            </div>
                        @endif

                        @if (session('info'))
                            <div href="javascript:void(0)"
                                 class="message-item d-flex align-items-center border-bottom px-3 py-2">
                                            <span class="btn btn-primary rounded-circle btn-circle"><i
                                                    data-feather="box" class="text-white"></i></span>
                                <div class="w-75 d-inline-block v-middle pl-2">
                                    <h6 class="message-title mb-0 mt-1">Info</h6>
                                    <span class="font-12 text-nowrap d-block text-muted">
                                                    {{ session('info') }}
                                                </span>
                                    <span class="font-12 text-nowrap d-block text-muted">9:02 AM</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </li>
            </ul>
        </div>
    </li>
    <!-- End Notification -->
    <!-- ============================================================== -->
    <!-- create new -->
    <!-- ============================================================== -->
    <!--
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i data-feather="settings" class="svg-icon"></i>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="#">Action</a>
            <a class="dropdown-item" href="#">Another action</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Something else here</a>
        </div>
    </li>
    -->
    <li class="nav-item flex-grow-0">
        <div class="customize-input nav-link">
            <select
                class="custom-select custom-select-set bg-white border-0 custom-shadow custom-radius">
                <option selected>{{ config('app.locale', '') }}</option>
                <option>{{ config('app.fallback_locale', '') }}</option>
            </select>
        </div>
    </li>
    <li class="nav-item flex-grow-1 justify-content-center display-flex" id="errorHandlerAjax">
        @if(session('errors') || session('fail'))
            <div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert"
                 id="cstm-danger-alert">
                {{ session('errors') ?? session('fail') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
    </li>
</ul>
