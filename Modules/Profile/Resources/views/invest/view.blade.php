@extends('profile::layouts.app')

@section('title',  'View loan - ')

@section('style')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css">
@endsection
@section('content')
    <div class="row" id="loan-invest">
        <div class="col-lg-12  mt-5">
            <h1>{{__('common.Loan')}} {{$loan->loan_id}} - {{__('common.InstalmentLoan')}}</h1>
        </div>
        <div class="col-lg-4  mt-5">
            <h3>Loan Details</h3>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.LoanOriginator')}}
                </div>
                <div class="col-lg-6">
                    {{$loan->originator->name}}
                </div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.LoanType')}}
                </div>
                <div class="col-lg-6">
                    {{loanType($loan->type)}}
                </div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.LoanAmount')}}
                </div>
                <div class="col-lg-6">{{ amount($loan->amount) }}</div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.RemainingPrincipal')}}
                </div>
                <div class="col-lg-6">{{ $loan->isFinished() ? amount(0) : amount($loan->remaining_principal) }}</div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.InterestRate')}}
                </div>
                <div class="col-lg-6">{{rate($loan->interest_rate_percent)}}</div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.RemainingTerm')}}
                </div>
                <div class="col-lg-6">
                    {{termFormat($loan->final_payment_date)}}
                </div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.ListingDate')}}
                </div>
                <div class="col-lg-6">
                    {{ showDate($loan->created_at)}}
                </div>
            </div>
            @if(in_array($loan->status ,\Modules\Common\Entities\Loan::getFinalStatuses()))
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.EarlyRepaymentDate')}}
                </div>
                <div class="col-lg-6">
                    {{ showDate($loan->unlisted_at)}}
                </div>
            </div>
            @endif
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.DateOfIssue')}}
                </div>
                <div class="col-lg-6">
                    {{showDate($loan->lender_issue_date)}}
                </div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.ClosingDate')}}
                </div>
                <div class="col-lg-6">
                    {{showDate($loan->final_payment_date)}}
                </div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.Status')}}
                </div>
                <div class="col-lg-6">
                    {{payStatus($loan->payment_status, $loan)}}
                </div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.Borrower')}}
                </div>
                <div class="col-lg-6">
                    {{ucfirst($loan->borrower_gender)}}, {{$loan->borrower_age}} y.
                </div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.Country')}}
                </div>
                <div class="col-lg-6">
                    {{$loan->country->name}}
                </div>
            </div>
            <div class="row pb-2 pt-2">
                <div class="col-lg-6">
                    {{__('common.AssignmentAgreement')}}
                </div>
                <div class="col-lg-6">
                    <a href="{{route('profile.invest.assignment-agreement.template', $loan->loan_id)}}"
                       target="_blank">{{__('common.Download')}}
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mt-5 pl-4 pr-4">
            <h3 class="mb-3">{{__('common.InvestmentBreakdown')}}</h3>
            <div>
                <canvas id="myChart" width="200px" height="200px" class="mr-auto mt-5"></canvas>
                <div class="cell mt-5" id="Legend"></div>
            </div>
        </div>

        <div class="col-lg-4 mt-5 ml-auto">
            <h3 class="mb-3">{{__('common.Invest')}}</h3>
            <div class="card pl-2 pr-2">
                <div class="card-body row">
                    <div class="col-lg-6 mt-2 mb-4">{!! trans('common.AvailableForInvestment')!!}</div>
                    <div class="col-lg-6 mt-2 mb-4 ml-auto text-right">{{ amount($loan->amount_available) }}

                    </div>
                    <form method="POST" class="mt-3 mb-3 w-100"
                          action="{{route('profile.invest.invest' ,$loan->loan_id)}}"
                          autocomplete="off">
                        @csrf
                        @if (session('fail'))
                            <div class="col-12 withdraw-validation-er">
                                <div
                                    class="text-left mb-2 bg-danger text-white w-100 rounded-lg">{{session('fail')}}</div>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="withdraw-validation-er">
                                <div class="text-left mb-2 bg-success text-white">{{session('success')}}</div>
                            </div>
                        @endif
                        <div class="col-12 maxAmountWallet">
                            <div
                                class="text-left mb-2 bg-danger text-white w-100 rounded-lg">
                                {{__('common.АmountIsBiggerThenUninvestedFunds')}}

                            </div>
                        </div>
                        <div class="col-12 decimalError">
                            <div
                                class="text-left mb-2 bg-danger text-white w-100 rounded-lg">
                                {{__('common.EnterValidValue')}}

                            </div>
                        </div>
                        <div class="col-12 mb-1">
                            <input type="number" name="amount" id="amount" class="form-control text-center"
                                   placeholder=""
                                   step=".01">
                        </div>

                        <div class="col-12 mt-3">
                            <input id="form_submit" class="btn ui teal button w-100 " type="submit"
                                   value="{{ __('common.Invest') }}">
                        </div>
                    </form>
                    <div class="col-12">
                        <h5 class="mt-3 text-black">{{ __('common.BuybackGuarantee') }}</h5>
                        <p class="mt-4">
                            <img src="{{ url('/') }}/images/icons/secure.svg" alt="secure-img"
                                 class="ui tiny rounded image d-inline-block w-25 float-left"
                                 style="margin-left: -.9rem">
                        <p class="d-inline-block float-left w-75"
                           style="font-size: 0.9rem; padding-top: .4rem">{{ __('common.ThisLoanComesWithABuyback') }}</p>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7 mt-2">
            <div class="col-lg-12 mt-5">
                <h3>{{ __('common.PaymentSchedule') }}</h3>
            </div>
            <div class="table-responsive" id="payment-schedule-table">
                <div id="table-invests">
                    @include('profile::invest.payment-schedule.payment-schedule-table')
                </div>
            </div>
        </div>
        <div class="col-lg-1 mt-2"></div>
        <div class="col-lg-4 mt-2">
            <div class="col-lg-12 mt-5">
                <h3>{{ __('common.MyAgreements') }}</h3>
            </div>


            <div class="row">
                @foreach($loan->contracts as $contract)
                    @if($contract->investor_id == Auth::guard('investor')->user()->investor_id)
                        @php
                            $data = json_decode($contract->data);
                        @endphp
                        <p class="text-primary">
                            <a href="{{ route('profile.invest.assignment-agreement.download', $contract->loan_contract_id) }}"
                               target="_blank">
                                {{ __('common.AgreementNo') . $data->Loan->loan_id}} -
                                {{ $data->Investor->investor_id }} -
                                {{ $data->Transaction->created_at }} -
                                {{ $data->Transaction->transaction_id }}
                            </a>
                        </p>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

    <script>
        let availableForInvestment = '{{($loan->amount_available < 0 ? '0%' : rate($loan->getAvailablePercent()))}}';
        let investorsShare = '{{(($investorsShare['percent'] + $loan->getAssignedOriginationFeePercent()) > 100 ? rate(90) : rate($investorsShare['percent']))}}';
        let myShare = '{{rate($myLoanShare['percent'])}}';
        let Stikcredit = '{{amount($loan->remaining_principal / $loan->getAssignedOriginationFeePercent()) }}';
        let StikcreditPrecent = '{{rate($loan->getAssignedOriginationFeePercent())}}';

        let data = {
            datasets: [{
                data: [availableForInvestment.replace(' %', ''), StikcreditPrecent.replace(' %', ''), investorsShare.replace(' %', ''), myShare.replace(' %', '')],
                "backgroundColor": ["#D9D9D9", "#F2F2F2", "#59B7B9", "#199C9E"]
            }],
            labels: [
                'Available for investment - ' + availableForInvestment + ' / {{($loan->amount_available < 0 ? amount(0) : amount($loan->amount_available))}}',
                'Stikcredit skin in the game ' + StikcreditPrecent + ' / ' + Stikcredit,
                '{{$investorsShare['count']}} Investor shares - ' + investorsShare + ' / {{amount($investorsShare['share'])}}',
                'Your investment share - ' + myShare + ' / {{amount($myLoanShare['share'])}}'
            ]
        };
        let options = {
            responsive: true,
            maintainAspectRatio: true,
            elements: {
                arc: {
                    borderWidth: 0
                }
            },
            tooltips: {
                enabled: false,
            },
            legend: {
                display: false,
                position: 'bottom',
                labels: {
                    fontColor: '#212529'
                },
                align: 'left'
            },
        };
        let ctx = $('#myChart');
        let myDoughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: options
        });
        $("#Legend").html(myDoughnutChart.generateLegend());

        let maxAmountAvailable = {{$loan->amount_available}};
        let maxUninvestedAmount = {{$investor->wallet()->uninvested}};
        let maxAmountАvailableError = $('.maxAmountАvailable');
        let maxAmountWallet = $('.maxAmountWallet');

        maxAmountАvailableError.hide();
        maxAmountWallet.hide();

        $('#amount').keyup(function () {
            let inputVal = $(this).val();
            checkDecimal($(this), '#amount');
            if (inputVal > maxAmountAvailable) {
                maxAmountАvailableError.show();
                $('.withdraw-validation-er').remove();
            } else {
                maxAmountАvailableError.hide();
            }
            if (maxAmountAvailable > maxUninvestedAmount && inputVal > maxUninvestedAmount) {
                maxAmountWallet.show();
                $('.withdraw-validation-er').remove();
            } else {
                maxAmountWallet.hide();
            }
        });
        $('.decimalError').hide();

        function checkDecimal(el, className) {
            let amount = el.val();
            if (amount.toString().split(".")[1] && amount.toString().split(".")[1].length > 2) {
                $('.decimalError').show();
                $('.withdraw-validation-er').remove();
                $(className).val(Number(amount).toFixed(2));
                setTimeout(function () {
                    $('.decimalError').hide();
                }, 1500);
                return false;
            }
        }

    </script>
@endpush
