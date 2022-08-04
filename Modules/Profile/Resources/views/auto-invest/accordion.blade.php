<div class="card border-0">
    <div class="card-header" id="heading1">
        <h5 class="mb-0 accordion-title-select">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse1"
                    aria-expanded="false" aria-controls="collapse1">
                <i class="fa fa-chevron-down rotate"></i> {{__('common.WhatIsAutoInvestStrategy')}}
            </button>
        </h5>
    </div>

    <div id="collapse1" class="collapse" aria-labelledby="heading1"
         data-parent=".accordion">
        <div class="card-body">{!! trans('common.WhatIsAutoInvestStrategyContent')!!}</div>
    </div>
</div>

<div class="card border-0">
    <div class="card-header" id="heading2">
        <h5 class="mb-0 accordion-title-select">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse2"
                    aria-expanded="false" aria-controls="collapse2">
                <i class="fa fa-chevron-down rotate"></i> {{__('common.HowCanICreateNewStrategy')}}
            </button>
        </h5>
    </div>

    <div id="collapse2" class="collapse" aria-labelledby="heading2"
         data-parent=".accordion">
        <div class="card-body">{!! trans ('common.HowCanICreateNewStrategyContent')!!}</div>
    </div>
</div>
<div class="card border-0">
    <div class="card-header" id="heading3">
        <h5 class="mb-0 accordion-title-select">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse3"
                    aria-expanded="false" aria-controls="collapse3">
                <i class="fa fa-chevron-down rotate"></i> {{__('common.CanICreateMoreThanOneStrategy')}}
            </button>
        </h5>
    </div>

    <div id="collapse3" class="collapse" aria-labelledby="heading3"
         data-parent=".accordion">
        <div class="card-body">{{__('common.CanICreateMoreThanOneStrategyContent')}}</div>
    </div>
</div>

<div class="card border-0">
    <div class="card-header" id="heading4">
        <h5 class="mb-0 accordion-title-select">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse4"
                    aria-expanded="false" aria-controls="collapse4">
                <i class="fa fa-chevron-down rotate"></i> {{__('common.StopAutoInvestStrategy')}}
            </button>
        </h5>
    </div>

    <div id="collapse4" class="collapse" aria-labelledby="heading4"
         data-parent=".accordion">
        <div class="card-body">{{__('common.StopAutoInvestStrategyContent')}}</div>
    </div>
</div>

<div class="card border-0">
    <div class="card-header" id="heading5">
        <h5 class="mb-0 accordion-title-select">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse5"
                    aria-expanded="false" aria-controls="collapse5">
                <i class="fa fa-chevron-down rotate"></i> {{__('common.InvestManually')}}
            </button>
        </h5>
    </div>

    <div id="collapse5" class="collapse" aria-labelledby="heading5"
         data-parent=".accordion">
        <div class="card-body">{{__('common.InvestManuallyContent')}}</div>
    </div>
</div>
