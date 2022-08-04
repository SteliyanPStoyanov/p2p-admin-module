<select name="{{$componentNameCustom}}" id="sex" class="form-control">
    <option value>{{__('table.Sex')}}</option>
    <option
    	@if($sex == 'male')
            selected
        @endif
        value='male'>
        {{__('table.Man')}}
    </option>
    <option
    	@if($sex == 'female')
            selected
        @endif
        value='female'>
        {{__('table.Woman')}}
    </option>
    <option
    	@if($sex == 'common')
            selected
        @endif
        value='common'>
        {{__('table.Common')}}
    </option>
</select>
