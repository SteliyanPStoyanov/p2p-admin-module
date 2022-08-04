<label for="city{{$name}}">{{__('table.City')}}</label>
<select
	name="{{$name}}"
	id="{{$id ?? 'city_id'}}"
	class="form-control live-search-city" data-live-search="true">
    <option value>{{__('table.SelectCity')}}</option>
    @foreach($cities as $city)
        <option
            @if($selectedId == $city->city_id)
            	selected
            @endif
            value="{{$city->city_id}}">{{$city->name}}
        </option>
    @endforeach
</select>
