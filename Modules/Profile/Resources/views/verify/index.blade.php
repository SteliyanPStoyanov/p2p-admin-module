@extends('profile::layouts.app')

@section('title',  'Verify your identity - ')

@section('content')
    <div class="row">

        <div class="col-lg-6 mx-auto text-center text-black">
            <div id="formContent" class="w-100" style="max-width: 100%;">
                <div class="row px-2">
                    <h2 class="font-weight-bold mt-3 d-block w-100 text-alt-gray">{{ __('common.VerifyYourIdentity') }}</h2>
                    <p class="mt-2 d-block w-100 text-black">{{ __('common.NeedSomePersonalInformation') }}</p>
                </div>
                <form method="POST" class="row w-100 mx-auto" action="{{route('profile.verify.verifySubmit')}}"
                      autocomplete="off">
                    @csrf
                    @if($errors->count())
                        <div class="col-12 d-block">
                            <div class="mt-1 pl-1 mb-0 bg-danger text-left w-100 rounded-lg"
                                 style="margin-bottom: -1rem">
                                @foreach($errors->all() as $error)
                                    @if($error != __('common.Required'))
                                        {{$error}}
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="form-group w-100 mt-3 row text-left mb-3">
                        <label class="col-12">{{ __('common.DateOfBirth') }}</label>
                        <div class="col mr-3">
                            <select type="text" name="day" class="form-control"
                                    placeholder="{{ __('common.Day') }}">
                                @for($day = 1; $day <= 31; $day++)
                                    <option @if(old('day') == $day ||
                                 (!empty(\Carbon\Carbon::parse($investor->birth_date)->format('d') &&
                                 \Carbon\Carbon::parse($investor->birth_date)->format('d') == $day)))
                                            selected @endIf value="{{$day}}">{{sprintf("%02d", $day)}}</option>
                                @endfor
                            </select>
                            @if(!empty($errors) && $errors->has('day'))
                                <div class="row">
                                    <div class="mt-1 pl-1 mb-0 bg-danger text-left"
                                         style="margin-bottom: -1rem">{{$errors->first('day')}}</div>
                                </div>
                            @endif
                        </div>
                        <div class="col mr-3">
                            <select type="text" name="month" class="form-control"
                                    placeholder="{{ __('common.Month') }}">
                                @for($month = 1; $month <= 12; $month++)
                                    <option @if(old('month') == $month ||
                                 (!empty(\Carbon\Carbon::parse($investor->birth_date)->format('m') &&
                                 \Carbon\Carbon::parse($investor->birth_date)->format('m') == $month)))
                                            selected @endIf value="{{$month}}">{{sprintf("%02d",$month)}}</option>
                                @endfor
                            </select>
                            @if(!empty($errors) && $errors->has('month'))
                                <div class="row">
                                    <div class="mt-1 pl-1 mb-0 bg-danger text-left"
                                         style="margin-bottom: -1rem">{{$errors->first('month')}}</div>
                                </div>
                            @endif
                        </div>

                        <div class="col">
                            @php
                                $yCounts = \Carbon\Carbon::now()->format('Y') - 1920;

                            @endphp

                            <select type="text" name="year" class="form-control"
                                    placeholder="{{ __('common.Year') }}">
                                @for($year = 1; $year <= $yCounts; $year++)
                                    <option @if(old('year') == \Carbon\Carbon::now()->subYears($year)->format('Y') ||
                                 (!empty(\Carbon\Carbon::parse($investor->birth_date)->format('Y') &&
                                 \Carbon\Carbon::parse($investor->birth_date)->format('Y') == \Carbon\Carbon::now()->subYears($year)->format('Y'))))
                                            selected
                                            @endIf value="{{\Carbon\Carbon::now()->subYears($year)->format('Y')}}">
                                        {{\Carbon\Carbon::now()->subYears($year)->format('Y')}}</option>
                                @endfor
                            </select>
                            @if(!empty($errors) && $errors->has('year'))
                                <div class="row">
                                    <div class="mt-1 pl-1 mb-0 bg-danger text-left"
                                         style="margin-bottom: -1rem">{{$errors->first('year')}}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group w-100 row text-left my-3">
                        <div class="col-12">
                            <label for="citizenship">{{ __('common.CountryOfCitizenship') }}</label>
                            <select id="citizenship" name="citizenship" class="form-control">
                                <option selected="">{{ __('common.ChooseCitizenship') }}</option>
                                @foreach($countries as $country)
                                    <option
                                        @if(($investor->citizenship ?? old('citizenship')) == $country->country_id)
                                        selected
                                        @endIf
                                        value="{{$country->country_id}}">
                                        {{$country->name}}
                                    </option>
                                @endforeach
                            </select>
                            @if(!empty($errors) && $errors->has('citizenship'))
                                <div class="row">
                                    <div class="mt-1 pl-1 mb-0 bg-danger text-left"
                                         style="margin-bottom: -1rem">{{$errors->first('citizenship')}}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group w-100 row text-left my-3">
                        <div class="col-12">
                            <label for="residence">{{ __('common.CountryOfResidence') }}</label>
                            <select id="residence" name="residence" class="form-control">
                                <option selected="">{{ __('common.ChooseCountryOfResidence') }}</option>
                                @foreach($countries as $country)
                                    <option
                                        @if(($investor->residence ?? old('residence')) == $country->country_id)
                                        selected
                                        @endIf
                                        value="{{$country->country_id}}">{{$country->name}}</option>
                                @endforeach
                            </select>
                            @if(!empty($errors) && $errors->has('residence'))
                                <div class="row">
                                    <div class="mt-1 pl-1 mb-0 bg-danger text-left"
                                         style="margin-bottom: -1rem">{{$errors->first('residence')}}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group w-100 row text-left my-3">
                        <div class="col-12">
                            <label for="address">{{ __('common.ResidenceAddress') }}</label>
                            <input type="text" id="address" value="{{ $investor->address ?? old('address') }}"
                                   name="address"
                                   class="form-control"
                                   placeholder="{{ __('common.IncludeStreetNameAndNumber') }}">
                            @if(!empty($errors) && $errors->has('address'))
                                <div class="row">
                                    <div class="mt-1 pl-1 mb-0 bg-danger text-left"
                                         style="margin-bottom: -1rem">{{$errors->first('address')}}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group w-100 row text-left my-3">
                        <div class="col mr-3">
                            <label class="w-100" for="city">{{ __('common.City') }}</label>
                            <input type="text" id="city" value="{{ $investor->city ?? old('city') }}" name="city"
                                   class="form-control"
                                   placeholder="{{ __('common.City') }}">
                            @if(!empty($errors) && $errors->has('city'))
                                <div class="row">
                                    <div class="mt-1 pl-1 mb-0 bg-danger text-left"
                                         style="margin-bottom: -1rem">{{$errors->first('city')}}</div>
                                </div>
                            @endif
                        </div>
                        <div class="col">
                            <label class="w-100" for="postcode">{{ __('common.PostalCode') }}</label>
                            <input type="text" id="postcode" value="{{ $investor->postcode ?? old('postcode') }}"
                                   name="postcode"
                                   class="form-control"
                                   placeholder="{{ __('common.PostalCode') }}">
                            @if(!empty($errors) && $errors->has('postcode'))
                                <div class="row">
                                    <div class="mt-1 pl-1 mb-0 bg-danger text-left"
                                         style="margin-bottom: -1rem">{{$errors->first('postcode')}}</div>
                                </div>
                            @endif
                        </div>

                    </div>
                    <div class="form-group w-100 row text-left no-inset">
                        <div class="col-12">
                            <p class="mt-3 text-black px-1">

                                {{ ($investor->type == \Modules\Common\Entities\Investor::TYPE_INDIVIDUAL)
                                    ?  __('common.AreYouOneOfYourImmediate')
                                    :  __('common.IsTheBusinessOwner') }}
                            </p>
                        </div>
                        <div class="form-check col-1 ml-4 mt-3">
                            <input class="form-check-input" type="radio" name="political" id="politicalRadios1"
                                   value="0" @if(($investor->political ?? old('political')) == 0)
                                   checked
                                @endIf>
                            <label class="form-check-label" for="politicalRadios1">
                                {{ __('common.No') }}
                            </label>
                        </div>
                        <div class="form-check col-1 mt-3 ml-3">
                            <input class="form-check-input" type="radio" name="political" id="politicalRadios2"
                                   value="1" @if(($investor->political ?? old('political')) == 1)
                                   checked
                                @endIf>
                            <label class="form-check-label" for="politicalRadios2">
                                {{ __('common.Yes') }}
                            </label>
                        </div>
                        <div class="col-12 mt-4 text-right">
                            <input id="form_submit" class="ui teal button w-100" type="submit"
                                   value="{{ __('common.Continue') }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
