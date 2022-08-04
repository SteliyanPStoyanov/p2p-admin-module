<div class="card">
    <h5 class="card-header">{{__('common.AmountAvailable')}}</h5>
    <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
        {{__('common.Clear')}}
    </a>
    <div class="card-body">
        <input type="number" name="amount_available[from]"
               class="form-control w100 mb-3"
               placeholder="From" min="1"
               value="{{ session($cacheKey . '.amount_available.from') }}"
               step="1">
        <input type="number" name="amount_available[to]" class="form-control w100"
               placeholder="To" min="1"
               value="{{ session($cacheKey . '.amount_available.to') }}"
               step="1">
    </div>
</div>
