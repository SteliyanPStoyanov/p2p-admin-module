<label for="client-bank">{{__('common.Bank')}}</label>
<select name="{{$name}}" id="client-bank" class="form-control live-search-city" data-live-search="true">
    <option value>{{__('common.SelectBank')}}</option>
    @foreach($banks as $bank)
        <option style="font-size: 13px;"
            @if($selectedId == $bank->bank_id)
            selected
            @endif
            value="{{$bank->bank_id}}">{{$bank->bic}} - {{$bank->name}}</option>
    @endforeach
</select>
