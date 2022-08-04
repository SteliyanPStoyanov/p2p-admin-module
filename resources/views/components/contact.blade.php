@include('components.simple-select',
        [
         'nameType' => $nameType,
         'selectedId' => $selectedId,
         'firstOption' => $firstOption,
         'key' => $key,
         'label' => $label,
         'collection' => $collection,
         'id' => $id,
         ]
     )
<br>

<label for="contact_first_name">{{__('common.Name')}}</label>
<input type="text" class="form-control" id="contact_first_name" value="{{$contactFirstName}}"
       name="{{$contactName}}"/>

<label for="contact_middle_name">{{__('common.MiddleName')}}</label>
<input type="text" class="form-control"  id="contact_middle_name" value="{{$middleName}}"
       name="{{$contactMiddleName}}"/>

<label for="contact_last_name">{{__('common.LastName')}}</label>
<input type="text" class="form-control" id="contact_last_name" value="{{$lastName}}"
       name="{{$contactLastName}}"/>

<label for="contact_phone">{{__('common.Phone')}}</label>
<input type="text" class="form-control" id="contact_phone" value="{{$phone}}"
       name="{{$contactPhone}}"/>
