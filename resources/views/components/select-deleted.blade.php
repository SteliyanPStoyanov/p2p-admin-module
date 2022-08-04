<select name="deleted" class="form-control">
    <option value>{{__('common.FilterByDeleted')}}</option>
    <option
        @if($deleted === '1')
        selected
        @endif
        value='1'>
        {{__('common.Yes')}}
    </option>
    <option
        @if($deleted === '0')
        selected
        @endif
        value='0'>
        {{__('common.No')}}
    </option>
</select>
