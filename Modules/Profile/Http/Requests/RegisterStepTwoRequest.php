<?php

namespace Modules\Profile\Http\Requests;

use Modules\Common\Entities\Investor;
use Modules\Core\Http\Requests\BaseRequest;
use Illuminate\Http\Request;

class RegisterStepTwoRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @param Request $request
     * @return array
     */
    public function rules(Request $request): array
    {
        $pasword = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[\!\@\#\$\%\^\&\*\(\)\_\+\-\=\[\]\{\}\;\'\\:\"\,\.\/\<\>\?\`\~]).{6,}$/";

        $rules = [
            'first_name' => 'required|min:2|max:40',
            'last_name' => 'required|min:2|max:40',
            'type' => 'required|min:2|max:40',
            'password' => 'required|string|min:6|regex:' . $pasword,
            'agreement' => 'accepted',
            'marketing' => 'nullable',
        ];

        if ($request->type == Investor::TYPE_COMPANY) {
            $rules['company_name'] = 'required|min:2|max:40';
            $rules['company_number'] = 'required|numeric';
        }

        return $rules;
    }
}
