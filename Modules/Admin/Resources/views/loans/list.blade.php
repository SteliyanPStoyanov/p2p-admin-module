@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="{{ assets_version(asset('css/table-style.css')) }}">
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form id="loanForm" class="form-inline card-body"
                      action="{{ route('admin.loans.list') }}"
                      method="PUT">
                    {{ admin_csrf_field() }}
                    <div class="form-row w-100">
                        <div class="form-group col-lg-2 mb-3">
                            <input type="text" autocomplete="off" name="createdAt[from]"
                                   class="form-control w-100 singleDataPicker"
                                   value="{{ session($cacheKey . '.createdAt.from') }}"
                                   placeholder="{{__('common.ListingDateFrom')}}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input type="text" autocomplete="off" name="createdAt[to]" class="form-control w-100
                            singleDataPicker"
                                   value="{{ session($cacheKey . '.createdAt.to') }}"
                                   placeholder="{{__('common.ListingDateTo')}}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <select name="originator" class="form-control w-100">
                                <option value>{{__('common.Lender')}}</option>
                                @foreach($loanOriginators as $loanOriginator)
                                    <option
                                        @if(session($cacheKey . '.originator_id') == $loanOriginator->originator_id)
                                        selected
                                        @endif
                                        value="{{$loanOriginator->originator_id}}">{{$loanOriginator->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-lg-2 mb-3">
                            <select name="country_id" class="form-control w-100">
                                <option value>{{__('common.Country')}}</option>
                                @foreach($loanCountries as $loanCountry)
                                    <option
                                        @if(session($cacheKey . '.country_id') == $loanCountry->country_id)
                                        selected
                                        @endif
                                        value="{{$loanCountry->country_id}}">{{$loanCountry->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <input name="loan_id" class="form-control w-100" type="text"
                                   placeholder="{{__('common.Id')}}"
                                   value="{{ session($cacheKey . '.loan_id') }}">
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <select name="type" class="form-control w-100">
                                <option value>{{__('common.LoanType')}}</option>
                                @foreach($loanTypes as $key=> $loanType)
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
                                   placeholder="{{__('common.LenderId')}}"
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
                                <option value>{{__('common.LoanStatus')}}</option>
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
                            <select id="paymentStatuses" name="payment_status" class="form-control w-100">
                                <option value="">{{__('common.PaymentStatus')}}</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 mb-3">
                            <select name="unlisted" class="form-control w-100">
                                <option value>{{__('common.SelectListingStatus')}}</option>
                                <option
                                    @if(session($cacheKey . '.unlisted') === 0)
                                    selected
                                    @endif
                                    value="0">{{ __('common.Listed') }}
                                </option>
                                <option
                                    @if(session($cacheKey . '.unlisted') == 1)
                                    selected
                                    @endif
                                    value="1">{{ __('common.Unlisted') }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-12 mt-4">
                        <x-btn-filter/>
                    </div>

                    {{-- multiple filter --}}
                    <select class="form-control noClear" name="limit" id="maxRows"
                            style="position: absolute; right: 321px;bottom: 25px;z-index: 10;">
                        <option class="paginationValueLimit" value="10">10</option>
                        <option class="paginationValueLimit" value="25">25</option>
                        <option class="paginationValueLimit" value="50">50</option>
                        <option class="paginationValueLimit" value="100">100</option>
                    </select>
                    {{-- multiple filter --}}
                </form>
            </div>
        </div>

    </div>
    <div class="row" id="container-row">
        <div class="col-lg-12">
            <div id="main-table" class="card">
                <div class="card-body pt-5">
                    <div class="table-responsive">
                        <table class="table" >
                            <thead>
                            @include('admin::loans.list-table-head')

                            </thead>
                            <tbody class="text-center" id="table-loans">
                            @include('admin::loans.list-table')
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/jsGrid.js')) }}"></script>

    <script>
        let picker = $('.singleDataPicker').daterangepicker({
            autoUpdateInput: false,
            "singleDatePicker": true,
            "autoApply": true,
            locale: {
                format: 'DD.MM.YYYY',
            }
        });

        let pickerTwo = $('.singleDataPicker').daterangepicker({
            autoUpdateInput: false,
            "singleDatePicker": true,
            "autoApply": true,
            locale: {
                format: 'DD.MM.YYYY',
            }
        });

        $('.singleDataPicker').on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY'));
        });

        loadSimpleDataGrid('{{ route('admin.loans.refresh') }}', $("#loanForm"), $("#table-loans"));

        $("#maxRows").change(function () {
            let routeRefreshLoan = '{{ route('admin.loans.refresh')}}';

            $.ajax({
                type: 'get',
                url: routeRefreshLoan,
                data: $('#loanForm').serialize(),
                success: function (data) {
                    $('#table-loans').html(data);
                },
            });
        });

        let statusFinish = '{{\Modules\Common\Entities\Loan::STATUS_FINISH}}';
        let finalPaymentStatuses = {!! json_encode($loanFinalPaymentStatuses) !!};
        let paymentStatuses = {!! json_encode($loanPaymentStatuses) !!}
        $("select[name=status]").on('change', function (event) {
            let paymentStatusSelect = $("#paymentStatuses");
            let statusesToFill = paymentStatuses;
            let formName = 'payment_status'
            if (this.value == statusFinish) {
                statusesToFill = finalPaymentStatuses;
                formName = 'final_payment_status';
            }

            paymentStatusSelect.empty();
            paymentStatusSelect.attr('name', formName);
            paymentStatusSelect.append('<option value="">{{__('common.PaymentStatus')}}</option>');
            $.each(statusesToFill, function (key, value) {
                paymentStatusSelect.append('<option value="' + value + '">' + value + '</option>');
            });
        });
    </script>
@endpush
