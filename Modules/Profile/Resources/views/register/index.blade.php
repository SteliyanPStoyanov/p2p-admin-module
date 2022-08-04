@extends('profile::layouts.login')

@section('title',  'Create account - ')

@section('content')
    <div class="container" id="register-page">
        <a href="{{route('homepage')}}">
            <img class="img" id="logo" src="{{ assets_version(asset('images/icons/logo.svg')) }}" style="width: 150px;" alt="homepage"/></a>
        <div class="row vh-50">
            <div class="col-lg-6 mx-auto my-auto">
                <div id="formContent">
                    <h2>{{ __('common.CreateAnAccount') }}</h2>
                    <form method="POST" action="{{ route('profile.register') }}" autocomplete="off">
                        @csrf
                        @if(!empty($parentInvestor))
                            <input type="hidden" value="{{$parentInvestor->investor_id}}" name="referral_id">
                        @endif

                        <input class="w-100" type="text" value="{{ old('email') }}" name="email"
                               placeholder="{{ __('common.EmailAddress') }}"
                               autocomplete="off">
                        @if (session('fail'))
                            <div>
                                <div class="px-2 mt-1 text-danger-red">{{session('fail')}}</div>
                            </div>
                        @endif
                        <div class="w-100 float-left">
                            <input id="form_submit" class="w-100" type="submit"
                                   value="{{ __('common.Continue') }}">
                        </div>
                        <p>
                            <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-check"
                                 fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                            </svg>
                            {{ __('common.EarnUpTo') }}
                        </p>
                        <p>
                            <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-check"
                                 fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                            </svg>
                            {{ __('common.TrustedLoanOriginator') }}
                        </p>
                        <p>
                            <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-check"
                                 fill="currentColor"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
                            </svg>
                            {{ __('common.AllLoansGuarantee') }}

                        </p>
                        <p class="mt-5"><span class="pr-2">{{ __('common.AlreadyRegistered') }}</span>
                            <a class="mr-3"
                               href="{{route('profile')}}">{{ __('common.Login') }}.</a></p>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection

