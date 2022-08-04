@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="page-title text-truncate text-dark font-weight-medium mb-1 text-center mb-3">
                        <strong>
                            @if($investor->political === 1)
                                <div class="btn waves-effect btn-rounded btn-danger">
                                    <h1 class="font-weight-bold text-white"
                                        style="line-height: 24px; padding: 0 20px;">{{__('common.Pep')}}</h1>
                                </div>
                            @endif
                            {{__('common.Name')}}
                            : {{($investor->type == \Modules\Common\Entities\Investor::TYPE_INDIVIDUAL)
                                ? $investor->fullName() : $investor->company->first()->name}}
                            ,
                            {{__('common.InvestorID')}}: {{$investor->investor_id}},
                            {{__('common.Status')}}: {{$investor->status}}
                        </strong></h2>
                    <ul class=" nav nav-tabs mt-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab"
                               aria-controls="overview" aria-selected="true">{{__('common.Overview')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="wallet-tab" data-toggle="tab" href="#wallet" role="tab"
                               aria-controls="wallet" aria-selected="false">{{__('common.Wallet')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="investments-tab" data-toggle="tab" href="#investments" role="tab"
                               aria-controls="investments" aria-selected="false">{{__('common.Investments')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab"
                               aria-controls="documents" aria-selected="false">{{__('common.Documents')}}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="log-tab" data-toggle="tab" href="#log" role="tab"
                               aria-controls="log" aria-selected="false">{{__('common.Log')}}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" id="verification-tab" data-toggle="tab" href="#verification" role="tab"
                               aria-controls="verification" aria-selected="false">{{__('common.Verification')}}</a>
                        </li>
                    </ul>
                    <br>
                    <div class="tab-content mb-1 mt-1" id="myTabContent">
                        <div class="tab-pane fade show active" id="overview" role="tabpanel"
                             aria-labelledby="overview-tab">
                            @include('admin::investor.components.overview')
                        </div>
                        <div class="tab-pane fade" id="wallet" role="tabpanel" aria-labelledby="wallet-tab">
                            @include('admin::investor.components.wallet')
                        </div>
                        <div class="tab-pane fade" id="investments" role="tabpanel" aria-labelledby="investments-tab">
                            @include('admin::investor.components.investment')
                        </div>
                        <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                            @include('admin::investor.components.documents')
                        </div>
                        <div class="tab-pane fade" id="log" role="tabpanel" aria-labelledby="log-tab">
                            @include('admin::investor.components.log')
                        </div>
                        <div class="tab-pane fade" id="verification" role="tabpanel" aria-labelledby="verification-tab">
                            @include('admin::investor.components.verification')
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
        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('show.bs.tab', function (e) {

            window.location.hash = e.target.hash;

            if (e.target.hash === '#investments') {
                e.preventDefault();
                location.reload();
            }

            if (e.target.hash === '#log') {
                e.preventDefault();
                location.reload();
            }

            if (e.target.hash === '#wallet') {
                e.preventDefault();
                location.reload();
            }

            $("html, body").scrollTop(0);
        })


    </script>
@endpush
