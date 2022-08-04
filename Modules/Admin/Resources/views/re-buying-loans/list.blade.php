@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="loanForm" class="form-inline card-body"
                      action="{{ route('admin.re-buying-loans.list') }}"
                      method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2 mb-3">
                            <select name="country_id" class="form-control w-100">
                                <option value>{{__('common.SelectCountry')}}</option>
                                @foreach($countries as $country)
                                    <option
                                        @if(session($cacheKey . '.country_id') == $country)
                                        selected
                                        @endif
                                        value="{{$country->country_id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input name="loan_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterById')}}"
                                   value="{{ session($cacheKey . '.loan_id') }}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <select name="type" class="form-control w-100">
                                <option value>{{__('common.SelectLoanType')}}</option>
                                @foreach($loanTypes as $loanType)
                                    <option
                                        @if(session($cacheKey . '.type') == $loanType)
                                        selected
                                        @endif
                                        value="{{loanType($loanType,true)}}">{{$loanType}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-2 mb-3">
                            <input name="lender_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.FilterByLenderId')}}"
                                   value="{{ session($cacheKey . '.lender_id') }}">
                        </div>

                        <div class="form-group col-lg-2 mb-3">
                            <input type="number" autocomplete="off" name="interest_rate_percent[from]"
                                   class="form-control w-100"
                                   value="{{ session($cacheKey . '.interest_rate_percent.from') }}"
                                   placeholder="{{__('common.InterestRateFrom')}}">
                        </div>

                        <div class="form-group col-lg-2 mb-3">
                            <input type="number" autocomplete="off" name="interest_rate_percent[to]"
                                   class="form-control w-100"
                                   value="{{ session($cacheKey . '.interest_rate_percent.to') }}"
                                   placeholder="{{__('common.InterestRateTo')}}">
                        </div>

                        <div class="form-group col-lg-2 mb-3">
                            <input type="number" autocomplete="off" name="period[from]" class="form-control w-100"
                                   value="{{ session($cacheKey . '.period.from') }}"
                                   placeholder="{{__('common.LoanTermFrom')}}">
                        </div>

                        <div class="form-group col-lg-2 mb-3">
                            <input type="number" autocomplete="off" name="period[to]" class="form-control w-100"
                                   value="{{ session($cacheKey . '.period.to') }}"
                                   placeholder="{{__('common.LoanTermTo')}}">
                        </div>

                        <div class="form-group col-lg-2 mb-3">
                            <select name="status" class="form-control w-100">
                                <option value>{{__('common.SelectLoanStatus')}}</option>
                                @foreach($loanStatuses as $loanStatus)
                                    <option
                                        @if(session($cacheKey . '.status') == $loanStatus)
                                        selected
                                        @endif
                                        value="{{$loanStatus}}">{{$loanStatus}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <select name="payment_status" class="form-control w-100">
                                <option value>{{__('common.SelectPaymentStatus')}}</option>
                                @foreach($loanPaymentStatuses as $loanPaymentStatus)
                                    <option
                                        @if(session($cacheKey . '.payment_status') == $loanPaymentStatus)
                                        selected
                                        @endif
                                        value="{{$loanPaymentStatus}}">{{$loanPaymentStatus}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-4">
                        <x-btn-filter/>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="table-loans">
                            @include('admin::re-buying-loans.list-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>
    <script>
        loadSimpleDataGrid('{{ route('admin.re-buying-loans.refresh') }}', $("#loanForm"), $("#table-loans"));
    </script>
@endpush
