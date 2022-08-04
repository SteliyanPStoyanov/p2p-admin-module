@php
    $types = \Modules\Common\Entities\Loan::getTypesWithLabels();
@endphp

<div class="card">
    <h5 class="card-header">{{__('common.LoanType')}}</h5>
    <div class="card-body">
        <div class="form-group">
            <select class="form-control w-100" name="loan[type]">
                <option value="">{{__('common.AllLoanTypes')}}</option>
                @foreach($types as $type)
                    <option @if(session($cacheKey . '.loan.type') == $type)
                            selected
                            @endif
                            value="{{loanType($type,true)}}">{{$type}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <a href="#" class="text-secondary ml-4 mb-3 clearGroupFilter">
        {{__('common.Clear')}}
    </a>
</div>
