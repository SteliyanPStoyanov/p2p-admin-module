@extends('layouts.login')

@section('content')
    <div class="wrapper fadeInDown">
        <div id="formContent">

            <!-- Icon -->
            <div class="fadeIn first">
                <img class="img-fluid" id="logo" src="{{ assets_version(asset('images/icons/logo.png')) }}" alt="homepage"/>
            </div>


            <!-- Login Form -->
            <input id="submited" type="hidden" value="0"/>
            <form method="POST" id="adminLogin" action="{{ route('login') }}">
                {{ admin_csrf_field() }}
                <div class="login-form-head">
                    <p>Try to login into Afranga admin pane!</p>
                </div>
                @if (session('fail'))
                    <div>
                        <div class="p-3 mb-2 bg-danger text-white">{{session('fail')}}</div>
                    </div>
                @endif

                <input type="text" id="login" class="fadeIn second" name="username" placeholder="username">
                <input type="password" id="password" class="fadeIn third" name="password" placeholder="password">
                <input id="form_submit" class="bg-danger" type="submit" value="{{ __('Login') }}">
            </form>

        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $("#adminLogin").on('submit', function(e) {
            e.preventDefault();

            if ($('#submited').val() == 0) {
                this.submit();
                $('#adminLogin').attr('action', '');
                $('#submited').val(1);
                return true;
            }

          return false;
       });
    </script>
@endpush
