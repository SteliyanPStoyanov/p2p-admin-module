<div class="card-body">
    @php
        $range1 = \Modules\Common\Entities\Portfolio::PORTFOLIO_RANGE_1;
        $range2 = \Modules\Common\Entities\Portfolio::PORTFOLIO_RANGE_2;
        $range3 = \Modules\Common\Entities\Portfolio::PORTFOLIO_RANGE_3;
        $range4 = \Modules\Common\Entities\Portfolio::PORTFOLIO_RANGE_4;
    @endphp
    <div class="ui checkbox">
        <input class="hidden" value="{{$range1}}"
               type="checkbox"
               name="payment_status[{{$range1}}]"
               @if(session($cacheKey . '.payment_status.'.$range1) == $range1)
               checked
               @endif
               id="payment_status1">
        <label class="form-check-label mr-3" for="payment_status1">
            {{payStatus(\Modules\Common\Entities\Portfolio::getQualityMapping($range1))}}
        </label>
    </div>
    <div class="mt-3 ui checkbox">
        <input class="hidden"
               value="{{$range2}}"
               type="checkbox"
               name="payment_status[{{$range2}}]"
               @if(session($cacheKey . '.payment_status.'.$range2) == $range2)
               checked
               @endif
               id="payment_status2">
        <label class="form-check-label mr-3" for="payment_status2">
            {{payStatus(\Modules\Common\Entities\Portfolio::getQualityMapping($range2))}}
        </label>
    </div>
    <div class="mt-3 ui checkbox">
        <input class="hidden"
               value="{{$range3}}"
               type="checkbox"
               name="payment_status[{{$range3}}]"
               @if(session($cacheKey . '.payment_status.'.$range3) == $range3)
               checked
               @endif
               id="payment_status3">
        <label class="form-check-label mr-3" for="payment_status3">
            {{payStatus(\Modules\Common\Entities\Portfolio::getQualityMapping($range3))}}
        </label>
    </div>
    <div class=" mt-3 ui checkbox">
        <input class="hidden"
               value="{{$range4}}"
               type="checkbox"
               name="payment_status[{{$range4}}]"
               @if(session($cacheKey . '.payment_status.'.$range4) == $range4)
               checked
               @endif
               id="payment_status4">
        <label class="form-check-label mr-3" for="payment_status4">
            {{payStatus(\Modules\Common\Entities\Portfolio::getQualityMapping($range4))}}
        </label>
    </div>
</div>
