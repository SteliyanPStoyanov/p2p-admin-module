<select name="blocked" class="form-control">
    <option value>{{__('table.FilterByBlocked')}}</option>
    <option
        @if($blocked === '1')
    selected
    @endif
    value='1'>
        {{__('table.Yes')}}
    </option>
    <option
        @if($blocked === '0')
    selected
    @endif
    value='0'>
        {{__('table.No')}}
    </option>
</select>
