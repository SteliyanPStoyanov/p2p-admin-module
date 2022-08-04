@include('components.simple-select',
        [
         'name' => $name,
         'selectedId' => $selectedId,
         'firstOption' => $firstOption,
         'key' => $key,
         'label' => $label,
         'collection' => $collection,
         'id' => $id,
         ]
     )
<br>

<label for="guarant_first_name">{{__('common.Name')}}</label>
<input type="text" class="form-control" id="guarant_first_name" value="{{$nameGuarant}}"
       name="{{$guarantName}}"/>

<label for="guarant_middle_name">{{__('common.MiddleName')}}</label>
<input type="text" class="form-control" id="guarant_middle_name" value="{{$middleName}}"
       name="{{$guarantMiddleName}}"/>

<label for="guarant_last_name">{{__('common.LastName')}}</label>
<input type="text" class="form-control" id="guarant_last_name" value="{{$lastName}}"
       name="{{$guarantLastName}}"/>

<label for="guarant_phone">{{__('common.Phone')}}</label>
<input type="text" class="form-control" id="guarant_phone" value="{{$phone}}"
       name="{{$phoneName}}"/>

<label for="guarant_pin">{{__('common.Pin')}}</label>
<input type="text" class="form-control" id="guarant_pin" value="{{$pin}}"
       name="{{$pinName}}"/>

<label class="w-100" for="guarant_idcard_number">{{ __('common.IdCardNumber') }}</label>
<input type="text" class="form-control" id="guarant_id_card_number" name="{{$idCardNumberName}}" value="{{$idCardNumber}}"/>

<label for="guarant_idcard_issue_date">{{__('common.IssueDate')}}</label>
<input class="form-control" type="date" id="guarant_id_card_issue_date" name="{{$issueDateName}}" value="{{$issueDate}}"
       placeholder="YYYY/MM/DD">

<label for="guarant_idcard_valid_date">{{__('common.ValidDate')}}</label>
<input class="form-control" type="date" id="guarant_id_card_valid_date" name="{{$validDateName}}" value="{{$validDate}}"
       placeholder="YYYY/MM/DD">

<label for="guarant_address">{{__('common.Address')}}</label>
<input type="text" class="form-control" name="{{$addressName}}" id="guarant_address" value="{{$address}}">
