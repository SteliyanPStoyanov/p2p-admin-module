<div class="card">
    <h5 class="card-header">{{__('common.DiscountPremium')}} %</h5>
    <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
        {{__('common.Clear')}}
    </a>
    <div class="card-body">
        <input type="number" name="discount[from]"
               class="form-control w100 mb-3"
               placeholder="From" min="1"
               value="{{ session($cacheKey . '.discount.from') }}"
               step="1">
        <input type="number" name="discount[to]"
               class="form-control w100"
               value="{{ session($cacheKey . '.discount.to') }}"
               placeholder="To" min="1"
               step="1">
    </div>
</div>
