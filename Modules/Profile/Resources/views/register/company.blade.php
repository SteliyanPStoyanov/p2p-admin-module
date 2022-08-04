<div class="row m-0">
    <div class="col-lg-6 mb-3">
        <input class="w-100" type="hidden" name="type" value="{{\Modules\Common\Entities\Investor::TYPE_COMPANY}}" >
        <input class="w-100" type="text" name="company_name" value="{{old('company_name')}}"
               placeholder="{{ __('common.CompanyName') }}"
               autocomplete="company_name">
    </div>
    <div class="col-lg-6">
        <input class="w-100" type="text" name="company_number" value="{{old('company_number')}}"
               placeholder="{{ __('common.CompanyNumber') }}"
               autocomplete="company_number">
    </div>
    <div class="col-lg-6 mb-3">
        <input class="w-100" type="text" name="first_name" value="{{old('first_name')}}"
               placeholder="{{ __('common.FirstName') }}"
               autocomplete="first_name">
    </div>
    <div class="col-lg-6">
        <input class="w-100" type="text" name="last_name" value="{{old('last_name')}}"
               placeholder="{{ __('common.LastName') }}"
               autocomplete="last_name">
    </div>

    <div class="col-lg-12 mt-3 mb-3">
        <input id="password" class="w-100" type="password" name="password"
               value="{{old('password')}}"
               placeholder="{{ __('common.Password') }}"
               autocomplete="password">
        <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
        <p class="additional-infotext mt-1">
            {{ __('common.PasswordRule') }}
        </p>
        <div class="form-check mt-3">
            <input class="form-check-input mt-0 mr-2 check-box-w20" type="checkbox"
                   name="agreement"
                   id="agreement">
            <label class="form-check-label ml-2" for="agreement">
                I confirm that I've read and agree to the <a
                    href="{{ url('/') }}/user-agreement"
                    target="_blank"
                    style="color: #0070C0 !important;">User
                    Agreement.</a>
            </label>
        </div>
        <div class="form-check mt-3">
            <input class="form-check-input mt-0 mr-2 check-box-w20" type="checkbox"
                   name="marketing"
                   id="marketing">
            <label class="form-check-label ml-2" for="marketing">
                {{ __('common.AgreeMarketing') }}

            </label>
        </div>
    </div>
</div>
