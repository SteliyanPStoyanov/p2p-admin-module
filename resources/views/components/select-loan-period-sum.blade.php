@if(count($administrator->offices) == 0)
    <p>{{__('common.ChooseProduct')}}</p>

    {!! Form::select('product_id', $administrator->offices->last()->products->pluck('name','product_id'),
        null, ['class' => 'form-control', 'id' => 'product']) !!}
    <br>
@else
    <p>{{__('table.OfficeName')}}</p>
    <select class="form-control" name="loan[office_id]" id="offices">
        <option>{{__('common.ChooseOffice')}}</option>
        @foreach($administrator->offices as $office)
            <option
                @if($selectedId == $office->office_id) selected @endif
            value="{{$office->office_id}}">{{$office->name}} </option>
        @endforeach
    </select>
    <br>
    <input type="hidden" id="countOffices"
           value="{{count($administrator->offices)}}"/>
    <div class="nav nav-tabs" id="nav-tab" role="tablist"></div>
@endif
<div id="loan-products-warning"></div>
<div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active p-3" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
        <div class="form-group">
            <label class="w-100" for="salaryPeriod"> {{ __('common.AmountApproved')}}
                <span class="float-right">
                <span class="font-weight-bold text-primary ml-2 mt-1 paymentsSumText" id="payment_sum"> 150 </span>
                {{ __('common.Levs') }}</span></label>

            <input type="range" class="form-control-range"
                   id="paymentsSum" step="10" min="100" max="800">
            <input type="number" class="form-control"
                   id="loan_sum" placeholder="{{ __('common.AmountApproved') }}"
                   name="loan[loan_sum]" max="800">
            <input type="hidden" id="loan_product_id" name="loan[product_id]"/>


            <label class="w-100" for="paymentsPeriod">{{ __('common.PeriodApproved') }}
                <span class="float-right">
                <span class="font-weight-bold text-primary ml-2 mt-1 paymentsPeriodText" id="loanPeriod"> 8 </span>
                <span id="periodLabel">{{ __('common.Days') }}</span>
                </span></label>

            <input type="range" class="form-control-range"
                   id="paymentsPeriod" min="1" max="30">
            <input type="number" class="form-control"
                   id="loan_period" placeholder="{{ __('table.PeriodApproved') }}"
                   name="loan[loan_period]" min="1" max="30">

            <div id="toPayments">
                <p id="toPaymentsSum" class="card-text"><strong>{{__('head::clientCard.amount')}}
                        : </strong><span></span></p>
                <p id="toPaymentsTime" class="card-text"><strong>{{__('head::clientCard.period')}}
                        : </strong><span></span></p>
                <p id="toPaymentsPayment" class="card-text"><strong>{{__('head::clientCard.installments')}}
                        : </strong><span></span></p>
                <p id="toPaymentsInterest" class="card-text"><strong>{{__('head::clientCard.interest')}}
                        : </strong><span></span></p>
                <p id="toPaymentsPenalty" class="card-text"><strong>{{__('head::clientCard.penalty')}}
                        : </strong><span></span></p>
            </div>
        </div>
    </div>
</div>
