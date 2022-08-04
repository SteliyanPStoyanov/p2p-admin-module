<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Modules\Core\Http\Requests\BaseRequest;

class VerifyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'day' => 'required|numeric|between:1,31',
            'month' => 'required|numeric|between:1,12',
            'year' => 'required|integer|min:1920|digits:4',
            'birth_date' => 'required|date',
            'citizenship' => 'required|numeric|exists:country,country_id',
            'residence' => 'required|numeric|exists:country,country_id',
            'address' => 'required|string|min:5|max:100',
            'city' => 'required|string|min:3|max:30',
            'postcode' => 'nullable|regex:/^[a-zA-Z0-9_\-\_\;\(\)\*\# ]*$/|max:20',
            'political' => 'required|numeric|in:0,1',
        ];
    }

    protected function prepareForValidation()
    {
        $this->request->set(
            'birth_date',
            $this->request->get('year') . '-' . $this->request->get('month') . '-' . $this->request->get('day')
        );
    }

    protected function failedValidation(Validator $validator)
    {
        return back()->withErrors($validator->errors());
    }

    public function messages()
    {
        return [
            'day.required' => __('common.Required'),
            'month.required' => __('common.Required'),
            'year.required' => __('common.Required'),
            'citizenship.required' => __('common.Required'),
            'citizenship.numeric' => __('common.Required'),
            'residence.required' => __('common.Required'),
            'residence.numeric' => __('common.Required'),
            'address.required' => __('common.Required'),
            'city.required' => __('common.Required'),
            'political.required' => __('common.Required'),
        ];
    }
}
