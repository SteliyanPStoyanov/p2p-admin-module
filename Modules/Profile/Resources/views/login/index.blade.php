@extends('profile::layouts.login')

@section('title',  'Log in - ')

@section('content')
    <div class="container" id="login-page">
        <a href="{{route('homepage')}}">
            <img class="img" id="logo" src="{{ assets_version(asset('images/icons/logo.svg')) }}" style="width: 150px;" alt="homepage"/></a>
        <div class="row vh-50">
            <div class="col-lg-6 mx-auto">
                <div id="formContent">
                    <h2>{{ __('common.TryToLoginInto') }}</h2>
                    <p>{{ __('common.TryToLoginIntoInfo') }}<a href="{{route('profile.register')}}">here</a></p>
                    <input id="submited" type="hidden" value="0"/>
                    <input id="submitedReCaptcha" type="hidden" value="0"/>
                    <form method="POST" id="login" action="{{ route('profile.login') }}">
                        @csrf
                        @if (session('fail'))
                            <div>
                                <div class="mb-1 bg-danger text-white">{{session('fail')}}</div>
                            </div>
                        @endif

                        @if (session('success'))
                            <div>
                                <div class="p-1 mb-1 bg-success text-white">{{session('success')}}</div>
                            </div>
                        @endif
                        <input type="text" class="w-100 mb-4 mt-4" name="email"
                               placeholder="{{ __('common.EmailAddress') }}">
                        <div>
                            <input id="password" type="password" class="w-100" name="password"
                               placeholder="{{ __('common.Password') }}">
                            <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                        </div>

                        <button
                                id="form_submit"
                                class="w-100 g-recaptcha"
                                data-sitekey="6LeinwQaAAAAAOG1c66YxZWjPOyJMK8JYfLaiXtb"
                                data-callback='onSubmit'
                                data-action='submit'>{{ __('common.Login') }}
                        </button>
                        <p class="forgot-password"><a
                                    href="{{route('profile.forgotPassword')}}">{{ __('common.ForgotPassword') }}</a></p>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @if(isProd())
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <script>
            function onSubmit(token) {
                if ($('#submitedReCaptcha').val() == 0) {
                    document.getElementById("login").submit();
                    $('#submitedReCaptcha').val(1);
                }
            }
        </script>
    @endif
    <script>
        $("#login").on('submit', function(e) {
            e.preventDefault();
            $("#form_submit").attr("disabled", true);

            if ($('#submited').val() == 0) {
                this.submit();
                $('#login').attr('action', '');
                $('#submited').val(1);
                return true;
            }

            return false;
        });

        $(".toggle-password").click(function () {

            $(this).toggleClass("fa-eye fa-eye-slash");
            let input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
@endpush
