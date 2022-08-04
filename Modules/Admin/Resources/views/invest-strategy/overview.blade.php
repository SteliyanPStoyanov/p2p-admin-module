@extends('layouts.app')
@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection
@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <ul class=" nav nav-tabs mt-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab"
                               aria-controls="overview" aria-selected="true">{{__('common.Overview')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab"
                               aria-controls="history" aria-selected="false">{{__('common.InvestStrategyHistory')}}</a>
                        </li>

                    </ul>
                    <br>
                    <div class="tab-content mb-1 mt-1" id="myTabContent">
                        <div class="tab-pane fade show active" id="overview" role="tabpanel"
                             aria-labelledby="overview-tab">
                            @include('admin::invest-strategy.overview-default')
                        </div>
                        <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                            @include('admin::invest-strategy.history')
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('show.bs.tab', function (e) {

            window.location.hash = e.target.hash;

            if (e.target.hash === '#history') {
                e.preventDefault();
                location.reload();
            }

            if (e.target.hash === '#overview') {
                e.preventDefault();
                location.reload();
            }

            $("html, body").scrollTop(0);
        })
    </script>
@endpush
