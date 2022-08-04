<select name="{{$componentNameCustom}}" id="type" class="form-control w-100">
    <option value>{{__('common.Type')}}</option>
     @foreach($typeOptions as $typeOption)
    <option
        @if($type == $typeOption)
        selected
        @endif
        value='{{$typeOption}}'>
        {{__('communication::smsTable.'.ucfirst($typeOption))}}
    </option>
    @endforeach
</select>
