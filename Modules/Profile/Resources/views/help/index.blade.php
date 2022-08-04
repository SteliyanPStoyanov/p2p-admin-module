@extends('profile::layouts.app')

@section('title',  'Help - ')

@section('content')
    <div class="row">
        <div class="col-lg-12 mt-5">
            <h2 class="">{{__('static.HelpPageTitle')}}</h2>
            <p class="">@lang('static.HelpPageHelpText')</p>
            
            <ul class="nav nav-tabs w-100 mt-5" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="GettingStarted-tab" data-toggle="tab" href="#GettingStarted"
                       role="tab" aria-controls="home"
                       aria-selected="true">{{__('static.HelpPageTabGettingStarted')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="Investing-tab" data-toggle="tab" href="#Investing" role="tab"
                       aria-controls="profile" aria-selected="false">{{__('static.HelpPageTabInvesting')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="AboutLoans-tab" data-toggle="tab" href="#AboutLoans" role="tab"
                       aria-controls="contact" aria-selected="false">{{__('static.HelpPageTabAboutLoans')}}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="DepositWithdrawals-tab" data-toggle="tab" href="#DepositWithdrawals"
                       role="tab"
                       aria-controls="contact" aria-selected="false">{{__('static.HelpPageTabDepositWithdrawals')}}</a>
                </li>
            </ul>
            <div class="tab-content pt-3" id="myTabContent">
                <div class="tab-pane fade show active" id="GettingStarted" role="tabpanel"
                     aria-labelledby="GettingStarted-tab">
                    <div class="accordion">
                        @include('profile::help.tab-one')
                    </div>
                </div>
                <div class="tab-pane fade" id="Investing" role="tabpanel" aria-labelledby="Investing-tab">
                    <div class="accordionTwo">
                        @include('profile::help.tab-two')
                    </div>
                </div>
                <div class="tab-pane fade" id="AboutLoans" role="tabpanel" aria-labelledby="AboutLoans-tab">
                    <div class="accordionThree">
                        @include('profile::help.tab-three')
                    </div>
                </div>
                <div class="tab-pane fade" id="DepositWithdrawals" role="tabpanel"
                     aria-labelledby="DepositWithdrawals-tab">
                    <div class="accordionFour">
                        @include('profile::help.tab-four')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(".accordion-title-select").click(function () {
            $(this).find('.rotate').toggleClass("down");
        })
        $(document)
            .ready(function () {
                let url = document.location.toString();
                if (url.match('#')) {
                    if (url.split('#')[1]) {
                        $('.nav-link').removeClass('active');
                        $('.tab-pane').removeClass('show active');
                        if (url.split('#')[1] == 'Investing') {
                            $("#Investing-tab").addClass('active');
                            $("#myTabContent .tab-pane:nth-child(2)").addClass('show active');
                        }
                        if (url.split('#')[1] == 'AboutLoans') {
                            $("#AboutLoans-tab").addClass('active');
                            $("#myTabContent .tab-pane:nth-child(3)").addClass('show active');
                        }
                        if (url.split('#')[1] == 'DepositWithdrawals') {
                            $("#DepositWithdrawals-tab").addClass('active');
                            $("#myTabContent .tab-pane:nth-child(4)").addClass('show active');
                        }
                    }
                }
            });
    </script>
@endpush
