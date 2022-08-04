<?php

namespace Modules\Profile\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Modules\Core\Http\Requests\BaseRequest;

class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required',
            'email' => $this->getConfiguration('requestRules.email'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw ValidationException::withMessages(['password' => __('auth.invalidEmailOrPassword')]);
    }
}
