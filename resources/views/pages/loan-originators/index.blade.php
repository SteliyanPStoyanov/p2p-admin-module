@extends('pages.layouts.app')

@section('title',  'Loan originators - ')

@section('style')

    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/loan-originators-styles.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/bootstrap.min.css') }}">

@endsection

@section('content')

    <div class="ui vertical segment features-container" id="loan-originators-container">
        <div class="ui stackable grid container heading-container">
            <h2 class="ui sixteen wide column header left aligned text-black" style="margin-top: 0; padding: 0">{{__('static.LoanOriginatorsPageTitle')}}</h2>
            <div class="ui eight wide column"><a href="https://stikcredit.com"
                                                 target="_blank"><img
                            src="{{ assets_version(url('/') . '/images/loan-originators/stikcredit-logo.svg') }}"
                            alt="stikcredit-logo"
                            class="ui medium rounded image"></a>
            </div>
        </div>

        <div class="ui vertical segment features-container" id="loan-originators-about">
            <div class="ui three column stackable center aligned grid container">
                <div class="row">
                    <div class="ui eight wide column left aligned">
                        <h3 class="ui sixteen wide column huge header left aligned text-black">{{__('static.LoanOriginatorsAboutTitle')}}</h3>
                        <p class="ui sixteen wide column left aligned text-black">{!! trans('static.LoanOriginatorsAboutText')!!}</p>
                    </div>
                    <div class="ui five wide column right aligned">
                        <h3 class="ui sixteen wide column huge header right aligned text-black">{{__('static.LoanOriginatorsDocumentsTitle')}}</h3>
                        <a href="{{ url('/') }}/docs/pdf/stikcredit_presentation_1Q21.pdf" target="_blank"
                           class="ui sixteen wide column right aligned">{{__('static.LoanOriginatorsPageFeature1Text')}}</a>
                        <a href="{{ url('/') }}/docs/pdf/1Q21_unaudited_stikcredit_financials_EN.pdf"
                           target="_blank" class="ui sixteen wide column right aligned">{{__('static.LoanOriginatorsPageFeature2Text')}}</a>
                        <a href="{{ url('/') }}/docs/pdf/stikcredit_annual_report_ENG.pdf" target="_blank"
                           class="ui sixteen wide column right aligned">{{__('static.LoanOriginatorsPageFeature3Text')}}</a>
                        <a href="{{ url('/') }}/docs/pdf/consumer_loan_agreement_GTC_stikcredit.pdf" target="_blank"
                           class="ui sixteen wide column right aligned">{{__('static.LoanOriginatorsPageFeature4Text')}}</a>
                    </div>
                </div>
            </div>
            <div class="ui stackable grid container" id="company-stats">
                <h2 class="ui sixteen wide column huge header left aligned text-black">{{__('static.LoanOriginatorsCompanyStats')}}</h2>
                <div class="ui seven wide column left aligned">
                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle1')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText1')}}</div>
                    </div>
                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle2')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText2')}}</div>
                    </div>
                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle3')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText3')}}</div>
                    </div>
                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle4')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText4')}}</div>
                    </div>
                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle5')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText5')}}</div>
                    </div>
                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle6')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText6')}}</div>
                    </div>
                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle7')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText7')}}</div>
                    </div>
                </div>
                <div class="ui seven wide column right aligned">
                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle8')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText8')}}</div>
                    </div>
                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle9')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText9')}}</div>
                    </div>

                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle10')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText10')}}</div>
                    </div>

                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle11')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText11')}}</div>
                    </div>

                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle12')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText12')}}</div>
                    </div>

                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle13')}}</div>
                        <div class="mobile-table-content">{{__('static.LoanOriginatorsCompanyText13')}}</div>
                    </div>

                    <div class="border-bottom">
                        <div class="mobile-table-title">{{__('static.LoanOriginatorsCompanyTitle14')}}</div>
                        <div class="mobile-table-content"><a href="https://stikcredit.com"
                                                             target="_blank">stikcredit.com</a></div>
                    </div>

                </div>
            </div>

        </div>
    </div>


@endsection
