<form style="width: 123px;" class="invest-form single-sell-button ml-3 investment-isOnCard" action="{{route('profile.my-investments.sell')}}"
      onSubmit="sellFormSubmit($(this)); return false;">
    <input type="number" name="amount" class="form-control single-amount"
           placeholder="Amount" min="0.1" step="0.01"
           value="{{$inCartArray[$investment->investment_id]['principal_for_sale'] }}">
    <input type="hidden" class="loan_id" name="loan_id" value="{{$loan->loan_id}}">
    <input type="hidden" class="investment_id" name="investment_id" value="{{$investment->investment_id}}">
    <input type="hidden" class="originator_id" name="originator_id" value="{{$loan->originator->originator_id}}">
    <button class="ui teal button" disabled>
        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
    </button>
    <input type="hidden" class="cart_loan_id" name="cart_loan_id" value="{{$inCartArray[$investment->investment_id]['cart_loan_id']}}">
    <div class="close-form" style="position: absolute; right: -25px; top: 5px; color: red; font-weight: bolder;"
         onclick="deleteCartLoan({{$inCartArray[$investment->investment_id]['cart_loan_id']}});">
        <i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i>
    </div>
</form>
