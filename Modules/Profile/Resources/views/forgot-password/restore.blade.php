@extends('profile::layouts.login')

@section('title',  'Restore password - ')

@section('content')
    <div class="container">
        <a href="{{route('homepage')}}">
            <img class="img" id="logo" src="{{ assets_version(asset('images/icons/logo.svg')) }}" style="width: 150px;" alt="homepage"/></a>
        <div class="row vh-50">
            <div class="col-lg-6 mx-auto my-auto">
                <div id="formContent">
                    <h2>{{ __('common.CreateNewPassword') }}</h2>
                    <div class="login-form-head mb-3">
                        <p>{{ __('common.CreateNewPasswordTitle') }}</p>
                    </div>
                    <form method="POST" action="{{ route('profile.restorePasswordSubmit') }}" autocomplete="false">
                        @csrf

                        @if (session('fail'))
                            <div>
                                <div class="p-1 mb-1 bg-danger text-white">{{session('fail')}}</div>
                            </div>
                        @endif
                        @if (session('success'))
                            <div>
                                <div class="p-1 mb-1 bg-success text-white">{{session('success')}}</div>
                            </div>
                        @endif

                        <div>
                            <input id="password" type="password" class="w-100" name="password"
                                   placeholder="{{ __('common.Password') }}" autocomplete="new-password">
                            <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                        </div>
                        <div>
                            <input id="re-password" type="password" class="w-100 mt-2" name="re-password"
                                   placeholder="{{ __('common.RepeatPassword') }}" autocomplete="renew-password">
                            <span toggle="#re-password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                        </div>
                        <p class="additional-infotext mt-1">
                            {{ __('common.PasswordRule') }}
                        </p>
                        <input type="hidden" name="hash" value="{{$hash}}">
                        <input id="form_submit" class="w-100" type="submit" value="{{ __('common.Confirm') }}">

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>

        $('.additional-infotext').hide();

        $('#password').focusin(function () {
            $('.additional-infotext').show();
        });
        $('#password').focusout(function () {
            $('.additional-infotext').hide();
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
