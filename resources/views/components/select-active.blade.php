<select name="active" class="form-control w-100">
    <option value>{{__('common.SelectByStatus')}}</option>
    <option
        @if($active === '1')
            selected
        @endif
    value='1'>
        Active
    </option>
    <option
        @if($active === '0')
            selected
        @endif
    value='0'>
        Inactive
    </option>
</select>
