<div class="container mw-100 loan-details-admin p-0">
    <div class="row">
        <div class="col-lg-6 col-xl-3 col-md-12 col-sm-12 d-inline-block float-left ld-col-1">
            <div class="card">
                <h3 class="card-header pt-4 pl-4"><b>{{__('common.LoanDetails')}}</b></h3>
                <div class="card-body">
                    <p class="card-text">
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.LoanOriginatorId')}}</strong>:</span>
                        <span class="card-content">{{$loan->contract_id}}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.LoanOriginator')}}</strong>:</span>
                        <span class="card-content">{{$loan->originator->name}}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.Country')}}</strong>:</span>
                        <span class="card-content">{{$loan->country->name}}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.LoanType')}}</strong>:</span>
                        <span class="card-content">{{loanType($loan->type)}}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.Currency')}}</strong>:</span>
                        <span class="card-content">{{$loan->currency->code}}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.Buyback')}}</strong>:</span>
                        <span class="card-content">{{ $loan->buyback ? __('common.Yes') : __('common.No') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.OriginalPrincipal')}}</strong>:</span>
                        <span class="card-content">{{ $loan->amount }}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.OutstandingPrincipal')}}</strong>:</span>
                        <span
                            class="card-content">{{ $loan->isFinished() ? amount(0) : $loan->remaining_principal }}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.InterestRate')}}</strong>:</span>
                        <span class="card-content">{{ $loan->interest_rate_percent }}%</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.Term')}}</strong>:</span>
                        <span class="card-content">{{ $loan->period }}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.ListingDate')}}</strong>:</span>
                        <span class="card-content">{{ showDate($loan->created_at)}}</span>
                    </p>
                    @if(in_array($loan->status ,\Modules\Common\Entities\Loan::getFinalStatuses()))
                        <p class="card-text">
                            <span class="card-title-main"><strong>{{__('common.EarlyRepaymentDate')}}</strong>:</span>
                            <span class="card-content">{{ showDate($loan->unlisted_at)}}</span>
                        </p>

                    @endif
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.DateOfIssue')}}</strong>:</span>
                        <span class="card-content">{{ showDate($loan->lender_issue_date) }}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.ClosingDate')}}</strong>:</span>
                        <span class="card-content">{{ showDate($loan->final_payment_date) }}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.PaymentStatus')}}</strong>:</span>
                        <span class="card-content">{{ payStatus($loan->payment_status, $loan) }}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.ListingStatus')}}</strong>:</span>
                        <span
                            class="card-content">{{ $loan->unlisted ? __('common.Unlisted') : __('common.Listed') }}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.Status')}}</strong>:</span>
                        <span class="card-content">{{ loanStatus($loan->status) }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-9 col-md-12 col-sm-12 d-inline-block float-left ld-col-2">
            <div class="card">
                <h3 class="card-header pt-4 pl-4"><b>{{__('common.InvestmentBreakdown')}}</b></h3>
                <div class="card-body">
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.LenderSkin')}}</strong>:</span>
                        <span
                            class="card-content">{{ rate($loan->getAssignedOriginationFeePercent()) }} / {{ amount($loan->getLenderSkin()) }}</span>
                    </p>
                    <p class="card-text">
                        <span class="card-title-main"><strong>{{__('common.AvailableForInvest')}}</strong>:</span>
                        <span
                            class="card-content"> {{ rate($loan->getAvailablePercentForInvest()) }} / {{ amount($loan->amount_available) }}</span>
                    </p>

                    <p class="card-text">
                        <span
                            class="card-title-main"><strong>{{ $loan->getInvestorSharedCount() . ' ' . __('common.InvestorShares')}}</strong>:</span>
                        <span
                            class="card-content"> {{ rate($investorsShare['percent']) }} / {{ amount($investorsShare['share']) }}</span>
                    </p>
                </div>
            </div>
            <div class="card">
                <h3 class="card-header pt-4 pl-4"><b>{{__('common.RepaymentInformation')}}</b></h3>
                <div class="card-body">
                    <p class="card-text">

                    @if(empty($loanRepayments))
                        <p class="card-text">
                            <span class="card-title-main"><strong>{{__('common.RepaidPrincipalToInvestors')}}</strong>:</span>
                            <span class="card-content">&euro; 0</span>
                        </p>

                        <p class="card-text"><span
                                class="card-title-main"><strong>{{__('common.RepaidInterestToInvestors')}}</strong>:</span>
                            <span class="card-content">&euro; 0</span>

                        </p>
                        <p class="card-text"><span
                                class="card-title-main"><strong>{{__('common.RepaidLatePaymentFee')}}</strong>:</span>
                            <span class="card-content">&euro; 0</span>
                        </p>
                    @else
                        @foreach($loanRepayments as $loanRepayment)

                            <p class="card-text"><span
                                    class="card-title-main"><strong>{{__('common.RepaidPrincipalToInvestors')}}</strong>:</span>
                                <span class="card-content">&euro; {{$loanRepayment->repaid_princ}}</span>
                            </p>

                            <p class="card-text"><span
                                    class="card-title-main"><strong>{{__('common.RepaidInterestToInvestors')}}</strong>:</span>
                                <span class="card-content">&euro; {{$loanRepayment->repaid_interest}}</span>

                            </p>
                            <p class="card-text"><span
                                    class="card-title-main"><strong>{{__('common.RepaidLatePaymentFee')}}</strong>:</span>
                                <span class="card-content">&euro; {{$loanRepayment->late_interes}}</span>
                            </p>
                        @endforeach
                    @endif


                    @if(empty($loanAccrueds))
                        <p class="card-text">
                                <span
                                    class="card-title-main"><strong>{{__('common.RepaidPrincipalToInvestors')}}</strong>:</span>
                            <span class="card-content">&euro; 0</span>
                        </p>

                        <p class="card-text">
                            <span
                                class="card-title-main"><strong>{{__('common.RepaidInterestToInvestors')}}</strong>:</span>
                            <span class="card-content">&euro; 0</span>

                        </p>
                        <p class="card-text">
                                <span
                                    class="card-title-main"><strong>{{__('common.RepaidLatePaymentFee')}}</strong>:</span>
                            <span class="card-content">&euro; 0</span>
                        </p>
                        <p class="card-text">
                            <span class="card-title-main"><strong>{{__('common.AccruedPrincipal')}}</strong>:</span>
                            <span class="card-content">&euro; 0</span>

                        </p>
                        <p class="card-text">
                            <span class="card-title-main"><strong>{{__('common.AccruedInterest')}}</strong>:</span>
                            <span class="card-content">&euro; 0</span>

                        </p>
                        <p class="card-text">
                            <span
                                class="card-title-main"><strong>{{__('common.AccruedLatePaymentFee')}}</strong>:</span>
                            <span class="card-content">&euro; 0</span>

                        </p>
                    @else

                        <p class="card-text">
                            <span class="card-title-main"><strong>{{__('common.AccruedPrincipal')}}</strong>:</span>
                            <span class="card-content">{{ amount($loanAccrueds->accrued_principal) }}</span>

                        </p>
                        <p class="card-text">
                            <span class="card-title-main"><strong>{{__('common.AccruedInterest')}}</strong>:</span>
                            <span class="card-content">{{ amount($loanAccrueds->accrued_interes) }}</span>

                        </p>
                        <p class="card-text">
                            <span
                                class="card-title-main"><strong>{{__('common.AccruedLatePaymentFee')}}</strong>:</span>
                            <span class="card-content">{{amount($loanAccrueds->late_payment_fee) }}</span>
                        </p>
                        @endif
                        </p>
                </div>
            </div>

            <div class="card" id="PaymentScheduleAdmin">
                <h3 class="card-header p-4"><b>{{__('common.PaymentSchedule')}}</b></h3>
                <div class="table-responsive p-2 mt-4">
                    <div id="table-invests">
                        @include('profile::invest.payment-schedule.payment-schedule-table')
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
