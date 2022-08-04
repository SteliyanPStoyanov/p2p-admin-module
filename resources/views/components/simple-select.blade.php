<label for="select{{$name}}">{{$label}}</label>
<select name="{{$name}}" id="{{$id}}" class="form-control">
    <option value>{{$firstOption}}</option>
    @foreach($collection as $item)
        <option
            @if($selectedId == $item->{$key})
                selected
            @endif
            value="{{$item->$key}}">{{$item->name}}</option>
    @endforeach
</select>
