@extends('profile::layouts.app')

@section('title',  'Profile - ')

@section('content')
    <form method="POST" id="editForm" class="row" action="{{ route('profile.profile.update') }}" autocomplete="off">
        @csrf

        <div class="col-lg-6 trans-details text-black" id="my-profile-details">
            <h2 class="mt-5 mb-5 text-black">{{__('common.MyProfile')}}</h2>
            @if (session('fail'))
                <div class="col-12">
                    <div class="mb-1 text-left bg-danger text-white w-100 rounded-lg">{{session('fail')}}</div>
                </div>
            @endif
            @if (session('success'))
                <div class="col-12">
                    <div class="p-1 mb-1 bg-success text-white w-100 rounded-lg">{{session('success')}}</div>
                </div>
            @endif
            <div class="row border-bottom">
                <div class="col-lg-4">
                    {{__('common.AccountID')}}
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            {{$investor->investor_id}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-4">
                    {{__('common.Name')}}
                </div>
                <div class="col-lg-8 ">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                {{$investor->fullName()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-4">
                    {{__('common.E-mail')}}
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                {{$investor->email}}
                            </div>
                            <div class="input-field row">
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="email"
                                           value="{{$investor->email}}">
                                </div>
                                <div class="col-lg-12">
                                    <a class="cancel-profile ui basic button no-box no-border w-10 float-right text-center"
                                       href="#">
                                        {{ __('common.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a class="edit-profile col-lg-1 text-left" href="#">
                            {{__('common.edit')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="row border-bottom" style="display: none;">
                <div class="col-lg-4">
                    {{__('common.E-mailForContacts')}}
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                {{$investor->email_notification}}
                            </div>
                            <div class="input-field row">
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="email_notification"
                                           value="{{$investor->email_notification}}">
                                </div>
                                <div class="col-lg-12">
                                    <a class="cancel-profile ui basic button no-box no-border w-10 float-right text-center"
                                       href="#">
                                        {{ __('common.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a class="edit-profile col-lg-1 text-left" href="#">
                            {{__('common.edit')}}
                        </a>
                    </div>
                </div>
            </div>

            <div class="row border-bottom">
                <div class="col-lg-4">
                    {{__('common.Telephone')}}
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                {{$investor->phone}}
                            </div>
                            <div class="input-field row">
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="phone"
                                           value="{{$investor->phone}}">
                                </div>
                                <div class="col-lg-12">
                                    <a class="cancel-profile ui basic button no-box no-border w-10 float-right text-center"
                                       href="#">
                                        {{ __('common.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a class="edit-profile col-lg-1 text-left" href="#">
                            {{__('common.edit')}}
                        </a>
                    </div>

                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-4">
                    {{__('common.BankAccount')}}
                    <i class="fa fa-info-circle" style="margin-left: 5px; cursor: pointer;" aria-hidden="true" data-toggle="tooltip" data-placement="top"
                       data-original-title="{{__('common.BankAccountTooltipText')}} "></i>
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                {{$investor->mainBankAccount() ? $investor->mainBankAccount()->iban : ''}}
                            </div>
                            <div class="input-field row">
                                <div class="col-lg-10">
                                    <select id="bank_account_id" name="bank_account_id" class="form-control w-100">
                                        @foreach($investor->bankAccounts as $backAccount)
                                            <option
                                                @if($investor->mainBankAccount()->bank_account_id == $backAccount->bank_account_id)
                                                selected
                                                @endif
                                                value="{{$backAccount->bank_account_id}}">{{$backAccount->iban}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-12">
                                    <a class="cancel-profile ui basic button no-box no-border w-10 float-right text-center"
                                       href="#">
                                        {{ __('common.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a class="edit-profile col-lg-1 text-left" href="#">
                            {{__('common.edit')}}
                        </a>
                    </div>

                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-4">
                    {{__('common.CountryOfResidence')}}
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                {{$investor->country ? $investor->country->name : ''}}
                            </div>
                            <div class="input-field row">
                                <div class="col-lg-10">
                                    <select id="residence" name="residence" class="form-control w-100">
                                        <option value="">{{__('common.SelectCountry')}}</option>
                                        @foreach($countries as $country)
                                            <option
                                                @if($investor->country && $investor->country->country_id == $country->country_id)
                                                selected
                                                @endif
                                                value="{{$country->country_id}}">{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-12">
                                    <a class="cancel-profile ui basic button no-box no-border w-10 float-right text-center"
                                       href="#">
                                        {{ __('common.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a class="edit-profile col-lg-1 text-left" href="#">
                            {{__('common.edit')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-4">
                    {{__('common.City')}}
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                {{$investor->city}}
                            </div>
                            <div class="input-field row">
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="city"
                                           value="{{$investor->city}}">
                                </div>
                                <div class="col-lg-12">
                                    <a class="cancel-profile ui basic button no-box no-border w-10 float-right text-center"
                                       href="#">
                                        {{ __('common.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a class="edit-profile col-lg-1 text-left" href="#">
                            {{__('common.edit')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-4">
                    {{__('common.Address')}}
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                {{$investor->address}}
                            </div>
                            <div class="input-field row">
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="address"
                                           value="{{$investor->address}}">
                                </div>
                                <div class="col-lg-12">
                                    <a class="cancel-profile ui basic button no-box no-border w-10 float-right text-center"
                                       href="#">
                                        {{ __('common.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a class="edit-profile col-lg-1 text-left" href="#">
                            {{__('common.edit')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-lg-4">
                    {{__('common.PostalCode')}}
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                {{$investor->postcode}}
                            </div>
                            <div class="input-field row">
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="postcode"
                                           value="{{$investor->postcode}}">
                                </div>
                                <div class="col-lg-12">
                                    <a class="cancel-profile ui basic button no-box no-border w-10 float-right text-center"
                                       href="#">
                                        {{ __('common.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a class="edit-profile col-lg-1 text-left" href="#">
                            {{__('common.edit')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="row password-field">
                <div class="col-lg-4">
                    {{__('common.Password')}}
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="text-field">
                                *********
                            </div>
                            <div class="input-field row">
                                <div class="col-lg-10">
                                    <div>
                                        <input id="old-password" class="form-control" placeholder="Current password"
                                               type="password"
                                               name="old-password">
                                        <span toggle="#old-password"
                                              class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                    <p class="mt-3">{{ __('common.PasswordRule') }}</p>
                                    <div>
                                        <input id="new-password" class="form-control mt-3 mb-3"
                                               placeholder="{{ __('common.NewPassword') }}"
                                               type="password"
                                               name="new-password" autocomplete="new-password">
                                        <span style="margin-top: -37px;" toggle="#new-password"
                                              class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                    <div>
                                        <input id="repeat-password" class="form-control" placeholder="{{ __('common.RepeatNewPassword') }}"
                                               type="password"
                                               name="repeat-password" autocomplete="repeat-password">
                                        <span toggle="#repeat-password"
                                              class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <a class="cancel-profile ui basic button no-box no-border w-10 float-right text-center"
                                       href="#">
                                        {{ __('common.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a class="edit-profile col-lg-1 text-left" href="#">
                            {{__('common.edit')}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-3">
                {{ __('common.NotifyMeEveryTime') }}
                <div class="form-check mt-3 w-100">
                    <input class="form-check-input mt-0 mr-2 check-box-w20"
                           @if($investor->addFundNotificationChecked())
                           checked
                           @endif
                           type="checkbox" name="add-funds"
                           id="add-funds">
                    <label class="form-check-label ml-3" for="add-funds">
                        {{ __('common.FundsAreAddedToNyAccount') }}
                    </label>
                </div>
                <div class="form-check mt-3 w-100">
                    <input class="form-check-input mt-0 mr-2 check-box-w20"
                           @if($investor->withdrawNotificationChecked())
                           checked
                           @endif
                           type="checkbox" name="withdrawal-made"
                           id="withdrawal-made">
                    <label class="form-check-label ml-3" for="withdrawal-made">
                        {{ __('common.WithdrawalRequestIsMade') }}
                    </label>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input mt-0 mr-2 check-box-w20"
                           @if($investor->newDeviceNotificationChecked())
                           checked
                           @endif
                           type="checkbox" name="new-device"
                           id="new-device">
                    <label class="form-check-label ml-3" for="new-device">
                        {{ __('common.NewDeviceLoggedIn') }}
                    </label>
                </div>
            </div>
            <br>
            <div class="row mt-5 pt-2 mb-5 font-weight-light">
                {{ __('common.NoteForExtraSecurityYou') }}

            </div>
            <div class="row">
                <h2>{{__('common.Agreements')}}</h2>
            </div>
            <div class="row">
                @foreach($investor->contracts as $contract)
                    <p class="text-primary">
                        <a href="{{ route('profile.profile.downloadAgreement', $contract->investor_contract_id) }}"
                           target="_blank">
                            {{ $contract->template->name . ' ' . __('common.concludedOn') . ' ' . date_format($contract->created_at, 'd-m-Y') }}
                        </a>
                    </p>
                @endforeach
            </div>
            <div class="row">
                <input id="form_submit" class="btn ui teal button ml-auto w-25" type="submit"
                       value="{{ __('common.SaveChanges') }}">
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        $('.input-field').hide();

        $('.edit-profile').click(function (e) {
            e.preventDefault();
            $(this).parent().find('.text-field').hide();
            $(this).parent().find('.input-field').show();
            $(this).hide();
        });

        $('.cancel-profile').click(function (e) {
            e.preventDefault();
            $(this).parent().parent().parent().find('.text-field').show();
            $(this).parent().parent().parent().find('.input-field').hide();
            $(this).parent().parent().parent().parent().find('.edit-profile').show();
        });


        $(".toggle-password").click(function () {

            $(this).toggleClass("fa-eye fa-eye-slash");
            let input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
@endpush
