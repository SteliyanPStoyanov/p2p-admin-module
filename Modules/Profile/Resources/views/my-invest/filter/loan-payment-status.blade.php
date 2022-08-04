<div class="card">
    <h5 class="card-header">{{__('common.LoanPaymentStatus')}}</h5>
    <div id="loanStatuses">
        @include('profile::invest.filter.range')
    </div>
    <div id="loanFinalStatuses" style="display: none;">
        @include('profile::my-invest.filter.final_payment_status')
    </div>
    <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
        {{__('common.Clear')}}
    </a>
</div>
