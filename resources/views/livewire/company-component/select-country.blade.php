<strong class="float-left mr-1">{{__('common.CompanyCountry')}} :</strong>
@if(!empty($this->field) and $this->field == 'country_id')
    <select style="margin-top: -8px" wire:model="country_id" class="form-control float-left w-50">
        @foreach( $this->countries as $country )
            <option value="{{$country->country_id}}">{{$country->name}}</option>
        @endforeach
    </select>
    <button type="submit"><i class=" fa fa-floppy-o ml-2"></i></button>
@else
    {{!empty($investor->company->first()->country_id) ? $investor->company->first()->country->name : ''}}
    <i class="fa fa-pencil ml-2" style="cursor: pointer;" aria-hidden="true"
       wire:click="companyField('country_id', '{{$investor->company->first()->country_id}}')"></i>
@endif
@error('country_id')
<div class="mb-1 text-error w-100 float-left">{{ country_id }}</div> @enderror
