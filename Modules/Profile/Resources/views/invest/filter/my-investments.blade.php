<div class="card">
    <h5 class="card-header">{{__('common.MyInvestments')}}</h5>
    <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
        {{__('common.Clear')}}
    </a>
    <div class="card-body">
        <div class="ui radio checkbox">
            <input class="hidden" type="radio"
                   name="my_investment" id="my_investment_include"
                   @if(session($cacheKey . '.my_investment.include') == 'include')
                   checked
                   @endif
                   value="include">
            <label class="mr-3" for="my_investment_include">
                Include
            </label>
        </div>

        <div class="ui radio checkbox mt-3">
            <input class="hidden" type="radio"
                   name="my_investment" id="my_investment_exclude"
                   @if(session($cacheKey . '.my_investment.exclude') == 'exclude')
                   checked
                   @endif
                   value="exclude">
            <label class="mr-3" for="my_investment_exclude">
                Exclude
            </label>
        </div>
    </div>
</div>
