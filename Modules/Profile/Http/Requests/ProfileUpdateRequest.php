<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class ProfileUpdateRequest extends BaseRequest
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [
            'email_notification' => [
                'nullable',
                'regex:/(.+)@(.+)\.(.+)/i'
            ],
            'email' => [
                'nullable',
                'regex:/(.+)@(.+)\.(.+)/i',
            ],
            'first_name' => [
                'min:2',
                'nullable',
                'max:40',
            ],
            'middle_name' => [
                'min:2',
                'nullable',
                'max:40',
                Rule::requiredIf($request->user('investor')->middle_name != $this->input('middle_name')),
            ],
            'last_name' => [
                'min:2',
                'nullable',
                'max:40',
            ],
            'phone' => [
                Rule::requiredIf($request->user()->phone != $this->input('phone')),
                'nullable',
                'numeric',
                'digits_between:7,15',
                'regex:/^([0-9_\-\s\-+()]*)$/'

            ],
            'bank_account_id' => [
                Rule::requiredIf($request->user()->bankAccounts->count()),
                'nullable',
                'min:1',
            ],
            'residence' => [
                Rule::requiredIf($request->user()->residence != $this->input('residence')),
                'nullable',
                'min:1',
            ],
            'city' => [
                Rule::requiredIf($request->user()->city != $this->input('city')),
                'nullable',
                'min:2',
            ],
            'address' => [
                Rule::requiredIf($request->user()->address != $this->input('address')),
                'nullable',
                'min:2',
            ],
            'postcode' => [
                Rule::requiredIf($request->user()->postcode != $this->input('postcode')),
                'nullable',
                'max:20',
                'regex:/^[a-zA-Z0-9_\-\_\;\(\)\*\# ]*$/',
            ],
        ];

        if (!empty($request['new-password'])) {
            $rules['old-password'] = [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::guard('investor')->user()->password)) {
                        $fail('Current password missmatch!');
                    }
                },
            ];
            $rules['new-password'] = 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/';
            $rules['repeat-password'] = 'required|same:new-password';
        }

        if ($request->user('investor')->email != $this->input('email')) {
            $rules['email'] = [
                $rules['email'] = 'regex:/(.+)@(.+)\.(.+)/i',
                $rules['email'] = 'unique:investor,email'
            ];
        }

        return $rules;
    }
}
