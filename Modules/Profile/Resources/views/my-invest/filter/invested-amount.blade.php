<div class="card">
    <h5 class="card-header">{{__('common.InvestedAmount')}}</h5>
    <div class="card-body">
        <input type="number" name="invested_amount[from]" class="form-control w100 mb-3"
               placeholder="From" min="1"
               value="{{ session($cacheKey . '.invested_amount.from') }}"
               step="1">
        <input type="number" name="invested_amount[to]" class="form-control w100"
               placeholder="To" min="1"
               value="{{ session($cacheKey . '.invested_amount.to') }}"
               step="1">
    </div>
    <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
        {{__('common.Clear')}}
    </a>
</div>
