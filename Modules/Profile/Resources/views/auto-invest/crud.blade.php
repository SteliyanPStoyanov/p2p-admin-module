@extends('profile::layouts.app')

@section('title',  'Create strategy - ')

@section('style')
    <link rel="stylesheet"
          href="{{ assets_version(url('/') . '/css/bootstrap-select.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/account-statement.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/invest-styles.css') }}">
    <link rel="stylesheet" href="{{ assets_version(url('/') . '/css/auto-invest-styles.css') }}">

@endsection
@section('content')
    <div class="row">
        <div id="formContent" class="w-100 auto-invest-crud" style="max-width: 100%;">
            <div class="row mt-5 mb-5">
                <h2 class="text-left text-black">{{__('common.CreateNewStrategy')}}</h2>
                <p class="d-block w-100 text-black">(<span class="loan-live-number"></span>) {{__('common.LoansMatchYourCriteria')}}</p>
            </div>
            @if (session('fail'))
                <div class="col-12">
                    <div class="p-1 my-4 bg-danger text-left">{{session('fail')}}</div>
                </div>
            @endif
            <form id="formContentSubmit" method="POST" class="row w-100 mx-auto" action="{{
                        !empty($investStrategy) ?
                    route('profile.autoInvest.update', $investStrategy->invest_strategy_id)
                    : route('profile.autoInvest.store')
                    }}"
                  autocomplete="off">
                @csrf
                <div class="col-lg-4 text-center text-black trans-details">
                    <div class="form-group w-100 mt-3 row text-left mb-3">
                        <label class="col-12">{{ __('common.StrategyName') }}</label>
                        <div class="col">
                            <input type="text" name="name" value="{{ $investStrategy->name ?? old('name')}}"
                                   class="form-control form-input-h40" maxlength="50">
                        </div>
                    </div>
                    @if(!empty($errors) && $errors->has('name'))
                        <div class="row">
                            <div class="text-left pl-1 mb-1 bg-danger text-white">{{$errors->first('name')}}</div>
                        </div>
                    @endif
                    <div class="form-group w-100 mt-3 row text-left mb-3">
                        <label class="col-12">{{ __('common.MaxInvestmentInOneLoan') }}
                            <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                               data-original-title="{{ __('common.MaxInvestmentInOneLoanTooltipText') }}"></i>
                        </label>
                        <div class="col">
                            <input id="min_amount" type="text" name="min_amount"
                                   value="{{$investStrategy->min_amount ?? (old('min_amount') ?? config('profile.min_amount'))}}"
                                   class="form-control form-input-h40 input-action-val" placeholder="€ {{config('profile.min_amount')}}"
                            >

                        </div>
                        <div class="ml-2 mr-2 pt-3">-</div>
                        <div class="col">
                            <input type="text" name="max_amount"
                                   value="{{$investStrategy->max_amount ?? old('max_amount')}}"
                                   class="form-control form-input-h40">
                        </div>

                    </div>
                    @if(!empty($errors) && $errors->has('min_amount'))
                        <div class="row">
                            <div class="text-left mb-1 bg-danger text-white">{{$errors->first('min_amount')}}</div>
                        </div>
                    @endif
                    @if(!empty($errors) && $errors->has('max_amount'))
                        <div class="row">
                            <div class="text-left mb-1 bg-danger text-white">{{$errors->first('max_amount')}}</div>
                        </div>
                    @endif
                    <div class="form-group w-100 mt-3 row text-left mb-3">
                        <label class="col-12">{{ __('common.InterestRate') }}
                            <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                               data-original-title="{{ __('common.InterestRateTooltipText') }}"></i>
                        </label>
                        <div class="col">
                            <input type="text" name="min_interest_rate"
                                   value="{{$investStrategy->min_interest_rate ?? old('min_interest_rate')}}"
                                   class="form-control form-input-h40 input-action-val">

                        </div>
                        <div class="ml-2 mr-2 pt-3">-</div>
                        <div class="col">
                            <input type="text" name="max_interest_rate"
                                   value="{{$investStrategy->max_interest_rate ?? old('max_interest_rate')}}"
                                   class="form-control form-input-h40 input-action-val">
                        </div>

                    </div>
                    @if(!empty($errors) && $errors->has('min_interest_rate'))
                        <div class="row">
                            <div class="text-left mb-1 bg-danger text-white">{{$errors->first('min_interest_rate')}}</div>
                        </div>
                    @endif
                    @if(!empty($errors) && $errors->has('max_interest_rate'))
                        <div class="row">
                            <div class="text-left mb-1 bg-danger text-white">{{$errors->first('max_interest_rate')}}</div>
                        </div>
                    @endif
                    <div class="form-group w-100 mt-3 row text-left mb-3">
                        <label class="col-12">{{ __('common.RemainingLoanTermMonths') }}</label>
                        <div class="col">
                            <input type="text" name="min_loan_period"
                                   value="{{$investStrategy->min_loan_period ?? old('min_loan_period')}}"
                                   class="form-control form-input-h40 input-action-val">

                        </div>
                        <div class="ml-2 mr-2 pt-3">-</div>
                        <div class="col">
                            <input type="text" name="max_loan_period"
                                   value="{{$investStrategy->max_loan_period ?? old('max_loan_period')}}"
                                   class="form-control form-input-h40 input-action-val">
                        </div>

                    </div>
                    @if(!empty($errors) && $errors->has('min_loan_period'))
                        <div class="row">
                            <div class="text-left mb-1 bg-danger text-white">{{$errors->first('min_loan_period')}}</div>
                        </div>
                    @endif
                    @if(!empty($errors) && $errors->has('max_loan_period'))
                        <div class="row">
                            <div class="text-left mb-1 bg-danger text-white">{{$errors->first('max_loan_period')}}</div>
                        </div>
                    @endif

                </div>
                <div class="col-lg-4 text-center text-black ml-5 trans-details">
                    <div class="form-group w-100 mt-3 row text-left mb-3">
                        <label class="col-12">{{ __('common.PortfolioSize') }}
                            <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                               data-original-title="{{ __('common.PortfolioSizeTooltipText') }}"></i>
                        </label>
                        <div class="col">
                            <input type="text" name="max_portfolio_size"
                                   value="{{$investStrategy->max_portfolio_size ?? old('max_portfolio_size')}}"
                                   class="form-control form-input-h40">
                        </div>
                    </div>
                    @if(!empty($errors) && $errors->has('max_portfolio_size'))
                        <div class="row">
                            <div class="text-left mb-1 bg-danger text-white">{{$errors->first('max_portfolio_size')}}</div>
                        </div>
                    @endif

                    <div class="form-group w-100 row text-left my-3">
                        <div class="col-12 loan_type_wrap">
                            <label for="loan_type">{{ __('common.LoanType') }}</label>
                            <select id="loan_type" name="loan_type[]" class="select-dropdown input-action-val"
                                    data-actions-box="true" data-selected-text-format="count>0"
                                    data-count-selected-text="({0}/{{count($types)}}) selected" multiple>
                                @foreach($types as $type)
                                    <option
                                        @if(  !empty($investStrategy) &&
                                            isset(json_decode($investStrategy->loan_type ,true)['type']) &&
                                            in_array($type,json_decode($investStrategy->loan_type ,true)['type'])
                                            )
                                        selected
                                        @endif
                                        value="{{$type}}">{{loanType($type,false)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group w-100 row text-left my-3">
                        <div class="col-12 loan_payment_status_wrap">
                            <label for="loan_payment_status">{{ __('common.LoanStatus') }}</label>
                            <select id="loan_payment_status" name="loan_payment_status[]"
                                    class="select-dropdown input-action-val"
                                    data-actions-box="true" data-selected-text-format="count>0"
                                    data-count-selected-text="({0}/{{count($paymentStatuses)-1}}) selected" multiple>
                                @foreach($paymentStatuses as $paymentStatus)
                                    @if(!$loop->last)
                                        <option
                                            @if(  !empty($investStrategy) &&
                                                isset(json_decode($investStrategy->loan_payment_status ,true)['payment_status']) &&
                                                in_array($paymentStatus,json_decode($investStrategy->loan_payment_status,true)['payment_status'])
                                                )
                                            selected
                                            @endif
                                            value="{{$paymentStatus}}">{{payStatus($paymentStatus)}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="text-left">
                        <p class="mt-2 d-block w-100 text-black">
                            {{ __('common.ReinvestReceivedRepayments') }}
                            <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                               data-original-title="{{ __('common.ReinvestReceivedPaymentsTooltipText') }}"></i>
                        </p>
                        <div class="ui radio checkbox">
                            <input class="hidden" type="radio"
                                   name="reinvest" id="reinvest_yes"
                                   @if(isset($investStrategy->reinvest) && $investStrategy->reinvest === 1)
                                   checked
                                   @else
                                   checked
                                   @endif
                                   value="1">

                            <label class="mr-3" for="reinvest_yes">
                                {{ __('common.Yes') }}
                            </label>
                        </div>

                        <div class="ui radio checkbox mt-1">
                            <input class="hidden" type="radio"
                                   name="reinvest" id="reinvest_no"
                                   @if(isset($investStrategy->reinvest) && $investStrategy->reinvest === 0)
                                   checked
                                   @endif
                                   value="0">
                            <label class="mr-3" for="reinvest_no">
                                {{ __('common.No') }}
                            </label>
                        </div>
                    </div>

                    <div class="text-left">
                        <p class="mt-2 d-block w-100 text-black">
                            {{ __('common.IncludeInvestedLoans') }}
                            <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                               data-original-title="{{ __('common.IncludeInvestedLoansTooltipText') }}"></i>
                        </p>
                        <div class="ui radio checkbox">
                            <input class="hidden input-action-val get-radio-val" type="radio"
                                   name="include_invested" id="include_invested_yes"
                                   @if(isset($investStrategy->include_invested)  && $investStrategy->include_invested === 1)
                                   checked
                                   @else
                                   checked
                                   @endif
                                   value="1">
                            <label class="mr-3" for="include_invested_yes">
                                {{ __('common.Yes') }}
                            </label>
                        </div>

                        <div class="ui radio checkbox mt-1">
                            <input class="hidden input-action-val get-radio-val" type="radio"
                                   name="include_invested" id="include_invested_no"
                                   @if(isset($investStrategy->include_invested) && $investStrategy->include_invested === 0)
                                   checked
                                   @endif
                                   value="0">
                            <label class="mr-3" for="include_invested_no">
                                {{ __('common.No') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group w-100 mt-3 row text-left mb-3">
                    <div class="ui checkbox" style="margin-left: .2rem">
                        <input class="hidden" value="1"
                               @if(!empty($investStrategy->agreed) && $investStrategy->agreed === 1)
                               checked
                               @elseif(old('agreed') == 1)
                               checked
                               @endif
                               type="checkbox" name="agreed" id="assignment_agreements">
                        <label class="form-check-label mr-3" for="assignment_agreements">
                            {{ __('common.IHaveReadAndAcceptThe') }} <a href="{{ url('/') }}/assignment-agreement"
                                                                        class="blue-color"
                                                                        target="_blank">
                                {{ __('common.AssignmentAgreements') }}</a>
                        </label>
                    </div>
                </div>
                @if(!empty($errors) && $errors->has('agreed'))
                    <div class="row d-block w-100">
                        <div class="w-75 pl-1 text-left mb-1 bg-danger text-white">{{$errors->first('agreed')}}</div>
                    </div>
                @endif
                <input type="submit" value="{{ __('common.SaveAndActivate') }}"
                       class="ui teal button btn float-right mr-3 ">
                <button class="btn btn-link btn-reset float-right blue-color"
                >{{ __('common.ResetFilters') }}
                </button>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript" src="{{ assets_version(asset('js/bootstrap-select.js')) }}"></script>
    <script>
        $(function () {
            $(".select-dropdown").selectpicker({
                showTick: true,
            });
        });

        $('[data-toggle="tooltip"]').tooltip({container: 'body'});

        loansCount();

        $('.input-action-val').keyup(function () {
            loansCount();
        });
        $('.get-radio-val').change(function () {
            loansCount();
        });

        $(".select-dropdown").on("changed.bs.select",
            function (e, clickedIndex, newValue, oldValue) {
                loansCount();
            });

        $('#min_amount').keyup(function () {
            if ($(this).val() < {{config('profile.min_amount')}}) {
                $(this).parent().append('<div class="tooltip-success-form" style="bottom:63px;">{{__('common.MinAmountStrategy')}}</div>');
                setTimeout(function () {
                    $('.tooltip-success-form').remove();
                }, 1000);
            }
        });

        $(".select-dropdown").on("loaded.bs.select",
            function (e, clickedIndex, newValue, oldValue) {
                clearSelect();
            });


        $('.btn-link.btn-reset').click(function (e) {
            e.preventDefault();
            clearForms();
            loansCount();
        });

        function clearForms() {
            $(':input').not(':button, :submit, :reset, :hidden, :checkbox, :radio').val('');
            $(':checkbox, :radio').prop('checked', false);
            $('#reinvest_yes').prop('checked', true);
            $('#include_invested_yes').prop('checked', true);
            clearSelect();
        }

        function clearSelect() {

            $("#loan_type").selectpicker("refresh");
            $("#loan_payment_status").selectpicker("refresh");

            $(".loan_type_wrap").find(".dropdown-menu.inner li").each(function (index) {
                $(this).find(".check-mark").not(':first').remove();
            });
            $(".loan_payment_status_wrap").find(".dropdown-menu.inner li").each(function (index) {
                $(this).find(".check-mark").not(':first').remove();
            });
        }

        function loansCount() {
            let data = $('#formContentSubmit .input-action-val').serialize();
            $.ajax({
                type: 'get',
                url: '{{route('profile.autoInvest.loanCount')}}',
                data: data,
                success: function (data) {
                    $('.loan-live-number').html(data.count);
                },
            });
        }

    </script>
@endpush
