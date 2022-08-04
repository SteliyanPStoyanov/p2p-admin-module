<select name="{{$componentNameCustom}}" id="sex" class="form-control">
    <option value>{{__('common.Sex')}}</option>
    <option
    	@if($sex == 'male')
            selected
        @endif
        value='male'>
        {{__('common.Man')}}
    </option>
    <option
    	@if($sex == 'female')
            selected
        @endif
        value='female'>
        {{__('common.Woman')}}
    </option>
</select>
