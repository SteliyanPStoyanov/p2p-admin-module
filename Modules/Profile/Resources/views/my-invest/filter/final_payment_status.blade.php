<div class="card-body">
    @php
        $finalPaymentStatuses = \Modules\Common\Entities\Loan::getFinalPaymentStatuses();
    @endphp
    @foreach($finalPaymentStatuses as $finalPaymentStatus)
        <div class="mt-3 ui checkbox">
            <input class="hidden" value="{{$finalPaymentStatus}}"
                   type="checkbox"
                   name="final_payment_status[]"
                   @if(session($cacheKey . '.final_payment_status.'.$finalPaymentStatus) == $finalPaymentStatus)
                   checked
                   @endif
                   id="{{$finalPaymentStatus}}">
            <label class="form-check-label mr-3" for="{{$finalPaymentStatus}}">
                {{ ucfirst($finalPaymentStatus) }}
            </label>
        </div>
    @endforeach
</div>
