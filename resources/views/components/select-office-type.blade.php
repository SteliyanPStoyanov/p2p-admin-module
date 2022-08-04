<select name="officeTypeId" class="form-control w-100">
    <option value>{{__('common.OfficeSelectByOfficeType')}}</option>
    @foreach($officeTypes as $officeType)
        <option
            @if($officeType->office_type_id == session($cacheKey . '.officeTypeId'))
            selected
            @endif
            value="{{ $officeType->office_type_id }}"
        >
            {{ $officeType->name }}
        </option>
    @endforeach
</select>
