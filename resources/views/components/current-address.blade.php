@include('components.select-city',
        ['cities' => $cities,
         'id' => 'client_current_address_city_id',
         'name' => $citiesName,
         'selectedId' => $selectedCityId,
        ]
    )
<label for="current_address">{{__('common.Address')}}</label>
<input type="text" class="form-control" name="{{$addressName}}" value="{{$address}}"/>
<label for="address_post_code">{{__('common.PostCode')}}</label>
<input type="text" class="form-control" name="{{$postCodeName}}" value="{{$postCode}}"/>


