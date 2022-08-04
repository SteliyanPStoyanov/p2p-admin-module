<button
    style="width: 144px; padding: .715em 0"
    class="ui teal button btn-filter-submit float-right ml-3 sell_loan"
    data-loanid="{{ $loan->loan_id }}"
    onClick="openSellForm($(this))"
>
    {{__('common.SellLoan')}}
</button>
<form class="invest-form ml-3 single-sell-button" action="{{route('profile.my-investments.sell')}}"
      onSubmit="sellFormSubmit($(this)); return false;" style="display: none; width: 123px;">
    <input type="number" name="amount" class="form-control single-amount"
           placeholder="Amount" min="0.1" value="{{$investment->invested_sum}}" step="0.01">
    <input type="hidden" class="loan_id" name="loan_id" value="{{$loan->loan_id}}">
    <input type="hidden" class="investment_id" name="investment_id" value="{{$investment->investment_id}}">
    <input type="hidden" class="originator_id" name="originator_id" value="{{$loan->originator->originator_id}}">
    <button class="ui teal button">
        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
    </button>
    <div class="close-form" style="position: absolute; right: -25px; top: 5px; color: red; font-weight: bolder;"
         onclick="sellSingleFormClose($(this));">
        <i class="fa fa-times" aria-hidden="true"  style="font-size: 20px;"></i>
    </div>
</form>
