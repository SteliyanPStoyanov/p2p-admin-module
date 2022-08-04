<form
    class="invest-all-form
@if($allInPage) inline-block  @endif"
    style="width: 122px; float: right;"
    onsubmit="return investAllFormSubmit($(this));"
    method="POST"
    action="{{route('profile.market-secondary.invest-all')}}">
    <input class="form-control d-inline-block invest-all-form-amount float-right pr-0"
           type="number"
           name="amount"
           placeholder="{{ __('common.Amount') }}" min="0.1"
           value="{{$loansInSecondaryCart->first()->principal_for_sale ?? 0}}"
           step="0.01">
    <button class="ui teal button d-inline-block float-right invest-all-form-submit"
            type="submit"
            value=""><i class="fa fa-shopping-bag" aria-hidden="true"></i></button>
    <div class="close-form remove-all-from-cart"
         style="position: absolute; right: -25px; top: 40px; color: red; font-weight: bolder;"
         onclick="deleteAllCartLoan($(this));">
        <i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i>
    </div>
</form>
<div id="investAllFormHas"></div>
<button style="width: 130px;" onclick="return investAllForm()"
        class="ui teal button btn-filter-submit float-right invest-all-button
        @if($allInPage) hide-some-element @endif">
    {{__('common.InvestInAll')}}</button>

<div class="close-form remove-all-from-cart remove-buy-cart
             @if($someInPage)
    inline-block
@else
    hide-some-element
@endif
    "
     style="position: absolute; right: -25px; top: 40px; color: red; font-weight: bolder;"
     onclick="deleteAllCartLoan($(this));">
    <i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i>
</div>

