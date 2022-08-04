@extends('profile::layouts.login')

@section('title',  'Forgot password - ')

@section('content')
    <div class="container">
        <a href="{{route('homepage')}}">
            <img class="img" id="logo" src="{{ assets_version(asset('images/icons/logo.svg')) }}" style="width: 150px;" alt="homepage"/></a>
        <div class="row vh-50">
            <div class="col-lg-6 mx-auto my-auto">
                <div id="formContent">
                    <h2>{{ __('common.PasswordRecovery') }}</h2>
                    <form method="POST" action="{{ route('profile.forgotPasswordSubmit') }}">
                        @csrf

                        <div class="login-form-head mb-4">
                            <p>{{ __('common.PasswordRecoverySubTitle') }}</p>
                        </div>
                        @if (session('fail'))
                            <div>
                                <div class="mb-2 bg-danger text-white">{{session('fail')}}</div>
                            </div>
                        @endif

                        <input type="text" id="login" class="w-100" name="email"
                               placeholder="{{ __('common.EmailAddress') }}" autocomplete="email">

                        <input id="form_submit" class="w-100" type="submit" value="{{ __('Send') }}" >

                        <div class="login-form-head ">
                            <p>
                                @if (session('success'))
                                    {{ __('common.PasswordRecoveryDescription') }}
                                @endif
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
