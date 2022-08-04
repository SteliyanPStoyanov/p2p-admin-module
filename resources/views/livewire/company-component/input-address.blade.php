<strong class="float-left mr-1">{{__('common.CompanyAddress')}} :</strong>
@if(!empty($this->field) and $this->field == 'address')
    <input wire:model="address" style="margin-top: -8px" class="form-control float-left w-50">
    <button type="submit"><i class=" fa fa-floppy-o ml-2"></i></button>
@else
    {{$investor->company->first()->address}}
    <i class="fa fa-pencil ml-2" style="cursor: pointer;" aria-hidden="true"
       wire:click="companyField('address', '{{$investor->company->first()->address}}')"></i>
@endif
@error('address')
<div class="mb-1 text-error w-100 float-left">{{ $message }}</div> @enderror
