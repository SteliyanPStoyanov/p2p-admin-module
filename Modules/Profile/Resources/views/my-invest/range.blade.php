<div class="card-body portfolio-ranges-wrapper">
    @php
        $portfolioEntity = \Modules\Common\Entities\Portfolio::class;
        $range1 = $portfolioEntity::PORTFOLIO_RANGE_1;
        $range2 = $portfolioEntity::PORTFOLIO_RANGE_2;
        $range3 = $portfolioEntity::PORTFOLIO_RANGE_3;
        $range4 = $portfolioEntity::PORTFOLIO_RANGE_4;
        $range5 = $portfolioEntity::PORTFOLIO_RANGE_5;
    @endphp
    <div class="ui checkbox">
        <input class="hidden" value="{{$range1}}"
               type="checkbox"
               name="payment_status[{{$range1}}]"
               @if(session($cacheKey . '.payment_status.'.$range1) == $range1)
               checked
               @endif
               id="payment_status1">
        <label class="form-check-label " for="payment_status1">
            {{payStatus($portfolioEntity::getQualityMapping($range1))}}
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
        <label class="form-check-label " for="payment_status2">
            {{payStatus($portfolioEntity::getQualityMapping($range2))}}
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
        <label class="form-check-label " for="payment_status3">
            {{payStatus($portfolioEntity::getQualityMapping($range3))}}
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
        <label class="form-check-label " for="payment_status4">
            {{payStatus($portfolioEntity::getQualityMapping($range4))}}
        </label>
    </div>
    <div class=" mt-3 ui checkbox">
        <input class="hidden"
               value="{{$range5}}"
               type="checkbox"
               name="payment_status[{{$range5}}]"
               @if(session($cacheKey . '.payment_status.'.$range5) == $range5)
               checked
               @endif
               id="payment_status5">
        <label class="form-check-label " for="payment_status5">
            {{payStatus($portfolioEntity::getQualityMapping($range5))}}
        </label>
    </div>
</div>
