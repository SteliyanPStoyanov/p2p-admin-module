<div class="buy-info-box mb-5 pb-1">
    <div class="pt-2">
        <span class="buy-number h2 pr-3">{{$countInvested}}</span> <span
            class="buy-text">{{__('common.InvestmentsMade')}}</span>
    </div>
    <div class="pt-2">
        <span class="buy-number h2 pr-3">{{amount($amountInvested)}}</span> <span
            class="buy-text">{{__('common.InvestedAmount')}}</span>
    </div>
</div>
@if($countProblematic > 0)
    <p>
        {!! __('common.WeCouldntBuy' ,
                [
                'link' => route('profile.invest.view-unsuccessful'),
                'count' => $countProblematic,
                'linkSec' => route('profile.invest.view-unsuccessful'),
                ]
            )
         !!}
    </p>
@endif

<h4 class="mt-4 mb-2 text-black pl-0">{{__('common.NeedHelpInvesting')}}</h4>

<p>
    {!! __('common.VisitOurHelpSection' ,
           [
           'link' => route('profile.help.index') .'#Investing',
           ]
       )
    !!}

</p>
