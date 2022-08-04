@extends('profile::layouts.login')

@section('title',  'Create account - ')

@section('content')
    <div class="container">
        <a href="{{route('homepage')}}">
            <img class="img" id="logo" src="{{ assets_version(asset('images/icons/logo.svg')) }}" style="width: 150px;"
                 alt="homepage"/></a>
        <div class="row vh-50">
            <div class="col-lg-6 mx-auto my-auto">
                <div id="formContent" class="w-100" style="max-width: 100%;">
                    <h2 class="text-left w-100">{{ __('common.CreateAnAccount') }}</h2>
                    <p class="mt-3">{{ __('common.FillInYourNames') }}</p>
                    <input id="submited" type="hidden" value="0"/>
                    <form method="POST" id="register-form" class="row" style="min-height: 360px"
                          action="{{ route('profile.createAccount.Submit') }}"
                          autocomplete="off">
                        @csrf
                        @if (session('fail'))
                            <div class="col-lg-12">
                                <div class="my-3 text-error">{!! preg_replace("/,/", " ", session('fail')) !!}</div>
                            </div>
                        @endif

                        <div id="custom-error" style="display: none;">
                            <div class="col-lg-12">
                                <div class="my-3 text-error" id="custom-error-text"></div>
                            </div>
                        </div>
                        <div class="col-lg-12 text-center">
                            <a id="tab1" class="register-tabs">Individual</a>
                            <a id="tab2" class="ml-4 register-tabs">Company</a>
                        </div>
                        <div style="padding-top: 20px;">
                            <div class="ui tab active" id="investor-type-individual"
                                 data-tab="investor-type-individual">

                            </div>
                            <div class="ui tab" id="investor-type-company" data-tab="investor-type-company">

                            </div>

                        </div>

                        <div class="col-12">
                            <input id="form_submit" class="w-100" type="submit"
                                   value="{{ __('common.Continue') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.4.2/dist/semantic.min.js"></script>
    <script>
        $('.additional-infotext').hide();

        $('#password').focusin(function () {
            $('.additional-infotext').show();
        });
        $('#password').focusout(function () {
            $('.additional-infotext').hide();
        });

        $("#register-form").on('submit', function (e) {
            e.preventDefault();
            hideCustomError();

            let pass = $('#password').val();
            if (pass.match(/[\|]/)) {
                showCustomError('The use of symbol "|" is not allowed in a password.');
                return false;
            }

            if ($('#submited').val() == 0) {
                this.submit();
                $('#register-form').attr('action', '');
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


        function showCustomError(msg) {
            $('#custom-error-text').html(msg);
            $('#custom-error').show();
        }

        function hideCustomError() {
            $('#custom-error-text').html('');
            $('#custom-error').hide();
        }

        investorType = window.localStorage.getItem('investorType');
        if (investorType === 'individual' || investorType === null) {
            individualType();
        }

        if (investorType === 'company') {
            companyType();
        }

        $("#tab1").click(function () {
            individualType();

        });

        $("#tab2").click(function () {
            companyType();
        });

        function individualType() {
            $.get('{{ route('profile.register.type' ,'individual') }}')
                .done(function (data) {
                    $('#investor-type-company').fadeOut(800, function () {

                    });

                    $('#investor-type-individual').fadeIn(800, function () {
                        $("#investor-type-individual").html(data);
                        $('#tab1').addClass('active');
                        $('#tab2').removeClass('active');
                        $.tab('change tab', 'investor-type-individual');
                        $("#investor-type-company").html('');
                    });
                    window.localStorage.setItem('investorType', 'individual');

                })
                .fail(function () {
                });
        }

        function companyType() {
            $.get('{{ route('profile.register.type' ,'company') }}')
                .done(function (data) {
                    $('#investor-type-individual').fadeOut(800, function () {

                    });

                    $('#investor-type-company').fadeIn(800, function () {
                        $("#investor-type-company").html(data);
                        $('#tab1').removeClass('active');
                        $('#tab2').addClass('active');
                        $.tab('change tab', 'investor-type-company');
                        $("#investor-type-individual").html('');
                    });

                    window.localStorage.setItem('investorType', 'company');
                })
                .fail(function () {
                });
        }
    </script>
@endpush

