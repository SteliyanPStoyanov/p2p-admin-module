<div class="card">
    <h5 class="card-header">{{__('common.InterestRate')}} %</h5>
    <div class="card-body">
        <input type="number" name="interest_rate_percent[from]"
               class="form-control w100 mb-3"
               placeholder="From" min="1"
               value="{{ session($cacheKey . '.interest_rate_percent.from') }}"
               step="1">
        <input type="number" name="interest_rate_percent[to]" class="form-control w100"
               value="{{ session($cacheKey . '.interest_rate_percent.to') }}"
               placeholder="To" min="1"
               step="1">
    </div>
    <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
        {{__('common.Clear')}}
    </a>
</div>
