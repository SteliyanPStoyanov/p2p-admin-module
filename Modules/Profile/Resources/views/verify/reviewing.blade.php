@extends('profile::layouts.app')

@section('title',  'Pending verification - ')

@section('content')
    <div class="row">

        <div class="col-lg-6 mx-auto text-center text-black">
            <div id="formContent" class="w-100" style="max-width: 100%;">
                <div class="row mt-3">
                    <h2 class="text-center d-block w-100 font-weight-bold mt-3">{{ __('common.WeAreReviewingYourInformation') }}</h2>
                    <p class="mt-3 d-block w-100 text-center">{{ __('common.WeWillSendYouAnEmailOnce') }}</p>
                </div>

                <form method="POST" class="row" action="{{ route('profile.verify.reviewingSubmit') }}" autocomplete="off">
                    @csrf
                    @if (session('fail'))
                        <div class="col-12">
                            <div class="p-1 mb-1 bg-danger text-white w-100 rounded-lg">{{session('fail')}}</div>
                        </div>
                    @endif
                    <div class="form-group w-100 row">

                        <div class="col-12 mt-5">

                            <input id="form_submit" class="d-block ui teal button w-100 mx-auto text-center" type="submit"
                                   value="{{ __('common.Continue') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

