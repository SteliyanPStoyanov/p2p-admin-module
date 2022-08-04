<div class="card">
    <h5 class="card-header">{{__('common.Term')}} (months)</h5>
    <div class="card-body">
        <div class="ui calendar mb-3">
            <div class="position-relative">
                <input type="text" name="period[from]"
                       class="form-control w100" min="1" step="1"
                       placeholder="From" value="{{ session($cacheKey . '.period.from') }}"
                >
            </div>
        </div>
        <div class="ui calendar mb-3">
            <div class="position-relative">
                <input type="text" name="period[to]" class="form-control w100" min="1"
                       step="1"
                       placeholder="To" value="{{ session($cacheKey . '.period.to') }}"
                >
            </div>
        </div>
    </div>
    <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
        {{__('common.Clear')}}
    </a>
</div>
