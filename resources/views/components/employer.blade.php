<div class="form-group">
    <label for="employer_name">{{__('common.Name')}}</label>
    <input type="text" class="form-control" name="{{$employerName}}" value="{{$name}}">
</div>
<div class="form-group">
    <label for="employer_bulstat">{{__('common.Bulstat')}}</label>
    <input type="text" class="form-control" name="{{$bulstatName}}" value="{{$bulstat}}">
</div>
@include('components.select-city',
    ['cities' => $cities,
     'name' => $citiesName,
     'selectedId' => $selectedCityId,
     'id'         => $citiesId,
     ])

<div class="form-group">
    <label for="employer-address">{{__('common.Address')}}</label>
    <input type="text" class="form-control" name="{{$addressName}}" value="{{$address}}">
</div>
<div class="form-group">
    <label for="employer-details">{{__('common.Details')}}</label>
    <input type="text" class="form-control" name="{{$detailsName}}" value="{{$details}}">
</div>
<div class="form-group">
    <label for="employer-position">{{__('common.Position')}}</label>
    <input type="text" class="form-control" name="{{$positionName}}" value="{{$position}}">
</div>
<div class="form-group">
    <label for="employer-salary">{{__('common.Salary')}}</label>
    <input type="number" class="form-control" name="{{$salaryName}}" value="{{$salary}}">
</div>
<div class="form-group">
    <label for="employer-experience">{{__('common.Experience')}}</label>
    <input type="number" class="form-control" name="{{$experienceName}}" value="{{$experience}}">
</div>
