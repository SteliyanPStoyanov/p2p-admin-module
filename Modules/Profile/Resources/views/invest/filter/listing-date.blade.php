<div class="card">
    <h5 class="card-header">{{__('common.ListingDate')}}</h5>
    <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
        {{__('common.Clear')}}
    </a>
    <div class="card-body">
        <div class="ui calendar mb-3" id="createdFromDatepicker">
            <div class="position-relative">
                <input type="text" name="created_at[from]"
                       class="form-control w100"
                       placeholder="From"
                       value="{{session($cacheKey . '.created_at.from') }}"
                >
                <i class="fa fa-calendar"></i>
            </div>
        </div>
        <div class="ui calendar mb-3" id="createdToDatepicker">
            <div class="position-relative">
                <input type="text" name="created_at[to]"
                       class="form-control w100"
                       placeholder="To"
                       value="{{session($cacheKey . '.created_at.to') }}"
                >
                <i class="fa fa-calendar"></i>
            </div>
        </div>
    </div>
</div>
