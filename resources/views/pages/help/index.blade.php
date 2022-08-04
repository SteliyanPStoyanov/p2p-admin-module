@extends('pages.layouts.app')

@section('title',  'Help - ')

@section('style')
    @parent
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/help-styles.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/bootstrap.min.css') }}">
@endsection

@section('content')

    <div class="ui vertical segment features-container inner-title" id="help-container">
        <span id="Investing"></span>
        <span id="AboutLoans"></span>
        <span id="DepositWithdrawals"></span>
        <div class="ui three column stackable left aligned grid container">
            <h2 class="ui header left aligned text-black" style="padding: 0">{{__('static.HelpPageTitle')}}</h2>
            <p class="help-text">@lang('static.HelpPageHelpText')</p>
        </div>
        <div class="ui vertical segment stackable left aligned grid container">

            <div id="menu-tabs" class="ui top attached tabular menu">
                <a class="item active" data-tab="first">{{__('static.HelpPageTabGettingStarted')}}</a>
                <a class="item" data-tab="second">{{__('static.HelpPageTabInvesting')}}</a>
                <a class="item" data-tab="third">{{__('static.HelpPageTabAboutLoans')}}</a>
                <a class="item" data-tab="fourth">{{__('static.HelpPageTabDepositWithdrawals')}}</a>
            </div>
            <div id="cont-m">
                <div class="ui bottom attached tab segment active" data-tab="first">
                    <div class="ui styled accordion">
                        <div class="title active">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion1Title')}}
                        </div>
                        <div class="content active">
                            <p>{{__('static.HelpPageTab1Accordion1Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion2Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab1Accordion2Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion22Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab1Accordion22Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion3Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab1Accordion3Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion4Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab1Accordion4Text')}}<a
                                    href="/register">{{__('static.MenuRegister')}}</a>
                            </p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion5Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab1Accordion5Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion55Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab1Accordion55Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion6Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab1Accordion6Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion7Title')}}
                        </div>
                        <div class="content">
                            <p>{!! trans('static.HelpPageTab1Accordion7Text') !!}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab1Accordion8Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab1Accordion8Text')}}</p>
                        </div>
                    </div>
                </div>
                <div class="ui bottom attached tab segment" data-tab="second">
                    <div class="ui styled accordion">
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion1Title')}}
                        </div>
                        <div class="content">
                            <p>{!! trans('static.HelpPageTab2Accordion1Text')!!}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion2Title')}}
                        </div>
                        <div class="content">
                            <p>{!! trans('static.HelpPageTab2Accordion2Text')!!}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion3Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab2Accordion3Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion4Title')}}
                        </div>
                        <div class="content">
                            <p>{!! trans('static.HelpPageTab2Accordion4Text')!!}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion5Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab2Accordion5Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion6Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab2Accordion6Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion7Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab2Accordion7Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion8Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab2Accordion8Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion9Title')}}
                        </div>
                        <div class="content">
                            <p>{!! trans('static.HelpPageTab2Accordion9Text')!!}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion11Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab2Accordion11Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab2Accordion12Title')}}
                        </div>
                        <div class="content">
                            <p>{!! trans('static.HelpPageTab2Accordion12Text')!!}</p>
                        </div>
                    </div>
                </div>
                <div class="ui bottom attached tab segment" data-tab="third">
                    <div class="ui styled accordion">
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab3Accordion1Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab3Accordion1Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab3Accordion2Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab3Accordion2Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab3Accordion3Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab3Accordion3Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab3Accordion4Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab3Accordion4Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab3Accordion5Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab3Accordion5Text')}}</p>
                        </div>
                    </div>
                </div>
                <div class="ui bottom attached tab segment" data-tab="fourth">
                    <div class="ui styled accordion">
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab4Accordion1Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab4Accordion1Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab4Accordion2Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab4Accordion2Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab4Accordion3Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab4Accordion3Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab4Accordion4Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab4Accordion4Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab4Accordion5Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab4Accordion5Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab4Accordion55Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab4Accordion55Text')}}</p>
                            <table class="ui table">
                                <thead>
                                <tr>
                                    <th class="left aligned">{{__('static.HelpPageTab4Table1')}}</th>
                                    <th class="left aligned">{{__('static.HelpPageTab4Table1Content')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table2')}}
                                    </td>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table2Content')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table3')}}
                                    </td>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table3Content')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table4')}}
                                    </td>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table4Content')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table5')}}
                                    </td>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table5Content')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table6')}}
                                    </td>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table6Content')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table7')}}
                                    </td>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table7Content')}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table8')}}
                                    </td>
                                    <td class="left aligned">
                                        {{__('static.HelpPageTab4Table8Content')}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab4Accordion6Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab4Accordion6Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab4Accordion7Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab4Accordion7Text')}}</p>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
                            {{__('static.HelpPageTab4Accordion8Title')}}
                        </div>
                        <div class="content">
                            <p>{{__('static.HelpPageTab4Accordion8Text')}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script nonce="helpPage">
        $(document)
            .ready(function () {
                let url = document.location.toString();
                if (url.match('#')) {
                    if (url.split('#')[1]) {
                        $('.item').removeClass('active');
                        $('.tab').removeClass('active');
                        if (url.split('#')[1] == 'Investing') {
                            $("#menu-tabs .item:nth-child(2)").addClass('active');
                            $("#cont-m .tab:nth-child(2)").addClass('active');
                        }
                        if (url.split('#')[1] == 'AboutLoans') {
                            $("#menu-tabs .item:nth-child(3)").addClass('active');
                            $("#cont-m .tab:nth-child(3)").addClass('active');
                        }
                        if (url.split('#')[1] == 'DepositWithdrawals') {
                            $("#menu-tabs .item:nth-child(4)").addClass('active');
                            $("#cont-m .tab:nth-child(4)").addClass('active');
                        }
                    }
                }

            });
        $('.help-footer-link .link.list .item').click(function () {
            setTimeout(function () {
                location.reload();
            }, 100);
        });
    </script>
@endpush
