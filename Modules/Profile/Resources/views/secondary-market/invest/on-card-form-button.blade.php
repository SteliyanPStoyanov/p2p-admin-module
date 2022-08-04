<form class="invest-form single-buy-button investment-isOnCard m-0"
      action="{{route('profile.market-secondary.invest-single')}}"
      onsubmit="return investSecondaryMarketFormSubmit($(this));">

 <input
        type="number"
        name="amount"
        class="form-control single-amount"
        placeholder="Amount"
        max="{{ $item->principal_for_sale }}"
        min="0.1"
        step="0.01"
        value="{{$inCartArray[$item->market_secondary_id]['principal_for_sale'] }}"
    >
    <input
        type="hidden"
        class="market_secondary_id"
        name="market_secondary_id"
        value="{{$item->market_secondary_id}}"
    >
    <input
        type="hidden"
        class="loan_id"
        name="loan_id"
        value="{{$item->loan_id}}"
    >
    <input
        type="hidden"
        class="investment_id"
        name="investment_id"
        value="{{$item->investment_id}}"
    >
    <input
        type="hidden"
        class="originator_id"
        name="originator_id"
        value="{{$item->originator_id}}"
    >
    <input
        type="hidden"
        class="premium"
        name="premium"
        value="{{$item->premium}}"
    >
    <button class="ui teal button" >
        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
    </button>
    <input type="hidden" class="cart_loan_id" name="cart_loan_id" value="{{$inCartArray[$item->market_secondary_id]['cart_loan_id']}}">
    <div class="close-form" style="position: absolute; right: -25px; top: 6px; color: red; font-weight: bolder;"
         onclick="deleteCartLoan({{$inCartArray[$item->market_secondary_id]['cart_loan_id']}});">
        <i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i>
    </div>
</form>



