<?php

namespace Modules\Profile\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class ForgottenPasswordRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => $this->getConfiguration('requestRules.email'),
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('auth.emailNotFound')
        ];
    }
}
