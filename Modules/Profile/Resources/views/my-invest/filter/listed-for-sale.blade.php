<div class="card">
    <h5 class="card-header">{{__('common.ListedForSale')}}</h5>
    <div class="card-body overflow-auto ">
        <div class="ui radio checkbox">
            <input class="hidden noClear" value="all"
                   type="radio"
                   name="investment[listed]"
                   checked
                   id="loan_listed_all">
            <label class="mr-3" for="loan_listed_all">
                {{__('common.LoanListedAll')}}
            </label>
        </div>
        <div class="ui radio checkbox mt-3">
            <input class="hidden noClear" value="listed"
                   type="radio"
                   name="investment[listed]"
                   id="loan_only_listed">
            <label class="mr-3" for="loan_only_listed">
                {{__('common.LoanOnlyListed')}}
            </label>
        </div>
        <div class="ui radio checkbox mt-3">
            <input class="hidden noClear" value="exclude"
                   type="radio"
                   name="investment[listed]"
                   id="loan_only_exclude">
            <label for="loan_only_exclude">
                {{__('common.LoanExcludeListed')}}
            </label>
        </div>

    </div>
</div>
