<div onclick="investSecondaryMarketSingleForm($(this));"
     class="invest-button-form ui teal button mt-0 mb-0">
    {{__('common.Invest')}}
</div>
<form style="display: none;" class="invest-form single-buy-button mt-0 mb-0"
      action="{{route('profile.market-secondary.invest-single')}}" method="POST"
      onsubmit="return investSecondaryMarketFormSubmit($(this));">
    <input
        type="number"
        name="amount"
        class="form-control single-amount"
        placeholder="Amount"
        min="0.1"
        max="{{ $item->principal_for_sale }}"
        step="0.01"
        value="{{ $item->principal_for_sale }}"
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
    <button class="ui teal button">
        <i class="fa fa-shopping-bag" aria-hidden="true"></i>
    </button>
    <div class="close-form" style="position: absolute; right: -25px; top: 6px;">
        <i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i>
    </div>
</form>
