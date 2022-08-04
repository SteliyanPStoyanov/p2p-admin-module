@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/investor-instalments.css')) }}">
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">

@endsection
@section('content')
    <div>
        <div class="card">
            <div class="card-body">
                <h2 class="page-title text-truncate text-dark font-weight-medium mb-1 text-center mb-3">
                    <strong> #{{$loan->loan_id}} - {{loanType($loan->type)}}</strong>
                </h2>
                <ul class="nav nav-tabs mt-1" id="loansTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="loan-details-tab" data-toggle="tab" href="#loan-details"
                           role="tab"
                           aria-controls="loan-details" aria-selected="true">{{__('common.LoanDetails')}}</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#investor-instalments" role="tab"
                           aria-controls="contact" aria-selected="false">{{__('common.InvestorInstalments')}}</a>
                    </li>
                </ul>
                <div class="tab-content mb-1 mt-1 pt-3" id="myTabContent">
                    <div class="tab-pane fade show active" id="loan-details" role="tabpanel"
                         aria-labelledby="loan-details-tab">
                        @include('admin::loans.components.loan-details')
                    </div>
                    <div class="tab-pane fade" id="investor-instalments" role="tabpanel" aria-labelledby="contact-tab">
                        @include('admin::loans.components.loan-investor-instalments')
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let url = document.location.toString();
        if (url.match('#')) {

            $('.nav-tabs a[href="#' + url.split('#')[1].split('&')[0] + '"]').tab('show');
        }

        if (url.match('&')) {
            let collapsParam = url.split('&');

            $('#collapse' + collapsParam[1].split('=')[1]).addClass('show active');
             $('#heading' + collapsParam[1].split('=')[1] + ' button').attr("aria-expanded","true");
            $('#collapse-investment' + collapsParam[2].split('=')[1]).addClass('show active');
             $('#heading-investment' + collapsParam[2].split('=')[1] + ' button').attr("aria-expanded","true");


        }
        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
            $("html, body").scrollTop(0);
        })
    </script>
@endpush
