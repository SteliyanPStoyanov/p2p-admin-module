<div class="card">
    <h5 class="card-header">{{__('common.LoanStatusActive')}}
        / {{__('common.LoanStatusRepaid')}}</h5>
    <div class="card-body overflow-auto ">
        @php
            $active = \Modules\Common\Entities\Loan::STATUS_ACTIVE;
            $repaid = \Modules\Common\Entities\Loan::STATUS_REPAID;
        @endphp
        <div class="ui radio checkbox">
            <input class="hidden noClear" value="{{$active}}"
                   type="radio"
                   name="loan[status]"
                   checked
                   id="loan_status1">
            <label class="mr-3" for="loan_status1">
                {{__('common.LoanActive')}}
            </label>
        </div>
        <div class="ui radio checkbox mt-3">
            <input class="hidden noClear" value="{{$repaid}}"
                   type="radio"
                   name="loan[status]"
                   @if(session($cacheKey . '.loan.status') == $repaid)
                   checked
                   @endif
                   id="loan_status2">
            <label for="loan_status2">
                {{__('common.LoanFinished')}}
            </label>
        </div>

    </div>
</div>
